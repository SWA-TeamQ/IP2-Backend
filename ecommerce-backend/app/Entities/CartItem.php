<?php
namespace App\Entities;

class CartItem {
    private ?int $id;
    private string $userId;
    private string $productId;
    private int $quantity;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->userId = $data['user_id'] ?? $data['userId'] ?? '';
        $this->productId = $data['product_id'] ?? $data['productId'] ?? '';
        $this->quantity = (int)($data['quantity'] ?? 1);
    }

    public function getId(): ?int { return $this->id; }
    public function getUserId(): string { return $this->userId; }
    public function getProductId(): string { return $this->productId; }
    public function getQuantity(): int { return $this->quantity; }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'productId' => $this->productId,
            'quantity' => $this->quantity
        ];
    }
}