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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('image', 100)->nullable();
            $table->string('name', 100);
            $table->string('user', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address_by_card')->nullable();
            $table->string('rt_rw', 100)->nullable();
            $table->string('subdistrict', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->enum('use_address', ['Y', 'N'])->nullable();
            $table->string('occupation_type', 100)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->text('company_address')->nullable();
            $table->text('correspondence_address')->nullable();
            $table->string('last_education', 50)->nullable();
            $table->string('study_program', 50)->nullable();
            $table->string('university_name', 100)->nullable();
            $table->string('graduation_year', 4)->nullable();
            $table->string('member_number', 50)->nullable();
            $table->date('join_date')->nullable();
            $table->string('member_type', 50)->nullable();
            $table->string('member_status', 50)->nullable();

            // Relasi branch_id ke tabel branches
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->string('legal_authorization_permission', 100)->nullable();
            $table->string('legal_authorization_file', 100)->nullable();
            $table->string('retired_tax_officer', 100)->nullable();
            $table->string('position_tax', 100)->nullable();
            $table->string('retirement_year', 4)->nullable();
            $table->string('retirement_decision_letter', 100)->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->string('practice_license_number', 100)->nullable();
            $table->string('certification_level', 100)->nullable();
            $table->date('practice_license_issue_date')->nullable();

            // Laravel timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
   
    }
};
