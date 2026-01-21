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
       Schema::table('profil', function (Blueprint $table) {
            $table->string('main_products')->nullable()->after('term');
            $table->string('address')->nullable()->after('main_products');
            $table->string('main_markets')->nullable()->after('address');
            $table->string('incoterms')->nullable()->after('main_markets'); // International Commercial Terms
            $table->string('terms_of_payment')->nullable()->after('incoterms');
            $table->string('average_lead_time')->nullable()->after('terms_of_payment');
            $table->string('sgs_audit_report_no')->nullable()->after('average_lead_time');
            $table->string('rating')->nullable()->after('sgs_audit_report_no');
            $table->string('average_response_time')->nullable()->after('rating');
            $table->integer('transactions_6_months')->nullable()->after('average_response_time');
            $table->string('description')->nullable()->after('transactions_6_months');
            $table->string('vision')->nullable()->after('description');
             $table->string('mision')->nullable()->after('vision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil', function (Blueprint $table) {
            //
        });
    }
};
