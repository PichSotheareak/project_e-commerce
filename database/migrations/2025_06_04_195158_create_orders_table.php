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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customers_id');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('branches_id');
            $table->timestamp('order_date');
            $table->decimal('total_amount',10,2)->default(0);
            $table->string('status');
            $table->string('payment_status')->default('unpaid');
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customers_id')
                ->references('id')
                ->on('customers')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('users_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

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
        Schema::dropIfExists('orders');
    }
};
