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
        Schema::create('orders_cars', function (Blueprint $table) {
            $table->id();
            // car id
            $table->unsignedBigInteger('car_id');
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');

            // renter id
            $table->unsignedBigInteger('renter_id');
            $table->foreign('renter_id')->references('id')->on('car_renters')->onDelete('cascade');

            // driver id
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');

            // longtude and latitude
            $table->string('latitude_from');
            $table->string('longitude_from');
            $table->string('latitude_to');
            $table->string('longitude_to');



            $table->string('distance');

            $table->string('min_price')->nullable();
            $table->string('max_price')->nullable();
            $table->string('price');

            $table->text('check_before')->nullable();
            $table->text('check_after')->nullable();


            // actual longtude and latitude
            $table->string('actual_latitude_from')->nullable();
            $table->string('actual_longitude_from')->nullable();

            $table->string('actual_latitude_to')->nullable();
            $table->string('actual_longitude_to')->nullable();
            $table->text('actual_distance')->nullable();
            $table->text('extra_distance')->nullable();

            $table->enum('driver', ['with_driver', 'without_driver'])->default('without_driver');


            $table->string('Insurance_amount_for_trip')->nullable();
            $table->string('Insurance_amount_for_car')->nullable();


            $table->string('contract_file')->nullable();

            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'canceled'])->default('pending');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_cars');
    }
};
