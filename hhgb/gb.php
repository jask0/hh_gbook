<?php

require_once __DIR__ . '/conf/db.php';

class GB
{
    private DB $db;
    private array $gb;
    private array $user;
    private array $system;
    private array $language;
    private string $languageCode;
    private string $path = '';
    private ?array $pagination = null;

    public function __construct(?DB $db = null)
    {
        $this->db = $db ?? new DB();
        $this->gb = $this->db->getGbConfig();
        $this->user = $this->db->getUserConfig();
        $this->system = $this->db->getSystemConfig();
        $this->setLanguage(field($this->gb, 'gb_language', 'en'));
    }

    /** Base URL from the embedding page to the folder containing hhgb/. */
    public function setPath(string $path = ''): void
    {
        $this->path = $path === '' ? '' : rtrim($path, '/') . '/';
    }

    public function setLanguage(string $language = ''): void
    {
        $language = $language !== '' ? $language : field($this->gb, 'gb_language');
        $this->languageCode = in_array($language, LANGUAGES, true) ? $language : 'en';
        $this->language = lang($this->languageCode);
    }

    public function getLanguage(string $language = ''): array
    {
        return $language === '' ? $this->language : lang($language);
    }

    public function getLanguageCode(): string { return $this->languageCode; }
    public function getDB(): DB { return $this->db; }
    public function getGbSettings(): array { return $this->gb; }
    public function getUserSettings(): array { return $this->user; }

    public function isLoggedIn(): bool
    {
        $credential = admin_credential($this->user);
        return ($_SESSION['admin'] ?? false) === true && $credential !== ''
            && ($_SESSION['admin_credential'] ?? '') === $credential;
    }

    public function includeMeta(): void
    {
        $base = e($this->path . 'hhgb/conf/');
        echo '<link rel="stylesheet" href="' . $base . 'css/guestbook.css">' . "\n";
        echo '<script defer src="' . $base . 'js/guestbook.js"></script>' . "\n";
        if (field($this->gb, 'use_custom_css') !== '1') {
            return;
        }
        $settings = json_decode(field($this->gb, 'custom_css'), true);
        if (!is_array($settings)) {
            return;
        }
        $rules = [];
        foreach (['.custom-panel', '.custom-heading', '.custom-body', '.custom-footer', '.custom-thumbnail'] as $selector) {
            $values = $settings[$selector] ?? null;
            if (!is_array($values)) {
                continue;
            }
            $declarations = [];
            foreach (['background-color', 'border-color', 'color'] as $property) {
                $color = field($values, $property);
                if (preg_match('/\A[0-9a-f]{6}\z/i', $color)) {
                    $declarations[] = "$property:#$color";
                }
            }
            if ($declarations) {
                $rules[] = '.hhgb ' . $selector . '{' . implode(';', $declarations) . '}';
            }
        }
        if ($rules) {
            echo '<style>' . implode('', $rules) . '</style>' . "\n";
        }
    }

    private function captchaEnabled(): bool
    {
        return field($this->system, 'captcha_on', '1') !== '0';
    }

    /** Call before sending HTML so a successful submission can redirect. */
    public function handleSubmission(string $action): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || field($_POST, 'hhgb_action') !== 'post') {
            return;
        }
        require_csrf();
        if ($this->captchaEnabled()) {
            $challenge = $_SESSION['hhgb_captcha'] ?? null;
            unset($_SESSION['hhgb_captcha']);
            $answer = strtoupper(trim(field($_POST, 'captcha')));
            if (!is_array($challenge) || ($challenge['expires'] ?? 0) < time()
                || !hash_equals(field($challenge, 'hash'), hash('sha256', $answer))) {
                $this->formError('error_wrong_captcha', $action);
            }
        }
        $input = $_POST;
        foreach (['email' => 'email', 'homepage' => 'hp', 'bild_url' => 'img_url', 'betreff' => 'subject'] as $key => $setting) {
            if (field($this->gb, 'view_' . $setting . '_input', '1') !== '1') {
                unset($input[$key]);
            }
        }
        try {
            $post = post_fields($input);
        } catch (InvalidArgumentException) {
            $this->formError('error_msg_dont_send', $action);
        }
        $post['public'] = (int) (field($this->gb, 'post_instant_online') === '1');
        $this->db->insertPost($post);
        $email = field($this->user, 'email');
        if (field($this->user, 'mail_msg') === '1' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            mail($email, $this->language['new_gb_msg'], $post['nachricht']);
        }
        $_SESSION['hhgb_flash'] = ['key' => 'msg_successful_send', 'data' => []];
        redirect($action);
    }

    private function formError(string $key, string $action): never
    {
        $data = [];
        foreach (array_keys(empty_post()) as $field) {
            $data[$field] = substr(field($_POST, $field), 0, 40000);
        }
        $_SESSION['hhgb_flash'] = ['key' => $key, 'data' => $data];
        redirect($action);
    }

    /** The selected pack supplies the picker; installed packs can render old posts. */
    private function smilies(): array
    {
        if (field($this->gb, 'smilies_on', '1') === '0') {
            return [[], []];
        }
        $sets = [];
        foreach (glob(__DIR__ . '/smilies/*', GLOB_ONLYDIR) ?: [] as $dir) {
            if (!is_link($dir)) {
                $folder = basename($dir);
                foreach (smiley_files($folder) as $file) {
                    $sets[$folder][$file] = rawurlencode($folder) . '/' . rawurlencode($file);
                }
            }
        }
        $settings = json_decode(field($this->gb, 'smilies'), true);
        $folder = is_array($settings) ? field($settings, 'folder', 'yellow') : 'yellow';
        $selected = $sets[$folder] ?? $sets['yellow'] ?? [];
        $all = $selected;
        foreach ($sets as $folder => $files) {
            $all += $files;
            foreach ($files as $file => $url) {
                $all[$folder . '/' . $file] = $url;
            }
        }
        return [$selected, $all];
    }

    public function showGBookForm(string $action): void
    {
        $path = $this->path;
        $gbs = $this->gb;
        $l = $this->language;
        $captcha = $this->captchaEnabled();
        [$smilies] = $this->smilies();
        $flash = $_SESSION['hhgb_flash'] ?? [];
        unset($_SESSION['hhgb_flash']);
        $data = ($flash['data'] ?? []) + empty_post();
        require __DIR__ . '/conf/templates/form_template.php';
    }

    private function pagination(): array
    {
        if ($this->pagination === null) {
            $size = positive_int(field($this->gb, 'posts_on_page'), 5, 100);
            $count = $this->db->countPosts($this->isLoggedIn() ? null : 1);
            $last = max(1, (int) ceil($count / $size));
            $page = min(positive_int(field($_GET, 'page'), 1), $last);
            $this->pagination = [$page, $last, $size];
        }
        return $this->pagination;
    }

    public function showGBookPosts(): void
    {
        $admin = $this->isLoggedIn();
        [$page, , $size] = $this->pagination();
        $posts = $this->db->readPostsForPage($size, ($page - 1) * $size, $admin);
        $path = $this->path;
        $gbs = $this->gb;
        $l = $this->language;
        [, $smilies] = $this->smilies();
        foreach ($posts as $data) {
            require __DIR__ . '/conf/templates/post_template.php';
        }
    }

    public function showGBpageNavi(): void
    {
        [$page, $last] = $this->pagination();
        if ($last === 1) {
            return;
        }
        echo '<nav class="gb-pages" aria-label="Pagination">';
        if ($page > 1) {
            echo '<a href="?page=' . ($page - 1) . '" rel="prev">&laquo; ' . e($this->language['previous_page']) . '</a>';
        }
        echo '<span>' . $page . ' / ' . $last . '</span>';
        if ($page < $last) {
            echo '<a href="?page=' . ($page + 1) . '" rel="next">' . e($this->language['next_page']) . ' &raquo;</a>';
        }
        echo '</nav>';
    }
}
