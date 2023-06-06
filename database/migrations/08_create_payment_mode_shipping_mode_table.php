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
        Schema::create('payment_mode_shipping_mode', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_mode_id');
            $table->unsignedBigInteger('shipping_mode_id');

            $table->foreign('payment_mode_id')
                ->references('id')
                ->on('payment_modes')
                ->cascadeOnDelete();

            $table->foreign('shipping_mode_id')
                ->references('id')
                ->on('shipping_modes')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_mode_shipping_mode');
    }
};
