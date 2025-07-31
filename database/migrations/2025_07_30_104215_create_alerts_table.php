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
    Schema::create('alerts', function (Blueprint $table) {
        $table->id();
        $table->string('severity'); // Kritik, Yüksek, Orta, Bilgi
        $table->string('icon'); // fa-fire-alt, fa-wind vb.
        $table->string('title');
        $table->string('location');
        $table->text('desc');
        $table->timestamps(); // created_at ve updated_at sütunlarını otomatik ekler
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
