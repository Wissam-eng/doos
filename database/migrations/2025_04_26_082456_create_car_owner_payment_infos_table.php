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
        Schema::create('car_owner_payment_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_owner_id');
            $table->foreign('car_owner_id')->references('id')->on('car_owners')->onDelete('cascade');
            $table->string('name_on_card');
            $table->string('card_number');
            $table->string('expiration_date');
            $table->string('cvv');
            $table->string('country');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('zip_code')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_owner_payment_infos');
    }
};
