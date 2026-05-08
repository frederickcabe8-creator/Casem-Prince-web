<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'short_description',
        'sku', 'base_price', 'sale_price', 'stock_quantity', 'is_active', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'base_price'  => 'decimal:2',
            'sale_price'  => 'decimal:2',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    // Spatie MediaLibrary
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
             ->useFallbackUrl('/images/product-placeholder.jpg');

        $this->addMediaCollection('thumbnail')
             ->singleFile()
             ->useFallbackUrl('/images/product-placeholder.jpg');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(300)->height(300)->nonQueued();

        $this->addMediaConversion('card')
             ->width(600)->height(600)->nonQueued();
    }

    // Relationships
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors
    public function getEffectivePriceAttribute(): string
    {
        return $this->sale_price ?? $this->base_price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->base_price;
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%")
              ->orWhere('sku', 'LIKE', "%{$term}%");
        });
    }

    public function scopeInCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopePriceBetween($query, float $min, float $max)
    {
        return $query->whereBetween('base_price', [$min, $max]);
    }
}