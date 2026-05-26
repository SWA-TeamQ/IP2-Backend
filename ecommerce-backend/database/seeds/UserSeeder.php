<?php
namespace App\Database\Seeds;

use App\Core\Database;
use PDO;

class UserSeeder {
    public static function run() {
        $db = Database::getConnection();
        
        // Admin user
        $adminEmail = 'admin@shoplight.com';
        $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role) 
                VALUES (:first_name, :last_name, :email, :password_hash, :role)
                ON CONFLICT (email) DO NOTHING";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'first_name' => 'ShopLight',
            'last_name' => 'Admin',
            'email' => $adminEmail,
            'password_hash' => $adminPassword,
            'role' => 'admin'
        ]);
        
        echo "User seeding complete (Admin: admin@shoplight.com / admin123)\n";
    }
}
