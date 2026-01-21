<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status_task', ['belum', 'proses', 'selesai'])->default('belum');
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->string('pj')->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['active', 'nonactive'])->default('active');
            $table->integer('order_display')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
