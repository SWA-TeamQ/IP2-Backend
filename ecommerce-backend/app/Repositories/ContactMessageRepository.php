<?php
namespace App\Repositories;

use App\Entities\ContactMessage;
use PDO;

class ContactMessageRepository extends BaseRepository {
    public function __construct() {
        parent::__construct(ContactMessage::class);
    }

    public function create(ContactMessage $message): int {
        $sql = "INSERT INTO contact_messages (name, email, subject, message, status) 
                VALUES (:name, :email, :subject, :message, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $message->name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $message->email, PDO::PARAM_STR);
        $stmt->bindValue(':subject', $message->subject, PDO::PARAM_STR);
        $stmt->bindValue(':message', $message->message, PDO::PARAM_STR);
        $stmt->bindValue(':status', $message->status, PDO::PARAM_STR);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function getAll(int $limit = 10, int $offset = 0): array {
        $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, ContactMessage::class);
    }
}