<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'cart_percent_coupons',
        'cart_value_coupons',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->morphs('coupon');
            $table->string('code')
                ->unique();
        });

        foreach ($this->tables as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('name')
                    ->nullable();
                $table->float('value');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (array_reverse($this->tables) as $tableName) {
            Schema::dropIfExists($tableName);
        }

        Schema::dropIfExists('coupons');
    }
};
