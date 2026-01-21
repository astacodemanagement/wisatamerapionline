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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('headline', 255);
            $table->string('news_slug', 300)->unique()->index();
            $table->longText('body');
            $table->string('resume')->nullable();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->string('thumbnail', 255)->nullable();
            $table->string('author', 100)->nullable();
            $table->dateTime('publish_date')->nullable();
            $table->enum('status', ['active', 'nonactive'])->default('active');
            $table->integer('order_display')->default(0)->index();
            $table->integer('views')->nullable();
            $table->foreign('category_id')->references('id')->on('blog_categories')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
