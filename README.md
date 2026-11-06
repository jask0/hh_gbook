# hh_gbook 1.6

A small PHP and MySQL guestbook for an existing website. Visitors can leave a message, and the owner can review, publish, edit, or delete it.

Thanks to the members of the HomepageHelfer forum who helped build it.

## Requirements

- PHP 8.4 or 8.5 with MySQLi (mysqlnd).
- A MySQL or MariaDB database with InnoDB and utf8mb4 support.
- GD with JPEG, PNG, and FreeType support for the image CAPTCHA.
- PHP command line access for setup, and HTTPS for the public site and admin login.

## Fresh install

1. Copy the project to your PHP host and create an empty database and a database user. The user needs permission to create tables in that database during setup.
2. From the project directory, run:

   `sh
   php hhgb/install.php install
   `

3. Enter your database details and choose an admin username and a password of 12 to 72 bytes. The installer creates the tables and writes the local database configuration to `hhgb/conf/local.php`.
4. Open `hhgb/` in the browser to sign in, and open the project root to see the guestbook.

Setup is available only from the command line. On shared hosting, run it from the provider's SSH or command line panel. The setup account must be able to write to `hhgb/conf/`. The file is created with mode `0600`. If PHP runs under a different account, assign the file to a dedicated group that includes that account and grant group read access with mode `0640`. Keep the directory writable only by the account that maintains the deployment. Keep the local file out of version control and any downloadable backup.

After setup, the app's database account needs SELECT, INSERT, UPDATE, and DELETE on the guestbook tables. For normal use it can run without schema privileges.

For non-interactive setup, set `HHGB_DB_USER`, `HHGB_DB_PASSWORD`, `HHGB_DB_NAME`, `HHGB_ADMIN_USER`, and `HHGB_ADMIN_PASSWORD`. Optional settings are `HHGB_DB_HOST` (default `localhost`), `HHGB_DB_PREFIX` (default `hhgb_`), `HHGB_DB_PORT`, and `HHGB_DB_SOCKET`. Supply passwords through your deployment's secret settings or the interactive prompts.

## Upgrade from 1.5

1. Take the old guestbook offline, back up both guestbook tables, and make a private copy of the old `hhgb/conf/config.php` for reference.
2. Install the 1.6 files and run:

   `sh
   php hhgb/install.php upgrade
   `

3. Enter the existing database details and table prefix from your private copy, then choose a new admin password. The existing username, settings, posts, dates, and moderation status are carried over. The database user needs ALTER permission for this step. For unattended upgrades, set the same environment variables as for setup. The upgrade uses the existing username, so `HHGB_ADMIN_USER` is optional.
4. Sign in with the new password, review a few old entries, and remove the old database configuration from the hosted copy. Version 1.6 does not read the old file or track a replacement in Git.

The upgrade converts both tables to utf8mb4. It decodes HTML entities in old post fields once so the new templates can escape the text consistently when displaying it. Some old posts edited by an administrator may have mixed escaped and literal text. Check entries that deliberately used entity syntax and keep the database backup until you have verified their display. Guestbook links and images use HTTP or HTTPS. The renderer supports basic BBCode, and nested formatting may display as literal tags.

If setup is interrupted during an upgrade, run the upgrade command again with the same database and prefix. The migration records completion in the database so entries are decoded only once. To recover access to an existing 1.6 install, run `php hhgb/install.php reset-password`.

## Embed the guestbook in an existing page

The project root contains a standalone example. To embed the guestbook from a page one directory deeper than the directory containing `hhgb/`, start with this example and adapt the two paths for your site:

```php
<?php
require_once __DIR__ . '/../hhgb/gb.php';
start_session();
$gb = new GB();
$gb->setPath('../');
$action = basename(__FILE__);
$gb->handleSubmission($action);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Guestbook</title>
    <?php $gb->includeMeta(); ?>
</head>
<body>
    <main class="hhgb">
        <h1><?= e($gb->getGbSettings()['gb_title']) ?></h1>
        <?php
        $gb->showGBookForm($action);
        $gb->showGBookPosts();
        $gb->showGBpageNavi();
        ?>
    </main>
</body>
</html>
```

Run the PHP setup and submission handler before output. The guestbook uses its own session cookie. If your site already opened a PHP session, call `session_write_close()` before starting the guestbook session. On a standalone page, you can call `handle_errors()` before setup to log errors and show visitors a generic message.

## Release acceptance checks

- Fresh setup creates a working guestbook with owner chosen credentials. Requests to the web installer cannot configure it, and a second CLI install cannot overwrite it.
- Upgrading a copy of a 1.5 database keeps post IDs, dates, publication status, settings, and the existing admin username. A new password works, and quoted text, BBCode, smileys, and new Unicode posts display safely.
- Logged-out requests cannot read admin views or modify data. Invalid routes and missing or incorrect CSRF tokens are rejected. Login changes the session ID, and logout ends the admin session.
- A correct CAPTCHA can submit a new pending post. Missing, wrong, and replayed answers cannot submit a post. An admin can publish, edit, and delete it.
- HTML, unsafe URLs, malformed request fields, and SQL punctuation are handled safely. Literal text remains readable.
- PHP lint and the main install, login, submission, and moderation flows pass on PHP 8.4 and 8.5. The guestbook works from the site root and a subdirectory.
