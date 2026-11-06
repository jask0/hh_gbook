<?php

require_once __DIR__ . '/functions.php';

class DB
{
    private mysqli $conn;
    private string $posts;
    private string $config;

    public function __construct(?array $settings = null)
    {
        $settings ??= read_local_config();
        $prefix = field($settings, 'table');
        if (!valid_table_prefix($prefix)) {
            throw new InvalidArgumentException('Invalid table prefix.');
        }
        $this->conn = db_connect($settings);
        $this->posts = '`' . $prefix . 'posts`';
        $this->config = '`' . $prefix . 'config`';
    }

    private function query(string $sql, array $params = []): mysqli_result|bool
    {
        return $this->conn->execute_query($sql, $params);
    }

    private function config(int $category): array
    {
        $rows = $this->query("SELECT variable, value FROM {$this->config} WHERE meta = ?", [$category])->fetch_all(MYSQLI_ASSOC);
        return array_column($rows, 'value', 'variable');
    }

    public function getSystemConfig(): array { return $this->config(0); }
    public function getUserConfig(): array { return $this->config(1); }
    public function getGbConfig(): array { return $this->config(2); }

    public function updateConfigTable(array $data): bool
    {
        $this->conn->begin_transaction();
        try {
            foreach ($data as $key => $value) {
                $this->query("UPDATE {$this->config} SET value = ? WHERE variable = ?", [(string) $value, $key]);
            }
            $this->conn->commit();
            return true;
        } catch (Throwable $error) {
            $this->conn->rollback();
            throw $error;
        }
    }

    public function countPosts(?int $public = null): int
    {
        $where = $public === null ? '' : ' WHERE public = ?';
        return (int) $this->query("SELECT COUNT(*) FROM {$this->posts}$where", $public === null ? [] : [$public])->fetch_row()[0];
    }

    public function getCountOfUnpablicPosts(): int
    {
        return $this->countPosts(0);
    }

    public function readPost(int $id): ?array
    {
        return $this->query("SELECT * FROM {$this->posts} WHERE id = ?", [$id])->fetch_assoc();
    }

    public function readPosts(int $public = 1): array
    {
        return $this->query("SELECT * FROM {$this->posts} WHERE public = ? ORDER BY datum DESC, id DESC", [$public])->fetch_all(MYSQLI_ASSOC);
    }

    public function readPostsForPage(int $limit, int $offset, bool $isAdmin, ?int $public = null): array
    {
        $public = $isAdmin ? $public : 1;
        $where = $public === null ? '' : ' WHERE public = ?';
        $params = $public === null ? [$limit, $offset] : [$public, $limit, $offset];
        return $this->query("SELECT * FROM {$this->posts}$where ORDER BY datum DESC, id DESC LIMIT ? OFFSET ?", $params)->fetch_all(MYSQLI_ASSOC);
    }

    private function postValues(array $post): array
    {
        return [$post['name'], $post['email'], $post['homepage'], $post['betreff'], $post['bild_url'],
            $post['nachricht'], $post['kommentar'], $post['public']];
    }

    public function insertPost(array $post): bool
    {
        return $this->query(
            "INSERT INTO {$this->posts} (name, email, homepage, betreff, bild_url, nachricht, kommentar, public, gb)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)",
            $this->postValues($post)
        );
    }

    public function updatePost(int $id, array $post): bool
    {
        return $this->query(
            "UPDATE {$this->posts} SET name = ?, email = ?, homepage = ?, betreff = ?, bild_url = ?,
             nachricht = ?, kommentar = ?, public = ?, datum = datum WHERE id = ?",
            [...$this->postValues($post), $id]
        );
    }

    public function deletePost(int $id): bool
    {
        $this->query("DELETE FROM {$this->posts} WHERE id = ?", [$id]);
        return $this->conn->affected_rows === 1;
    }
}
