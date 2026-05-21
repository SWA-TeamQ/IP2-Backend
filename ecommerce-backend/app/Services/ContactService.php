<?php
namespace App\Services;

use App\Entities\ContactMessage;
use App\Repositories\ContactMessageRepository;

class ContactService {
    private ContactMessageRepository $contactMessageRepo;

    public function __construct() {
        $this->contactMessageRepo = new ContactMessageRepository();
    }

    public function submitContactMessage(array $data): int {
        $message = new ContactMessage([
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'status' => 'new'
        ]);
        return $this->contactMessageRepo->create($message);
    }

    public function getAllContactMessages(int $limit = 10, int $offset = 0): array {
        return $this->contactMessageRepo->getAll($limit, $offset);
    }
}