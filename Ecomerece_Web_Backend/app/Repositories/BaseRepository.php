<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

abstract class BaseRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    protected function query(string $sql, array $params = []) {
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $type = is_int($val) ? PDO::PARAM_INT : (is_bool($val) ? PDO::PARAM_BOOL : (is_null($val) ? PDO::PARAM_NULL : PDO::PARAM_STR));
            $stmt->bindValue($key, $val, $type);
        }
        $stmt->execute();
        return $stmt;
    }

    protected function toPostgresArray(array $phpArray): string {
        if (empty($phpArray)) return '{}';
        $result = [];
        foreach ($phpArray as $item) {
            $result[] = '"' . str_replace('"', '\\"', $item) . '"';
        }
        return '{' . implode(',', $result) . '}';
    }
}