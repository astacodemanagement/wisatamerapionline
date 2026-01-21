<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')
                ->constrained('tours')
                ->cascadeOnDelete();
            $table->string('route_name'); // nama rute / titik perjalanan
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_tours');
    }
};
