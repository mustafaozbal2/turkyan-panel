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
        Schema::table('incidents', function (Blueprint $table) {
            // Onay mekanizması için durum bilgisi
            // 'onay_bekliyor', 'aktif', 'söndürüldü', 'hatali_alarm'
            $table->string('status')->default('aktif')->after('severity');

            // Yapay zeka tarafından sağlanan veriler
            $table->decimal('confidence_score', 3, 2)->nullable()->after('status'); // Örn: 0.95
            $table->string('estimated_size')->nullable()->after('confidence_score'); // 'small', 'medium', 'large'
            $table->string('evidence_image_url')->nullable()->after('estimated_size'); // Kanıt fotoğrafının URL'si
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'confidence_score',
                'estimated_size',
                'evidence_image_url'
            ]);
        });
    }
};