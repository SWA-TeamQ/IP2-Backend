<?php
namespace App\Entities;

class User {
    private ?string $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private ?string $passwordHash;
    private string $role;
    private ?string $avatarUrl;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->firstName = $data['first_name'] ?? $data['firstName'] ?? '';
        $this->lastName = $data['last_name'] ?? $data['lastName'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->passwordHash = $data['password_hash'] ?? null;
        $this->role = $data['role'] ?? 'user';
        $this->avatarUrl = $data['avatar_url'] ?? $data['avatarUrl'] ?? null;
    }

    public function getId(): ?string { return $this->id; }
    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getEmail(): string { return $this->email; }
    public function getPasswordHash(): ?string { return $this->passwordHash; }
    public function getRole(): string { return $this->role; }
    public function getAvatarUrl(): ?string { return $this->avatarUrl; }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'role' => $this->role,
            'avatarUrl' => $this->avatarUrl
        ];
    }
}