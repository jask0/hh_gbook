<?php
declare(strict_types=1);

function save_admin_page(string $page, DB $db, array $user, ?array $entry): string
{
    $notice = 'settings_successful_saved';
    $destination = '?page=' . $page;

    switch ($page) {
        case 'edit':
            $id = (int) $entry['id'];
            switch (field($_POST, 'action')) {
                case 'delete':
                    if (!$db->deletePost($id)) {
                        fail(404, 'Message not found.');
                    }
                    $notice = 'msg_successful_deleted';
                    $destination = '?page=index';
                    break;
                case 'save':
                    try {
                        $post = post_fields($_POST, true);
                    } catch (InvalidArgumentException $error) {
                        throw new InvalidArgumentException('Check this field: ' . $error->getMessage());
                    }
                    $db->updatePost($id, $post);
                    $notice = 'msg_successful_edited';
                    $destination .= '&id=' . $id;
                    break;
                default:
                    fail(400, 'Unknown action.');
            }
            break;

        case 'settings':
            $title = trim(field($_POST, 'gb_title'));
            $limit = filter_var(field($_POST, 'posts_on_page'), FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 100]]);
            if ($limit === false || !preg_match('/\A[^\p{C}]{1,100}\z/u', $title)) {
                throw new InvalidArgumentException('Enter a title of up to 100 characters and 1 to 100 messages per page.');
            }
            $settings = [
                'gb_title' => $title,
                'posts_on_page' => $limit,
                'gb_language' => admin_language(field($_POST, 'gb_language')),
            ];
            foreach (array_keys(admin_flags()) as $flag) {
                $settings[$flag] = field($_POST, $flag) === '1' ? '1' : '0';
            }
            save_admin_settings($db, $settings);
            break;

        case 'profile':
            $username = trim(field($_POST, 'username'));
            $email = trim(field($_POST, 'email'));
            $password = field($_POST, 'password');
            $notify = field($_POST, 'mail_msg') === '1';
            $usernameChanged = $username !== $user['user'];
            if ($usernameChanged && !preg_match("/\A[\p{L}\p{N}][\p{L}\p{N}._' -]{1,63}\z/u", $username)) {
                throw new InvalidArgumentException('Choose a username of 2 to 64 characters starting with a letter or number.');
            }
            if (($email !== '' || $notify) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Enter a valid email address.');
            }
            if ($usernameChanged || $password !== '') {
                $currentPassword = field($_POST, 'current_password');
                if (strlen($currentPassword) > 72 || str_contains($currentPassword, "\0")
                    || !password_verify($currentPassword, $user['password'])) {
                    throw new InvalidArgumentException('Enter your current password to change your username or password.');
                }
            }
            if ($password !== '' && (strlen($password) < 12 || strlen($password) > 72 || str_contains($password, "\0")
                || $password !== field($_POST, 'password_confirmation'))) {
                throw new InvalidArgumentException('Enter a new password of 12 to 72 bytes and confirm it.');
            }
            $settings = [
                'user' => $username,
                'email' => $email,
                'mail_msg' => $notify ? '1' : '0',
                'user_language' => admin_language(field($_POST, 'user_language')),
            ];
            if ($password !== '') {
                $settings['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            save_admin_settings($db, $settings);
            if ($usernameChanged || $password !== '') {
                if (!session_regenerate_id(true)) {
                    session_destroy();
                    fail(500, 'Could not renew the admin session. Log in again.');
                }
                $_SESSION = ['admin' => true, 'admin_credential' => admin_credential($settings + $user)];
            }
            break;

        case 'custom':
            $colors = [];
            foreach (admin_colors() as $key => [$selector, $property]) {
                $color = field($_POST, $key);
                if (!preg_match('/\A#[0-9a-f]{6}\z/i', $color)) {
                    throw new InvalidArgumentException('Choose a valid color.');
                }
                $colors[$selector][$property] = substr($color, 1);
            }
            $colors['.custom-thumbnail']['background-color'] = $colors['.custom-thumbnail']['border-color'];
            $settings = [
                'use_custom_css' => field($_POST, 'use_custom_css') === '1' ? '1' : '0',
                'custom_css' => json_encode($colors, JSON_THROW_ON_ERROR),
            ];
            foreach (['send_btn_color', 'link_btn_color'] as $key) {
                $button = field($_POST, $key);
                if (!in_array($button, admin_buttons(), true)) {
                    throw new InvalidArgumentException('Choose a valid button style.');
                }
                $settings[$key] = $button;
            }
            save_admin_settings($db, $settings);
            break;

        case 'smilies':
            $folder = field($_POST, 'folder');
            $sets = admin_smilie_sets();
            if (!array_key_exists($folder, $sets)) {
                throw new InvalidArgumentException('Choose an installed smiley set.');
            }
            save_admin_settings($db, [
                'smilies_on' => field($_POST, 'smilies_on') === '1' ? '1' : '0',
                'smilies' => json_encode(['folder' => $folder, 'list' => $sets[$folder]], JSON_THROW_ON_ERROR),
            ]);
            break;

        default:
            fail(400, 'Unknown action.');
    }

    $_SESSION['admin_notice'] = $notice;
    return $destination;
}

function save_admin_settings(DB $db, array $settings): void
{
    if (!$db->updateConfigTable($settings)) {
        fail(500, 'Could not save settings.');
    }
}

function admin_language(string $language): string
{
    if (!in_array($language, LANGUAGES, true)) {
        throw new InvalidArgumentException('Choose a supported language.');
    }
    return $language;
}

function admin_flags(): array
{
    return [
        'view_email_input' => 'email',
        'view_hp_input' => 'homepage',
        'view_img_url_input' => 'image',
        'view_subject_input' => 'subject',
        'scale_image' => 'scale_image',
        'post_instant_online' => 'post_msgs_immediately',
    ];
}

function admin_colors(): array
{
    return [
        'border' => ['.custom-panel', 'border-color', 'border_color'],
        'header_bg' => ['.custom-heading', 'background-color', 'header_bg'],
        'header_text' => ['.custom-heading', 'color', 'header_txt_color'],
        'body_bg' => ['.custom-body', 'background-color', 'text_bg'],
        'body_text' => ['.custom-body', 'color', 'body_txt_color'],
        'footer_bg' => ['.custom-footer', 'background-color', 'comment_bg'],
        'footer_text' => ['.custom-footer', 'color', 'comment_txt_color'],
        'thumb_border' => ['.custom-thumbnail', 'border-color', 'thumb_border_color'],
    ];
}

function admin_buttons(): array
{
    return ['btn-default', 'btn-primary', 'btn-success', 'btn-info', 'btn-warning', 'btn-danger', 'btn-link'];
}

function admin_smilie_sets(): array
{
    $sets = [];
    foreach (glob(__DIR__ . '/../smilies/*', GLOB_ONLYDIR) ?: [] as $directory) {
        $name = basename($directory);
        if (is_link($directory) || !preg_match('/\A[a-z0-9_-]+\z/i', $name)) {
            continue;
        }
        $files = smiley_files($name);
        if ($files !== []) {
            $sets[$name] = $files;
        }
    }
    return $sets;
}
