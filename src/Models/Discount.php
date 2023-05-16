<?php

namespace DV5150\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discount extends Model
{
    public $timestamps = false;

    public function discountable(): MorphTo
    {
        return $this->morphTo();
    }

    public function discount(): MorphTo
    {
        return $this->morphTo();
    }

    public function toArray(): array
    {
        return [
            'name' => static::getName(),
            'value' => static::getValue(),
            'unit' => static::getUnit(),
        ];
    }
}
