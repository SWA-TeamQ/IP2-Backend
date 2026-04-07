<?php

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../models/User.model.php';

class UserRepository
{
    // Used in admin pages where we need all users.
    public function getAllUsers()
    {
        $rows = db_fetch_all("SELECT id, full_name AS fullName, email, phone, role, created_at AS createdAt FROM users ORDER BY created_at DESC");
        $users = array();

        foreach ($rows as $row) {
            $users[] = new User($row);
        }

        return $users;
    }

    public function getUserById($id)
    {
        // FIXED: the Query now matches the ID parameter..not email
        $row = db_fetch_one(
            "SELECT id, full_name AS fullName, email, phone, role, created_at AS createdAt 
             FROM users WHERE id = :id LIMIT 1", 
            array(':id' => $id)
        );
        return $row ? new User($row) : null;
    }

    public function getUserByEmail($email)
    {
        // FIXED: Added 'password' to the SELECT so our Controller can verify it during login session
        $row = db_fetch_one(
            "SELECT id, full_name AS fullName, email, phone, role, password, created_at AS createdAt 
             FROM users WHERE email = :email LIMIT 1", 
            array(':email' => $email)
        );
        
        // we return the raw row or a slightly modified object because the User model 
        // usually hides the password, but the Controller needs it for password_verify()
        return $row; 
    }

    public function createUser($data)
    {
        db_execute(
            "INSERT INTO users (full_name, email, phone, password, role, created_at)
             VALUES (:full_name, :email, :phone, :password, :role, NOW())",
            array(
                ':full_name' => $data['full_name'],
                ':email'     => $data['email'],
                ':phone'     => isset($data['phone']) ? $data['phone'] : null,
                ':password'  => $data['password'], // This should be hashed before calling this function
                ':role'      => isset($data['role']) ? $data['role'] : 'customer'
            )
        );

        return db_last_insert_id();
    }
    
    public function updateUser($id, $data)
    {
        return db_execute(
            "UPDATE users
             SET full_name = :full_name,
                 email = :email,
                 phone = :phone,
                 role = :role
             WHERE id = :id",
            array(
                ':full_name' => $data['full_name'],
                ':email'     => $data['email'],
                ':phone'     => isset($data['phone']) ? $data['phone'] : null,
                ':role'      => isset($data['role']) ? $data['role'] : 'customer',
                ':id'        => $id
            )
        ) > 0;
    }

    public function updatePassword($id, $password)
    {
        return db_execute(
            "UPDATE users SET password = :password WHERE id = :id", 
            array(':password' => $password, ':id' => $id)
        ) > 0;
    }

    public function deleteUser($id)
    {
        return db_execute("DELETE FROM users WHERE id = :id", array(':id' => $id)) > 0;
    }
}