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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->dateTime('transaction_date');
            $table->dateTime('pick_up_date_time');
            $table->decimal('total_amount',10,2);
            $table->decimal('paid_amount',10,2);
            $table->string('status')->comment('pending,in_progress,finished,completed');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onupdate('cascade')
                ->onDelete('restrict');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onupdate('cascade')
                ->onDelete('restrict');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onupdate('cascade')
                ->onDelete('restrict');

            $table->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
                ->onupdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
