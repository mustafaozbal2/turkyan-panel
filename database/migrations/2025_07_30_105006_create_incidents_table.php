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
    Schema::create('incidents', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('location');
        $table->string('severity');
        $table->decimal('area_hectares', 8, 2);
        $table->integer('response_time_minutes');
        $table->decimal('latitude', 10, 7);   // YENİ EKLENDİ
        $table->decimal('longitude', 10, 7);  // YENİ EKLENDİ
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
