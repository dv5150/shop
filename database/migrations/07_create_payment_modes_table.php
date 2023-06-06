<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_modes', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('name');
            $table->float('price_gross')
                ->default(0.0);
            $table->boolean('is_active')
                ->default(true);
            $table->boolean('is_online_payment')
                ->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_modes');
    }
};