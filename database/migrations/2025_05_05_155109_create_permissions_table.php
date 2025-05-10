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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            // role id
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');


            $table->string('permission')->uniqid();
            $table->string('add')->default(0);
            $table->string('edit')->default(0);
            $table->string('delete')->default(0);
            $table->string('view')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->softDeletes();

            $table->timestamps();
        });

        $permissions = [
            'membership',
            'repaly_review',
            'users_mangement',
            'financial',
            'rental',
            'permissions',
            'car_owners',
            'car_renters',
            'drivers',
            'log',
        ];

        $data = array_map(function ($permission) {
            return [
                'role_id' => 1,
                'permission' => $permission,
                'add' => 1,
                'edit' => 1,
                'delete' => 1,
                'view' => 1,
                'status' => 1,
            ];
        }, $permissions);

        DB::table('permissions')->insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
