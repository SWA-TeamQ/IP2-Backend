<?php
namespace App\Entities;

class Review {
    private ?string $id;
    private string $productId;
    private ?string $userId;
    private int $rating;
    private string $comment;
    private string $createdAt;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['product_id'] ?? $data['productId'] ?? '';
        $this->userId = $data['user_id'] ?? $data['userId'] ?? null;
        $this->rating = (int)($data['rating'] ?? 0);
        $this->comment = $data['comment'] ?? '';
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public function getId(): ?string { return $this->id; }
    public function getProductId(): string { return $this->productId; }
    public function getUserId(): ?string { return $this->userId; }
    public function getRating(): int { return $this->rating; }
    public function getComment(): string { return $this->comment; }
    public function getCreatedAt(): string { return $this->createdAt; }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'productId' => $this->productId,
            'userId' => $this->userId,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'createdAt' => $this->createdAt
        ];
    }
}