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
        $table->decimal('enlem', 10, 7)->nullable()->after('updated_at');
        $table->decimal('boylam', 10, 7)->nullable()->after('enlem');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['enlem', 'boylam']);
    });
}

};
