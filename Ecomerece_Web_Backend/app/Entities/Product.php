<?php
namespace App\Entities;

class Product {
    private ?string $id;
    private string $name;
    private string $slug;
    private string $description;
    private int $priceCents;
    private ?int $salePriceCents;
    private array $images;
    private string $category;
    private ?string $badge;
    private array $attributes;
    private array $features;
    private array $highlights;
    private int $stockQuantity;
    private float $rating;
    private int $reviewCount;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->slug = $data['slug'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->priceCents = (int)($data['price_cents'] ?? 0);
        $this->salePriceCents = isset($data['sale_price_cents']) ? (int)$data['sale_price_cents'] : null;
        $this->images = is_array($data['images'] ?? null) ? $data['images'] : [];
        $this->category = $data['category'] ?? '';
        $this->badge = $data['badge'] ?? null;
        $this->attributes = is_array($data['attributes'] ?? null) ? $data['attributes'] : (json_decode($data['attributes'] ?? '{}', true) ?: []);
        $this->features = is_array($data['features'] ?? null) ? $data['features'] : [];
        $this->highlights = is_array($data['highlights'] ?? null) ? $data['highlights'] : [];
        $this->stockQuantity = (int)($data['stock_quantity'] ?? 0);
        $this->rating = (float)($data['rating'] ?? 0);
        $this->reviewCount = (int)($data['review_count'] ?? 0);
    }

    // Getters
    public function getId(): ?string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSlug(): string { return $this->slug; }
    public function getDescription(): string { return $this->description; }
    public function getPriceCents(): int { return $this->priceCents; }
    public function getSalePriceCents(): ?int { return $this->salePriceCents; }
    public function getImages(): array { return $this->images; }
    public function getCategory(): string { return $this->category; }
    public function getBadge(): ?string { return $this->badge; }
    public function getAttributes(): array { return $this->attributes; }
    public function getFeatures(): array { return $this->features; }
    public function getHighlights(): array { return $this->highlights; }
    public function getStockQuantity(): int { return $this->stockQuantity; }
    public function getRating(): float { return $this->rating; }
    public function getReviewCount(): int { return $this->reviewCount; }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'priceCents' => $this->priceCents,
            'salePriceCents' => $this->salePriceCents,
            'images' => $this->images,
            'category' => $this->category,
            'badge' => $this->badge,
            'attributes' => $this->attributes,
            'features' => $this->features,
            'highlights' => $this->highlights,
            'stockQuantity' => $this->stockQuantity,
            'rating' => $this->rating,
            'reviewCount' => $this->reviewCount
        ];
    }
}