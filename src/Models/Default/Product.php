<?php

namespace DV5150\Shop\Models\Default;

use DV5150\Shop\Concerns\Models\Product\DetachesAllDiscounts;
use DV5150\Shop\Contracts\Models\SellableItemContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements SellableItemContract
{
    use HasFactory, HasSlug, DetachesAllDiscounts;

    protected $guarded = [];

    public $casts = [
        'is_digital_item' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(config('shop.models.category'));
    }

    public function discounts(): MorphToMany
    {
        return $this->morphToMany(config('shop.models.discount'), 'discountable');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->name;
    }

    public function getPriceGross(): float
    {
        return $this->price_gross;
    }

    public function isDigitalItem(): bool
    {
        return $this->is_digital_item;
    }
}
