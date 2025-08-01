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
    Schema::create('fire_stations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained('users'); // İtfaiye kullanıcısına bağlamak için
        $table->string('name');
        $table->string('phone')->nullable();
        $table->decimal('latitude', 10, 7);
        $table->decimal('longitude', 10, 7);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fire_stations');
    }
};
