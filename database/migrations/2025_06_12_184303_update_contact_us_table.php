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
        Schema::table('contact_us', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['name', 'subject']);

            // Add new columns
            $table->string('first_name')->after('id')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('phone')->after('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_us', function (Blueprint $table) {
            // Rollback: add old columns back
            $table->string('name')->nullable();
            $table->string('subject')->nullable();

            // Drop new columns
            $table->dropColumn(['first_name', 'last_name', 'phone']);
        });
    }
};
