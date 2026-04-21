<?php
namespace App\Models;

class User extends Model {
    public function findByEmail($email) {
        return $this->query("SELECT * FROM users WHERE email = ?", [$email])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        return $this->query($sql, [
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT)
        ]);
    }
    public function find($id) {
    return $this->query("SELECT * FROM users WHERE id = ?", [$id])->fetch();
}
}