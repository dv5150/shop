<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_points', function (Blueprint $table) {
            $table->id();

            $table->string('provider');
            $table->string('external_id');

            $table->string('name');
            $table->string('zip_code');
            $table->string('city');
            $table->string('address');

            $table->string('info')
                ->nullable();
            $table->json('opening_hours')
                ->nullable();

            $table->timestamps();

            $table->index(['provider', 'external_id'], 'pickup_point_ext_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_points');
    }
}
