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
        Schema::table('profils', function (Blueprint $table) {
            $table->renameColumn('display_gambar_1', 'breadcrumb_1');
            $table->renameColumn('display_gambar_2', 'breadcrumb_2');
            $table->renameColumn('display_gambar_3', 'breadcrumb_3');
            $table->renameColumn('display_gambar_4', 'breadcrumb_4');
            $table->renameColumn('display_gambar_5', 'breadcrumb_5');
            $table->renameColumn('display_gambar_6', 'breadcrumb_6');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profils', function (Blueprint $table) {
            $table->renameColumn('breadcrumb_1', 'display_gambar_1');
            $table->renameColumn('breadcrumb_2', 'display_gambar_2');
            $table->renameColumn('breadcrumb_3', 'display_gambar_3');
            $table->renameColumn('breadcrumb_4', 'display_gambar_4');
            $table->renameColumn('breadcrumb_5', 'display_gambar_5');
            $table->renameColumn('breadcrumb_6', 'display_gambar_6');
        });
    }
};
