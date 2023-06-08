<?php

namespace DV5150\Shop\Tests\Concerns;

use DV5150\Shop\Tests\Mock\Models\User;
use Illuminate\Support\Facades\Hash;

trait ProvidesSampleUser
{
    protected User $testUser;

    public function setUpSampleUser(): void
    {
        $this->testUser = config('shop.models.user')::create([
            'name' => 'Johnny Jackson',
            'email' => 'johnny+12345@jackson.com',
            'password' => Hash::make('testing'),
        ]);
    }
}