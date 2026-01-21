<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source_table', 50)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->enum('action', ['Create', 'Read', 'Update', 'Delete', 'Login Success', 'Login Failed'])->nullable();
            $table->timestamp('logged_at')->nullable();
            $table->string('user', 50)->nullable();
            $table->text('old_data')->nullable();
            $table->text('new_data')->nullable();
            $table->timestamps();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_histories');
    }
}
