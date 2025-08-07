<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('volunteer_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_user_id')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('volunteer_reports', function (Blueprint $table) {
            $table->dropColumn('assigned_user_id');
        });
    }
};
