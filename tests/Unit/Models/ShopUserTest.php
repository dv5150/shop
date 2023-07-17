<?php

namespace DV5150\Shop\Tests\Unit\Models;

use DV5150\Shop\Contracts\Models\ShopUserContract;
use DV5150\Shop\Tests\Concerns\ProvidesSampleUser;
use DV5150\Shop\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopUserTest extends TestCase
{
    use ProvidesSampleUser;

    protected array $sampleAddress = [
        'display_name' => 'Test address',
        'name' => 'Johnny',
        'zip_code' => '1234',
        'city' => 'Budapest',
        'address' => 'Sample street 2',
        'phone' => '+36301234567',
        'comment' => 'Some comment goes here',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSampleUser();
    }

    /** @test */
    public function user_has_many_shipping_addresses()
    {
        /** @var ShopUserContract $user */
        $user = config('shop.models.user')::first();

        $user->shippingAddresses()->create($this->sampleAddress);

        $this->assertDatabaseHas('shipping_addresses', array_merge($this->sampleAddress, [
            'user_id' => $this->testUser->getKey(),
        ]));

        $this->assertSame(
            json_encode([
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
            ]), $user->getShippingAddresses()->toJson()
        );
    }

    /** @test */
    public function user_has_many_orders()
    {
        /** @var ShopUserContract $user */
        $user = config('shop.models.user')::first();

        $this->assertInstanceOf(HasMany::class, $user->orders());
    }
}