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
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doos_user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('doos_users')->onDelete('cascade');

            $table->unsignedBigInteger('renter_id')->nullable();
            $table->foreign('renter_id')->references('id')->on('car_renters')->onDelete('cascade');

            $table->unsignedBigInteger('car_owner_id')->nullable();
            $table->foreign('car_owner_id')->references('id')->on('car_owners')->onDelete('cascade');
            
            $table->string('action');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
