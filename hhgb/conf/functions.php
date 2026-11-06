<?php

const HHGB_VERSION = '1.6';
const LANGUAGES = ['en', 'de', 'ba'];

function local_config_path(): string
{
    return __DIR__ . '/local.php';
}

function configured(): bool
{
    return is_file(local_config_path());
}

function valid_table_prefix(string $prefix): bool
{
    return preg_match('/\A[A-Za-z0-9_]{1,48}\z/', $prefix) === 1;
}

function read_local_config(): array
{
    $config = require local_config_path();
    if (!is_array($config) || !valid_table_prefix(field($config, 'table'))) {
        throw new RuntimeException('Invalid guestbook database configuration.');
    }
    return $config;
}

function db_connect(array $config): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli(
        $config['servername'], $config['username'], $config['password'], $config['dbname'],
        $config['port'] ?? null, $config['socket'] ?? null
    );
    $db->set_charset('utf8mb4');
    return $db;
}

function handle_errors(): void
{
    ini_set('display_errors', '0');
    set_exception_handler(static function (Throwable $error): void {
        error_log((string) $error);
        fail(500, 'The guestbook could not complete the request.');
    });
}

function fail(int $status, string $message): never
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $message;
    exit;
}

function redirect(string $url, int $status = 303): never
{
    header('Location: ' . $url, true, $status);
    exit;
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        if (session_name() !== 'HHGBSESSID') {
            throw new LogicException('Close the website session before starting the guestbook session.');
        }
        return;
    }
    session_name('HHGBSESSID');
    $https = strtolower(field($_SERVER, 'HTTPS'));
    if (!session_start([
            'use_strict_mode' => 1,
            'use_only_cookies' => 1,
            'cookie_httponly' => 1,
            'cookie_secure' => $https !== '' && $https !== 'off' && $https !== '0',
            'cookie_samesite' => 'Lax',
        ])) {
        throw new RuntimeException('Could not start the guestbook session.');
    }
}

function admin_credential(array $user): string
{
    $name = field($user, 'user');
    $hash = field($user, 'password');
    return $name !== '' && $hash !== '' ? hash('sha256', $name . "\0" . $hash) : '';
}

function csrf_token(): string
{
    return $_SESSION['hhgb_csrf'] ??= bin2hex(random_bytes(32));
}

function require_csrf(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        fail(405, 'This action requires POST.');
    }
    $token = $_SESSION['hhgb_csrf'] ?? '';
    if (!is_string($token) || $token === '' || !hash_equals($token, field($_POST, 'csrf_token'))) {
        fail(403, 'The form expired. Open the page again and retry.');
    }
}

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function field(array $data, string $key, string $default = ''): string
{
    return is_string($data[$key] ?? null) ? $data[$key] : $default;
}

function lang(string $language): array
{
    require __DIR__ . '/lang/en.php';
    $english = $l;
    if ($language !== 'en' && in_array($language, LANGUAGES, true)) {
        require __DIR__ . '/lang/' . $language . '.php';
    }
    return array_map(static fn (string $text): string => html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $l + $english + [
        'previous_page' => 'Previous', 'next_page' => 'Next',
    ]);
}

function safe_url(string $value): string
{
    if (strlen($value) > 2048 || !filter_var($value, FILTER_VALIDATE_URL)) {
        return '';
    }
    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true) ? $value : '';
}

function positive_int(string $value, int $default, int $max = PHP_INT_MAX): int
{
    $number = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => $max]]);
    return $number === false ? $default : $number;
}

function empty_post(): array
{
    return array_fill_keys(['name', 'email', 'homepage', 'betreff', 'bild_url', 'nachricht', 'kommentar'], '');
}

function post_fields(array $input, bool $admin = false): array
{
    $limits = ['name' => 50, 'email' => 254, 'homepage' => 2048, 'betreff' => 100,
        'bild_url' => 2048, 'nachricht' => 10000, 'kommentar' => 10000];
    $post = empty_post();
    foreach ($limits as $key => $limit) {
        if ($key === 'kommentar' && !$admin) {
            continue;
        }
        if (array_key_exists($key, $input) && !is_string($input[$key])) {
            throw new InvalidArgumentException($key);
        }
        $value = trim(field($input, $key));
        if (strlen($value) > $limit * 4 || !preg_match('/\A.{0,' . $limit . '}\z/su', $value)
            || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) {
            throw new InvalidArgumentException($key);
        }
        $post[$key] = $value;
    }
    foreach (['name', 'nachricht'] as $key) {
        if ($post[$key] === '') {
            throw new InvalidArgumentException($key);
        }
    }
    if ($post['email'] !== '' && !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('email');
    }
    foreach (['homepage', 'bild_url'] as $key) {
        if ($post[$key] !== '' && safe_url($post[$key]) === '') {
            throw new InvalidArgumentException($key);
        }
    }
    $post['public'] = (int) ($admin && field($input, 'public') === '1');
    return $post;
}

/** Only image files already bundled in a local smiley set can be rendered. */
function smiley_files(string $folder): array
{
    if (!preg_match('/\A[A-Za-z0-9_-]+\z/', $folder)) {
        return [];
    }
    $dir = dirname(__DIR__) . '/smilies/' . $folder;
    if (is_link($dir)) {
        return [];
    }
    $files = [];
    foreach (glob($dir . '/*') ?: [] as $file) {
        $name = basename($file);
        if (is_file($file) && !is_link($file) && preg_match('/\A[A-Za-z0-9_().-]+\.(?:gif|png|jpe?g)\z/i', $name)) {
            $files[] = $name;
        }
    }
    return $files;
}

/** Escape visitor text first, then add only the markup this renderer owns. */
function bbCodeToHtml(string $text, array $smilies = [], string $smileyBase = '', bool $scale = true): string
{
    $pattern = '~\[(b|i|u|quote)\](.*?)\[/\1\]'
        . '|\[(url|img|smillie)\](.*?)\[/\3\]'
        . '|\[(url|size|color)=([^\]\r\n]+)\](.*?)\[/\5\]'
        . '|\{\{([A-Za-z0-9_().-]+)\}\}~is';
    $render = static function (array $match) use ($smilies, $smileyBase, $scale): string {
        $tag = strtolower(($match[1] ?? '') ?: ($match[3] ?? '') ?: ($match[5] ?? ''));
        $text = ($match[2] ?? '') !== '' ? $match[2] : ($match[4] ?? '');
        $argument = $match[6] ?? '';
        $label = $match[7] ?? '';
        if ($tag === 'url') {
            $url = safe_url($argument !== '' ? $argument : $text);
            if ($url !== '') {
                return '<a href="' . e($url) . '" rel="nofollow ugc">' . e($argument !== '' ? $label : $text) . '</a>';
            }
        } elseif ($tag === 'img') {
            $url = safe_url($text);
            if ($url !== '') {
                return '<img src="' . e($url) . '" alt="" loading="lazy"' . ($scale ? ' class="gb-image"' : '') . '>';
            }
        } elseif ($tag === 'smillie' || isset($match[8])) {
            $parts = explode('/', str_replace('\\', '/', $text));
            $file = $match[8] ?? array_pop($parts);
            $folder = array_pop($parts);
            $local = $smilies[$folder . '/' . $file] ?? $smilies[$file] ?? null;
            if ($local !== null) {
                return '<img src="' . e($smileyBase . $local) . '" alt="' . e($file) . '">';
            }
        } elseif ($tag === 'size' && preg_match('/\A(?:[89]|[1-3][0-9]|40)\z/', $argument)) {
            return '<span style="font-size:' . $argument . 'px">' . e($label) . '</span>';
        } elseif ($tag === 'color' && preg_match('/\A#[0-9a-f]{3}(?:[0-9a-f]{3})?\z/i', $argument)) {
            return '<span style="color:' . $argument . '">' . e($label) . '</span>';
        } elseif (isset(['b' => 1, 'i' => 1, 'u' => 1, 'quote' => 1][$tag])) {
            $htmlTag = ['b' => 'strong', 'i' => 'em', 'u' => 'u', 'quote' => 'blockquote'][$tag];
            return '<' . $htmlTag . '>' . e($text) . '</' . $htmlTag . '>';
        }
        return e($match[0]);
    };
    $out = '';
    $offset = 0;
    preg_match_all($pattern, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    foreach ($matches as $match) {
        [$code, $start] = $match[0];
        $out .= e(substr($text, $offset, $start - $offset)) . $render(array_column($match, 0));
        $offset = $start + strlen($code);
    }
    return nl2br($out . e(substr($text, $offset)));
}
