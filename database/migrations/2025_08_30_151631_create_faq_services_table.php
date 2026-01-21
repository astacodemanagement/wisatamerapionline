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
        Schema::create('faq_services', function (Blueprint $table) {
            $table->id();
            $table->string('question'); // Pertanyaan
            $table->text('answer'); // Jawaban
            $table->string('image')->nullable(); // Gambar terkait FAQ
            $table->foreignId('sub_service_id')->constrained('sub_services')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_services');
    }
};
