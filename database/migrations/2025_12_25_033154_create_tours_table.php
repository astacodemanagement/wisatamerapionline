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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // untuk URL friendly, misal /tour/national-park-2-days
            $table->string('image')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('price_label')->default('/ Per Ticket');
            $table->integer('duration_minutes');
            $table->integer('max_participants');
            $table->string('location');
            $table->text('short_description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('order_display')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popular_tours');
    }
};