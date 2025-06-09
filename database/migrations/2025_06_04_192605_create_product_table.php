<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('image')->default('no-image.png');
            $table->decimal('cost',10,2)->default(0);
            $table->decimal('price',10,2)->default(0);
            $table->integer('inStock')->default(0);
            $table->unsignedBigInteger('product_details_id');
            $table->unsignedBigInteger('categories_id');
            $table->unsignedBigInteger('brands_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_details_id')
                ->references('id')
                ->on('product_details')
                ->onupdate('cascade')
                ->onDelete('restrict');

            $table->foreign('categories_id')
                ->references('id')
                ->on('categories')
                ->onupdate('cascade')
                ->onDelete('restrict');

            $table->foreign('brands_id')
                ->references('id')
                ->on('brands')
                ->onupdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::dropIfExists('product');
    }
};
