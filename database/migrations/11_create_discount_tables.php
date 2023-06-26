<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'product_percent_discounts',
        'product_value_discounts',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->morphs('discount');
        });

        Schema::create('discountables', function (Blueprint $table) {
            $table->id();
            $table->morphs('discountable');
            $table->unsignedBigInteger('discount_id');

            $table->foreign('discount_id')
                ->references('id')
                ->on('discounts')
                ->cascadeOnDelete();
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

        Schema::dropIfExists('discountables');

        Schema::dropIfExists('discounts');
    }
};
