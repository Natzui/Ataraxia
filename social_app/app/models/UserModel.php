<?php
/**
 * UserModel - data access for the `users` table (database logic only).
 */
class UserModel extends Model
{
    /** Find a user by id (no password hash). */
    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT id, username, full_name, bio, profile_image, created_at
               FROM users WHERE id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    /** Find a user by username INCLUDING the password hash (used for login). */
    public function findByUsername(string $username): ?array
    {
        return $this->fetchOne(
            'SELECT id, username, password, full_name, bio, profile_image, created_at
               FROM users WHERE username = :username LIMIT 1',
            [':username' => $username]
        );
    }

    public function usernameExists(string $username): bool
    {
        return (bool) $this->fetchValue(
            'SELECT 1 FROM users WHERE username = :username LIMIT 1',
            [':username' => $username]
        );
    }

    public function create(string $username, string $passwordHash, string $fullName, ?string $profileImage): int
    {
        $this->query(
            'INSERT INTO users (username, password, full_name, profile_image)
             VALUES (:username, :password, :full_name, :profile_image)',
            [
                ':username'      => $username,
                ':password'      => $passwordHash,
                ':full_name'     => $fullName,
                ':profile_image' => $profileImage,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $fullName, ?string $bio, ?string $profileImage): void
    {
        $this->query(
            'UPDATE users SET full_name = :full_name, bio = :bio, profile_image = :profile_image WHERE id = :id',
            [
                ':full_name'     => $fullName,
                ':bio'           => $bio,
                ':profile_image' => $profileImage,
                ':id'            => $id,
            ]
        );
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        $this->query('UPDATE users SET password = :password WHERE id = :id', [':password' => $passwordHash, ':id' => $id]);
    }

    /** Search users by name or username. */
    public function search(string $term, int $limit = 30): array
    {
        $like = $this->likePattern($term);
        return $this->fetchAll(
            'SELECT id, username, full_name, bio, profile_image, created_at
               FROM users
              WHERE username LIKE :q1 OR full_name LIKE :q2
              ORDER BY full_name ASC
              LIMIT :lim',
            [':q1' => $like, ':q2' => $like, ':lim' => $limit]
        );
    }
}
