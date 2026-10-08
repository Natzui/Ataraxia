<?php
/**
 * CommentModel - data access for the `comments` table (database logic only).
 */
class CommentModel extends Model
{
    /**
     * Load the comments of several posts with ONE query.
     * Returns [post_id => [comment, comment, ...]] (oldest first).
     */
    public function forPosts(array $postIds): array
    {
        $postIds = array_values(array_unique(array_map('intval', $postIds)));
        if (!$postIds) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));
        $rows = $this->fetchAll(
            'SELECT c.id, c.post_id, c.user_id, c.content, c.created_at,
                    u.username, u.full_name, u.profile_image
               FROM comments c
         INNER JOIN users u ON u.id = c.user_id
              WHERE c.post_id IN (' . $placeholders . ')
              ORDER BY c.created_at ASC, c.id ASC',
            $postIds
        );

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[(int) $row['post_id']][] = $row;
        }
        return $grouped;
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT id, post_id, user_id, content, created_at FROM comments WHERE id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    public function create(int $postId, int $userId, string $content): int
    {
        $this->query(
            'INSERT INTO comments (post_id, user_id, content) VALUES (:pid, :uid, :content)',
            [':pid' => $postId, ':uid' => $userId, ':content' => $content]
        );
        return (int) $this->db->lastInsertId();
    }

    /** Only updates the comment if it belongs to $userId. */
    public function update(int $id, int $userId, string $content): void
    {
        $this->query(
            'UPDATE comments SET content = :content WHERE id = :id AND user_id = :uid',
            [':content' => $content, ':id' => $id, ':uid' => $userId]
        );
    }

    /** Only deletes the comment if it belongs to $userId. */
    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->query('DELETE FROM comments WHERE id = :id AND user_id = :uid', [':id' => $id, ':uid' => $userId]);
        return $stmt->rowCount() > 0;
    }
}
