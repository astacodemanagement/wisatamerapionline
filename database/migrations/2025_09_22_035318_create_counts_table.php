<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountsTable extends Migration
{
    public function up()
    {
        Schema::create('counts', function (Blueprint $table) { $table->id(); $table->string('name');  $table->string('amount');   $table->text('description'); $table->enum('status', ['active', 'nonactive'])->default('active'); $table->integer('order_display')->default(0); $table->timestamps();});
    }

    public function down()
    {
        Schema::dropIfExists('counts');
    }
}
