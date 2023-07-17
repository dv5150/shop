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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');

            $table->unsignedBigInteger('user_id')
                ->nullable();

            $table->string('email');
            $table->string('phone');
            $table->text('comment')
                ->nullable();

            $table->string('shipping_name');
            $table->string('shipping_zip_code');
            $table->string('shipping_city');
            $table->string('shipping_address');
            $table->text('shipping_comment')
                ->nullable();

            $table->string('billing_name');
            $table->string('billing_zip_code');
            $table->string('billing_city');
            $table->string('billing_address');
            $table->string('billing_tax_number')
                ->nullable();

            $table->unsignedBigInteger('shipping_mode_id')
                ->nullable();

            $table->unsignedBigInteger('payment_mode_id')
                ->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('orders');
    }
};
