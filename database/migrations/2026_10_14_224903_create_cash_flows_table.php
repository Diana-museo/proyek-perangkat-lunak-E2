<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // UUID atau kode unik transaksi
            $table->string('project_id')->nullable();   // Referensi dari kelompok D-1 (Proyek)
            $table->enum('type', ['income', 'expense']); // Pemasukan atau Pengeluaran
            $table->decimal('amount', 15, 2);           // Nilai uang (DPP, PPN, dll)
            $table->string('category');                 // Contoh: 'termin_invoice', 'payroll', 'pajak'
            $table->date('transaction_date');           // Format: YYYY-MM-DD
            $table->text('description')->nullable();    // Keterangan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};