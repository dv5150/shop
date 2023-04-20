<?php

namespace DV5150\Shop\Tests\Mock\Models;

use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Tests\Mock\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements ProductContract
{
    use HasFactory, HasTranslations, HasTranslatableSlug;

    protected $guarded = [];

    public $translatable = [
        'name',
        'slug',
        'description',
    ];

    public $casts = [
        'is_digital_product' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(config('shop.models.category'));
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(config('shop.models.order'));
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

    public function getID()
    {
        return $this->getKey();
    }

    public function isDigitalProduct(): bool
    {
        return $this->is_digital_product;
    }

    public static function newFactory(): Factory
    {
        return ProductFactory::new();
    }
}
