<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();

            // Foreign key otomatis dibuatkan index oleh MySQL, jadi tidak perlu index() terpisah.
            // restrictOnDelete: anggota tidak boleh dihapus permanen selama masih punya referral.
            $table->foreignId('pemberi_referral_id')->constrained('members')->restrictOnDelete();
            $table->foreignId('penerima_referral_id')->constrained('members')->restrictOnDelete();

            $table->string('client_name');
            $table->string('project_name');
            $table->unsignedBigInteger('nilai_estimasi')->nullable(); // rupiah, tanpa desimal
            $table->string('status', 20)->default('introduced');
            $table->timestamp('status_diubah_at')->nullable();        // untuk hitung "Won bulan ini"
            $table->text('catatan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Layar "Referral Saya" (yang perlu ditindaklanjuti penerima)
            $table->index(['penerima_referral_id', 'status']);
            // Counter dan laporan: jumlah Won per periode
            $table->index(['status', 'status_diubah_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
