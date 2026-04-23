<?php
namespace App\Models;

class User extends Model {
    public function findByEmail($email) {
        return $this->query("SELECT * FROM users WHERE email = :email", ['email' => $email])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role, avatar_url) 
                VALUES (:first_name, :last_name, :email, :password_hash, :role, :avatar_url)
                RETURNING id";
        return $this->query($sql, [
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => $data['role'] ?? 'user',
            'avatar_url' => $data['avatar_url'] ?? null
        ])->fetchColumn();
    }

    public function find($id) {
        return $this->query("SELECT * FROM users WHERE id = :id", ['id' => $id])->fetch();
    }
}