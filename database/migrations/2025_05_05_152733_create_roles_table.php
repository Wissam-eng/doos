<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role')->uniqid();// owner , manager , support
            $table->string('membership')->default(0);
            $table->string('repaly_review')->default(0);
            $table->string('users_mangement')->default(0);
            $table->string('financial')->default(0);
            $table->string('rental')->default(0);
            $table->string('permissions')->default(0);
            $table->string('car_owners')->default(0);
            $table->string('car_renters')->default(0);
            $table->string('drivers')->default(0);
            $table->string('log')->default(0);

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->softDeletes();
            $table->timestamps();
        });



        DB::insert("INSERT INTO `roles` (`role`, `membership`, `repaly_review`, `users_mangement`, `financial`, `rental`, `permissions`, `car_owners`, `car_renters`, `drivers` , `log`)
         VALUES ('owner', '1', '1', '1', '1', '1', '1', '1', '1', '1' , '1')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
