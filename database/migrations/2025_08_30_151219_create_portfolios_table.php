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
        Schema::create('portfolios', function (Blueprint $table) {
           $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->text('description_1');
            $table->text('description_2');
            $table->text('description_3');
            $table->enum('status', ['active', 'nonactive'])->default('active');
            $table->integer('order_display')->default(0);
            $table->foreignId('sub_service_id')->constrained('sub_services')->onDelete('cascade'); // Foreign key ke sub_services.id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
