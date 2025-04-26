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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();


            // car owner id forgn key
            $table->unsignedBigInteger('car_owner_id');
            $table->foreign('car_owner_id')->references('id')->on('car_owners')->onDelete('cascade');

            // categories_cars id forgn key
            $table->unsignedBigInteger('categories_cars_id')->nullable();
            $table->foreign('categories_cars_id')->references('id')->on('categories_cars')->onDelete('cascade');

            $table->enum('status', ['active', 'pending', 'inactive' , 'incomplete' , 'blocked'])->default('inactive');


            // car info
            $table->string('car_location');
            $table->string('car_vin'); // 17 chart
            $table->string('car_model');
            $table->string('car_mileage_range'); // 50_100000
            $table->enum('mechanical_condition', ['Excellent', 'Good', 'Fair', 'Not_work']);
            $table->enum('all_seats_seatable', ['yes', 'no']);
            $table->text('additional_info')->nullable();
            $table->string('number_of_door');
            $table->string('number_of_seats');
            $table->json('features')->nullable();

            $table->text('description')->nullable();
            $table->string('license_plate_number')->nullable();
            $table->string('state')->nullable();


            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
