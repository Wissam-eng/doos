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
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->string('doos');
            $table->string('car_owner');
            $table->string('driver')->nullable();
            $table->string('insurance_amount')->nullable();
            $table->enum('insurance_status', ['Recovered', 'Discounted']);
            // order id
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders_cars')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};
