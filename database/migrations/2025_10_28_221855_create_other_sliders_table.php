<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherSlidersTable extends Migration
{
    public function up()
    {
        Schema::create('other_sliders', function (Blueprint $table) { $table->id(); $table->string('name'); $table->string('slug')->unique(); $table->string('image')->nullable(); $table->text('description'); $table->enum('status', ['active', 'nonactive'])->default('active'); $table->integer('order_display')->default(0); $table->timestamps();});
    }

    public function down()
    {
        Schema::dropIfExists('other_sliders');
    }
}
