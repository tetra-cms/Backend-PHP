<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            $table->string('image_url')
                ->default('products/no_image.png');

            $table->string('name')->index();

            $table->text('description');

            $table->integer('price');

            $table->integer('stock')
                ->default(-1);

            $table->unsignedInteger('supply_quantum')
                ->default(1);

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();


            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
