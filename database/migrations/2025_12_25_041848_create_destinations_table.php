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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); // Nama destinasi, misalnya "Bromo", "Bali", "Yogyakarta"
            $table->string('slug', 300)->unique()->index(); // Slug untuk URL friendly, misalnya "bromo"
            $table->longText('description'); // Deskripsi lengkap destinasi
            $table->string('short_description')->nullable(); // Ringkasan singkat untuk preview
            $table->string('thumbnail', 255)->nullable(); // Gambar utama/thumbnail destinasi
            $table->text('location_details')->nullable(); // Detail lokasi (alamat, koordinat, dll)
            $table->integer('views')->default(0); // Jumlah views/halaman destinasi dibuka
            $table->enum('status', ['active', 'nonactive'])->default('active');
            $table->integer('order_display')->default(0)->index(); // Urutan tampilan di halaman daftar destinasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};