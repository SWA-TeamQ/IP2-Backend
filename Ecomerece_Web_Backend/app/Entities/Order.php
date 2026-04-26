<?php
namespace App\Entities;

class Order {
    private ?string $id;
    private string $userId;
    private int $subtotalCents;
    private int $taxCents;
    private int $shippingCents;
    private int $totalCents;
    private string $status;
    private array $shippingAddress;
    private array $items;
    private string $createdAt;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->userId = $data['user_id'] ?? $data['userId'] ?? '';
        $this->subtotalCents = (int)($data['subtotal_cents'] ?? $data['subtotalCents'] ?? 0);
        $this->taxCents = (int)($data['tax_cents'] ?? $data['taxCents'] ?? 0);
        $this->shippingCents = (int)($data['shipping_cents'] ?? $data['shippingCents'] ?? 0);
        $this->totalCents = (int)($data['total_cents'] ?? $data['totalCents'] ?? 0);
        $this->status = $data['status'] ?? 'pending';
        $this->shippingAddress = is_array($data['shipping_address'] ?? null) ? $data['shipping_address'] : (json_decode($data['shipping_address'] ?? '{}', true) ?: []);
        $this->items = $data['items'] ?? [];
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public function getId(): ?string { return $this->id; }
    public function getUserId(): string { return $this->userId; }
    public function getSubtotalCents(): int { return $this->subtotalCents; }
    public function getTaxCents(): int { return $this->taxCents; }
    public function getShippingCents(): int { return $this->shippingCents; }
    public function getTotalCents(): int { return $this->totalCents; }
    public function getStatus(): string { return $this->status; }
    public function getShippingAddress(): array { return $this->shippingAddress; }
    public function getItems(): array { return $this->items; }
    public function getCreatedAt(): string { return $this->createdAt; }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'subtotalCents' => $this->subtotalCents,
            'taxCents' => $this->taxCents,
            'shippingCents' => $this->shippingCents,
            'totalCents' => $this->totalCents,
            'status' => $this->status,
            'shippingAddress' => $this->shippingAddress,
            'items' => $this->items,
            'createdAt' => $this->createdAt
        ];
    }
}