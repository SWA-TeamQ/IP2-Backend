<?php
namespace App\Entities;

class ContactMessage extends BaseEntity {
    public int $id;
    public string $name;
    public string $email;
    public ?string $subject = null;
    public string $message;
    public string $status = 'new';
    public string $created_at;

    protected static function getTableName(): string {
        return 'contact_messages';
    }

    protected static function getFillable(): array {
        return ['name', 'email', 'subject', 'message', 'status'];
    }
}