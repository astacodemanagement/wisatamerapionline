<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // form_name
            $table->string('email');            // form_email
            $table->string('phone')->nullable(); // form_phone
            $table->string('subject')->nullable(); // form_subject
            $table->text('message');            // form_message
            $table->timestamps();               // created_at & updated_at
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
