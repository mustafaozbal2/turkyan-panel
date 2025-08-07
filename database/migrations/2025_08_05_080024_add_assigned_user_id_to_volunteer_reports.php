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
        Schema::table('volunteer_reports', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('volunteer_reports', function (Blueprint $table) {
    $table->unsignedBigInteger('assigned_user_id')->nullable();

    $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
});

    }
};
