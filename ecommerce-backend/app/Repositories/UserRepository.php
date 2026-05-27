<?php
namespace App\Repositories;

use App\Entities\User;
use PDO;

class UserRepository extends BaseRepository {
    public function findByEmail(string $email): ?User {
        $stmt = $this->query("SELECT * FROM users WHERE email = :email", ['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    public function find(string $id): ?User {
        $stmt = $this->query("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    public function create(User $user): string {
        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role, avatar_url) 
                VALUES (:first_name, :last_name, :email, :password_hash, :role, :avatar_url)
                RETURNING id";
        
        $stmt = $this->query($sql, [
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'password_hash' => $user->getPasswordHash(),
            'role' => $user->getRole(),
            'avatar_url' => $user->getAvatarUrl()
        ]);
        
        return $stmt->fetchColumn();
    }
}