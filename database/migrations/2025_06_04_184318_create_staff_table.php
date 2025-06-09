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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender')->default('male');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('profile')->default('no-image.png');
            $table->string('current_address');
            $table->string('position')->default('admin');
            $table->decimal('salary',10,2)->default(0);
            $table->unsignedBigInteger('branches_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('branches_id')
                ->references('id')
                ->on('branches')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
