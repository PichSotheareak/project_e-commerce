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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('gender')->after('name');
            $table->unsignedBigInteger('staff_id')->after('password');
            $table->softDeletes();

            $table->foreign('staff_id')
                ->references('id')
                ->on('staff')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('gender');
            $table->dropForeign('users_staff_id_foreign');
            $table->dropColumn('staff_id');
            $table->dropSoftDeletes();
        });
    }
};
