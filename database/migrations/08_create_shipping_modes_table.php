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
        Schema::create('shipping_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('price_gross')
                ->default(0.0);
            $table->string('provider')
                ->unique();
            $table->string('component_name')
                ->nullable();
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
        Schema::dropIfExists('shipping_modes');
    }
};
