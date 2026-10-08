<?php
/**
 * Base model - small PDO helpers. Every query MUST go through these
 * helpers so that values are always bound (prepared statements).
 */
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /** Prepare + bind + execute. $params may be named (':id' => 1) or positional (0 => 1). */
    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        foreach ($params as $name => $value) {
            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } elseif ($value === null) {
                $type = PDO::PARAM_NULL;
            } else {
                $type = PDO::PARAM_STR;
            }
            $stmt->bindValue(is_int($name) ? $name + 1 : $name, $value, $type);
        }
        $stmt->execute();
        return $stmt;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row ?: null;
    }

    protected function fetchValue(string $sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /** Build a safe LIKE pattern (escapes % and _ typed by the user). */
    protected function likePattern(string $term): string
    {
        return '%' . addcslashes($term, "\\%_") . '%';
    }
}
