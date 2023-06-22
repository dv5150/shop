<?php

namespace DV5150\Shop\Models\Default;

use DV5150\Shop\Contracts\Models\ProductContract;
use DV5150\Shop\Models\Deals\Discount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements ProductContract
{
    use HasFactory, HasSlug;

    protected $guarded = [];

    public $casts = [
        'is_digital_product' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(config('shop.models.category'));
    }

    public function discounts(): MorphMany
    {
        return $this->morphMany(config('shop.models.discount'), 'discountable');
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

    public function getPriceGross(): float
    {
        return $this->price_gross;
    }

    public function isDigitalProduct(): bool
    {
        return $this->is_digital_product;
    }
}
