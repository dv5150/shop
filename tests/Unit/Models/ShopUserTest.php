<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\Models\ShopUserContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

test('user_has_many_shipping_addresses', function () {
    /** @var ShopUserContract $user */
    $user = config('shop.models.user')::create([
        'name' => 'Johnny Jackson',
        'email' => 'johnny+12345@jackson.com',
        'password' => Hash::make('testing'),
    ]);

    $user->shippingAddresses()->create($this->sampleAddress);

    $this->assertDatabaseHas('shipping_addresses', array_merge($this->sampleAddress, [
        'user_id' => $user->getKey(),
    ]));

    expect(json_encode([
        [
            'displayName' => $this->sampleAddress['display_name'].' ('
                .$this->sampleAddress['zip_code'].' '
                .$this->sampleAddress['city'].', '
                .$this->sampleAddress['address'].')',
            'name' => $this->sampleAddress['name'],
            'zipCode' => $this->sampleAddress['zip_code'],
            'city' => $this->sampleAddress['city'],
            'street' => $this->sampleAddress['address'],
            'comment' => $this->sampleAddress['comment'],
        ]
    ]))->toBe($user->getShippingAddresses()->toJson());
});

test('user_has_many_orders', function () {
    /** @var ShopUserContract $user */
    $user = config('shop.models.user')::create([
        'name' => 'Johnny Jackson',
        'email' => 'johnny+12345@jackson.com',
        'password' => Hash::make('testing'),
    ]);

    $this->assertInstanceOf(HasMany::class, $user->orders());
});