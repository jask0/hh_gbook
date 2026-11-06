<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/conf/functions.php';

function install_input(string $key, string $label, ?string $default = null, bool $secret = false): string
{
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    if (!stream_isatty(STDIN)) {
        return $default ?? throw new RuntimeException("Set $key for non-interactive setup.");
    }

    $hint = $default === null || $secret ? '' : " [$default]";
    fwrite(STDERR, "$label$hint: ");
    if ($secret) {
        if (!function_exists('exec')) {
            throw new RuntimeException("Set $key to enter the password without echo.");
        }
        exec('stty -echo 2>/dev/null', $output, $status);
        if ($status !== 0) {
            throw new RuntimeException("Set $key to enter the password without echo.");
        }
    }
    try {
        $line = fgets(STDIN);
    } finally {
        if ($secret) {
            exec('stty echo 2>/dev/null');
            fwrite(STDERR, PHP_EOL);
        }
    }
    if ($line === false) {
        throw new RuntimeException('Setup input ended.');
    }
    $value = rtrim($line, "\r\n");
    return $value === '' && $default !== null ? $default : $value;
}

function install_db_settings(): array
{
    $settings = [
        'servername' => install_input('HHGB_DB_HOST', 'Database host', 'localhost'),
        'username' => install_input('HHGB_DB_USER', 'Database user'),
        'password' => install_input('HHGB_DB_PASSWORD', 'Database password', null, true),
        'dbname' => install_input('HHGB_DB_NAME', 'Database name'),
        'table' => install_input('HHGB_DB_PREFIX', 'Existing or new table prefix', 'hhgb_'),
    ];
    $port = getenv('HHGB_DB_PORT');
    if ($port !== false && $port !== '') {
        $settings['port'] = filter_var($port, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
        if ($settings['port'] === false) {
            throw new RuntimeException('HHGB_DB_PORT must be a port from 1 to 65535.');
        }
    }
    $socket = getenv('HHGB_DB_SOCKET');
    if ($socket !== false && $socket !== '') {
        $settings['socket'] = $socket;
    }
    if (!valid_table_prefix($settings['table'])) {
        throw new RuntimeException('The table prefix must contain 1–48 ASCII letters, digits or underscores.');
    }
    if ($settings['username'] === '' || $settings['dbname'] === '') {
        throw new RuntimeException('A database user and database name are required.');
    }
    return $settings;
}

function install_password_hash(): string
{
    $password = install_input('HHGB_ADMIN_PASSWORD', 'New admin password (12–72 bytes)', null, true);
    if (strlen($password) < 12 || strlen($password) > 72 || str_contains($password, "\0")) {
        throw new RuntimeException('Choose an admin password between 12 and 72 bytes without NUL characters.');
    }
    if (getenv('HHGB_ADMIN_PASSWORD') === false &&
        $password !== install_input('HHGB_ADMIN_PASSWORD', 'Repeat the admin password', null, true)) {
        throw new RuntimeException('The passwords do not match.');
    }
    return password_hash($password, PASSWORD_DEFAULT);
}

function install_stage_config(string $path, array $settings): string
{
    $temp = dirname($path) . '/.install-' . bin2hex(random_bytes(12)) . '.php';
    $file = fopen($temp, 'xb');
    if ($file === false) {
        throw new RuntimeException('Could not create a temporary configuration file.');
    }

    $contents = "<?php\nreturn " . var_export($settings, true) . ";\n";
    try {
        if (fwrite($file, $contents) !== strlen($contents) || !fflush($file)) {
            throw new RuntimeException('Could not write the configuration.');
        }
    } catch (Throwable $error) {
        unlink($temp);
        throw $error;
    } finally {
        fclose($file);
    }
    return $temp;
}

function install_defaults(string $admin, string $hash): array
{
    return [
        0 => ['captcha_on' => '1', 'schema_version' => '1.6'],
        1 => [
            'user' => $admin, 'password' => $hash, 'email' => '', 'mail_msg' => '0',
            'user_language' => 'de',
        ],
        2 => [
            'gb_title' => 'MyGB', 'info' => '', 'note' => '', 'gb_language' => 'de',
            'view_email_input' => '1', 'view_hp_input' => '1', 'view_img_url_input' => '1',
            'view_subject_input' => '1', 'posts_on_page' => '5', 'scale_image' => '1',
            'post_instant_online' => '0', 'send_btn_color' => 'btn-success',
            'link_btn_color' => 'btn-default', 'highlite_field_color' => 'has-success',
            'note_text_color' => 'text-success', 'use_custom_css' => '0',
            'custom_css' => json_encode([
                '.custom-panel' => ['border-color' => 'A7093E'],
                '.custom-heading' => ['background-color' => '650213', 'color' => 'FFB027'],
                '.custom-body' => ['background-color' => '840A12', 'color' => 'EAD72D'],
                '.custom-footer' => ['background-color' => '81060A', 'color' => 'B1A714'],
                '.custom-thumbnail' => ['background-color' => 'EC2438', 'border-color' => 'EC2438'],
            ], JSON_THROW_ON_ERROR),
            'smilies_on' => '1',
            'smilies' => '{"folder":"yellow","ext":"gif"}',
        ],
    ];
}

function install_create(mysqli $db, string $prefix, string $admin, string $hash, array &$created): void
{
    $posts = $prefix . 'posts';
    $config = $prefix . 'config';
    $existing = $db->execute_query(
        'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN (?, ?)',
        [$posts, $config]
    )->fetch_row();
    if ((int) $existing[0] !== 0) {
        throw new RuntimeException('Guestbook tables already exist. Use upgrade for a 1.5 database or choose a new prefix.');
    }

    $db->query("CREATE TABLE `$posts` (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        datum TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        name VARCHAR(50) NOT NULL,
        email VARCHAR(254),
        homepage VARCHAR(2048),
        betreff VARCHAR(100),
        bild_url VARCHAR(2048),
        nachricht TEXT NOT NULL,
        kommentar TEXT,
        public TINYINT(1) NOT NULL DEFAULT 0,
        gb TINYINT(1) NOT NULL DEFAULT 1
    ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $created[] = $posts;
    $db->query("CREATE TABLE `$config` (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        variable VARCHAR(100) NOT NULL UNIQUE,
        value TEXT,
        meta TINYINT NOT NULL
    ) ENGINE=InnoDB ROW_FORMAT=DYNAMIC DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $created[] = $config;

    $db->begin_transaction();
    try {
        $insert = $db->prepare("INSERT INTO `$config` (variable, value, meta) VALUES (?, ?, ?)");
        foreach (install_defaults($admin, $hash) as $category => $settings) {
            foreach ($settings as $key => $value) {
                $insert->execute([$key, $value, $category]);
            }
        }
        $db->commit();
    } catch (Throwable $error) {
        $db->rollback();
        throw $error;
    }
}

function install_check_account(mysqli $db, string $config, bool $lock = false): void
{
    $forUpdate = $lock ? ' FOR UPDATE' : '';
    $account = $db->query("SELECT variable FROM `$config` WHERE meta = 1 AND variable IN ('user', 'password')$forUpdate");
    if ($account->num_rows !== 2) {
        throw new RuntimeException('The existing guestbook database does not contain a complete admin account.');
    }
}

function install_migration_done(mysqli $db, string $config): bool
{
    $version = $db->query("SELECT value FROM `$config` WHERE variable = 'schema_version'")->fetch_row();
    if ($version === null) {
        return false;
    }
    if ($version[0] !== '1.6') {
        throw new RuntimeException('The existing database has an unrecognized schema version.');
    }
    return true;
}

function install_upgrade(mysqli $db, string $prefix, string $hash): void
{
    $posts = $prefix . 'posts';
    $config = $prefix . 'config';
    install_check_account($db, $config);
    if (!install_migration_done($db, $config)) {
        // DDL commits independently. Both alterations are safe to repeat after an interrupted upgrade.
        $db->query("ALTER TABLE `$posts`
            ENGINE=InnoDB, ROW_FORMAT=DYNAMIC, DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            MODIFY datum TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            MODIFY name VARCHAR(50) NOT NULL,
            MODIFY email VARCHAR(254),
            MODIFY homepage VARCHAR(2048),
            MODIFY betreff VARCHAR(100),
            MODIFY bild_url VARCHAR(2048),
            MODIFY nachricht TEXT NOT NULL,
            MODIFY kommentar TEXT");
        $db->query("ALTER TABLE `$config`
            ENGINE=InnoDB, ROW_FORMAT=DYNAMIC, DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            MODIFY variable VARCHAR(100) NOT NULL,
            MODIFY value TEXT");
    }

    $db->begin_transaction();
    try {
        install_check_account($db, $config, true);
        if (!install_migration_done($db, $config)) {
            $read = $db->prepare("SELECT id, name, email, homepage, betreff, bild_url, nachricht, kommentar
                FROM `$posts` WHERE id > ? ORDER BY id LIMIT 200");
            $update = $db->prepare("UPDATE `$posts` SET
                name = ?, email = ?, homepage = ?, betreff = ?, bild_url = ?, nachricht = ?, kommentar = ? WHERE id = ?");
            $lastId = 0;
            do {
                $read->execute([$lastId]);
                $rows = $read->get_result()->fetch_all(MYSQLI_ASSOC);
                foreach ($rows as $row) {
                    $lastId = (int) $row['id'];
                    unset($row['id']);
                    $decoded = array_map(
                        static fn (?string $value): ?string => $value === null
                            ? null
                            : html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                        $row
                    );
                    if ($decoded !== $row) {
                        $update->execute([...array_values($decoded), $lastId]);
                    }
                }
            } while (count($rows) === 200);

            $db->query("INSERT INTO `$config` (variable, value, meta) VALUES ('schema_version', '1.6', 0)");
        }
        install_reset_password($db, $config, $hash);
        $db->commit();
    } catch (Throwable $error) {
        $db->rollback();
        throw $error;
    }
}

function install_reset_password(mysqli $db, string $config, string $hash): void
{
    $db->execute_query("UPDATE `$config` SET value = ? WHERE variable = 'password' AND meta = 1", [$hash]);
    if ($db->affected_rows !== 1) {
        throw new RuntimeException('Could not replace the admin password.');
    }
}

$action = $argv[1] ?? '--help';
if ($action === '--help' || $action === '-h') {
    echo "Usage: php hhgb/install.php install|upgrade|reset-password\n";
    exit;
}
if ($argc !== 2 || !in_array($action, ['install', 'upgrade', 'reset-password'], true)) {
    fwrite(STDERR, "Usage: php hhgb/install.php install|upgrade|reset-password\n");
    exit(1);
}
if (PHP_VERSION_ID < 80400 || !extension_loaded('mysqli')) {
    fwrite(STDERR, "PHP 8.4 or later and the MySQLi extension are required.\n");
    exit(1);
}

umask(0077);
$path = local_config_path();
$created = [];
$temp = null;
$status = 0;
try {
    $lock = fopen(dirname($path) . '/.install.lock', 'c');
    if ($lock === false || !flock($lock, LOCK_EX | LOCK_NB)) {
        throw new RuntimeException('Could not lock setup. Check the configuration directory or wait for the current setup to finish.');
    }

    if ($action === 'reset-password') {
        if (!configured()) {
            throw new RuntimeException('No local configuration exists. Complete install or upgrade first.');
        }
        $settings = read_local_config();
        $db = db_connect($settings);
        $config = $settings['table'] . 'config';
        install_check_account($db, $config);
        install_reset_password($db, $config, install_password_hash());
        echo "The admin password was reset.\n";
    } else {
        if (file_exists($path) || is_link($path)) {
            throw new RuntimeException('A local configuration already exists. Use reset-password to recover admin access.');
        }
        $settings = install_db_settings();
        $hash = install_password_hash();
        $db = db_connect($settings);
        $temp = install_stage_config($path, $settings);

        if ($action === 'install') {
            $admin = trim(install_input('HHGB_ADMIN_USER', 'Admin username'));
            if (!preg_match('/\A[^\p{C}]{2,60}\z/u', $admin)) {
                throw new RuntimeException('The admin username must contain 2–60 characters without control characters.');
            }
            install_create($db, $settings['table'], $admin, $hash, $created);
        } else {
            install_upgrade($db, $settings['table'], $hash);
        }
        // Publish only the complete config, and never replace an existing file.
        if (!link($temp, $path)) {
            throw new RuntimeException('Could not publish the local configuration.');
        }
        echo "hh_gbook " . HHGB_VERSION . " setup completed. Open hhgb/ to sign in.\n";
    }
} catch (Throwable $error) {
    if (isset($db)) {
        foreach (array_reverse($created) as $table) {
            try {
                $db->query("DROP TABLE `$table`");
            } catch (mysqli_sql_exception $cleanupError) {
                fwrite(STDERR, "Could not clean up the newly created table $table.\n");
            }
        }
    }
    fwrite(STDERR, 'Setup failed: ' . $error->getMessage() . PHP_EOL);
    $status = 1;
} finally {
    if ($temp !== null) {
        unlink($temp);
    }
    if (isset($lock) && is_resource($lock)) {
        fclose($lock);
    }
}
exit($status);
