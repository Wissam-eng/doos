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
        Schema::create('car_owners', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable()->unique();
            $table->string('img')->nullable();
            $table->enum('role', ['person', 'Companies']);
            $table->enum('status', ['active', 'inactive'])->default('active');


            // forgin key membership
            $table->unsignedBigInteger('membership_id')->nullable();
            $table->foreign('membership_id')->references('id')->on('memberships')->onDelete('cascade');


            //if company
            $table->string('legal_name')->nullable();
            $table->string('employee_id_number')->nullable();
            $table->string('vat_number')->default('under_vat_threshold');
            $table->string('head_office_address')->nullable();

            // if person
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('date_of_birth')->nullable();
            //company id forgn key
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('car_owners')->onDelete('cascade');
            $table->string('address')->nullable();




            $table->string('address2')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();



            $table->string('notice_before_trip')->nullable();
            $table->string('min_duration_trip')->nullable();
            $table->string('max_duration_trip')->nullable();






            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_owners');
    }
};
