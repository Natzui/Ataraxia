<?php
/**
 * LikeModel - data access for the `likes` table (database logic only).
 * The UNIQUE (post_id, user_id) key guarantees one like per user per post.
 */
class LikeModel extends Model
{
    public function exists(int $postId, int $userId): bool
    {
        return (bool) $this->fetchValue(
            'SELECT 1 FROM likes WHERE post_id = :pid AND user_id = :uid LIMIT 1',
            [':pid' => $postId, ':uid' => $userId]
        );
    }

    public function count(int $postId): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM likes WHERE post_id = :pid', [':pid' => $postId]);
    }

    /** Like if not liked yet, otherwise unlike. Returns ['liked' => bool, 'count' => int]. */
    public function toggle(int $postId, int $userId): array
    {
        if ($this->exists($postId, $userId)) {
            $this->query('DELETE FROM likes WHERE post_id = :pid AND user_id = :uid', [':pid' => $postId, ':uid' => $userId]);
            $liked = false;
        } else {
            $this->query('INSERT IGNORE INTO likes (post_id, user_id) VALUES (:pid, :uid)', [':pid' => $postId, ':uid' => $userId]);
            $liked = true;
        }
        return ['liked' => $liked, 'count' => $this->count($postId)];
    }
}
