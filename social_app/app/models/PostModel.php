<?php
/**
 * PostModel - data access for the `posts` table (database logic only).
 * Listing queries also return the author, like count, comment count and
 * whether the viewing user already liked the post.
 */
class PostModel extends Model
{
    private function baseSelect(): string
    {
        return 'SELECT p.id, p.user_id, p.content, p.image, p.created_at,
                       u.username, u.full_name, u.profile_image,
                       (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id)    AS like_count,
                       (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count,
                       EXISTS (SELECT 1 FROM likes lm WHERE lm.post_id = p.id AND lm.user_id = :viewer) AS liked_by_me
                  FROM posts p
            INNER JOIN users u ON u.id = p.user_id ';
    }

    /** Newsfeed: all posts, latest first. */
    public function feed(int $viewerId, int $limit, int $offset): array
    {
        return $this->fetchAll(
            $this->baseSelect() . 'ORDER BY p.created_at DESC, p.id DESC LIMIT :lim OFFSET :off',
            [':viewer' => $viewerId, ':lim' => $limit, ':off' => $offset]
        );
    }

    public function countAll(): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM posts');
    }

    public function byUser(int $userId, int $viewerId, int $limit, int $offset): array
    {
        return $this->fetchAll(
            $this->baseSelect() . 'WHERE p.user_id = :uid ORDER BY p.created_at DESC, p.id DESC LIMIT :lim OFFSET :off',
            [':viewer' => $viewerId, ':uid' => $userId, ':lim' => $limit, ':off' => $offset]
        );
    }

    public function countByUser(int $userId): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM posts WHERE user_id = :uid', [':uid' => $userId]);
    }

    /** One post with author + counters. */
    public function find(int $id, int $viewerId): ?array
    {
        return $this->fetchOne(
            $this->baseSelect() . 'WHERE p.id = :pid LIMIT 1',
            [':viewer' => $viewerId, ':pid' => $id]
        );
    }

    /** Plain row from `posts` (used for ownership checks). */
    public function findRaw(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT id, user_id, content, image, created_at FROM posts WHERE id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    public function create(int $userId, string $content, ?string $image): int
    {
        $this->query(
            'INSERT INTO posts (user_id, content, image) VALUES (:uid, :content, :image)',
            [':uid' => $userId, ':content' => $content, ':image' => $image]
        );
        return (int) $this->db->lastInsertId();
    }

    /** Only updates the row if it belongs to $userId. */
    public function update(int $id, int $userId, string $content, ?string $image): void
    {
        $this->query(
            'UPDATE posts SET content = :content, image = :image WHERE id = :id AND user_id = :uid',
            [':content' => $content, ':image' => $image, ':id' => $id, ':uid' => $userId]
        );
    }

    /** Only deletes the row if it belongs to $userId. Comments and likes are removed by ON DELETE CASCADE. */
    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->query('DELETE FROM posts WHERE id = :id AND user_id = :uid', [':id' => $id, ':uid' => $userId]);
        return $stmt->rowCount() > 0;
    }

    /** Search posts by keyword. */
    public function search(string $term, int $viewerId, int $limit = 30): array
    {
        return $this->fetchAll(
            $this->baseSelect() . 'WHERE p.content LIKE :q ORDER BY p.created_at DESC, p.id DESC LIMIT :lim',
            [':viewer' => $viewerId, ':q' => $this->likePattern($term), ':lim' => $limit]
        );
    }
}
