<?php

namespace DV5150\Shop\Models\Deals;

use DV5150\Shop\Concerns\Deals\BaseDiscountTrait;
use DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract;
use DV5150\Shop\Contracts\Deals\Discounts\DiscountContract;
use DV5150\Shop\Contracts\Support\ShopItemCapsuleContract;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Discount extends BaseDeal implements BaseDiscountContract
{
    use BaseDiscountTrait;

    public $timestamps = false;

    protected $guarded = [];

    public function discount(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDiscount(): DiscountContract
    {
        return $this->discount;
    }

    public function products(): MorphToMany
    {
        return $this->morphedByMany(config('shop.models.product'), 'discountable');
    }

    public function getDiscountedPriceGross(ShopItemCapsuleContract $capsule): float
    {
        return $this->getDiscount()
            ->getDiscountedPriceGross($capsule);
    }

    public function getName(): ?string
    {
        return $this->getDiscount()
            ->getName();
    }

    public function getValue(): float
    {
        return $this->getDiscount()
            ->getValue();
    }

    public function getUnit(): string
    {
        return $this->getDiscount()
            ->getUnit();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'unit' => $this->getUnit(),
        ];
    }
}
