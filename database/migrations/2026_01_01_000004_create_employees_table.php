<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees_table', function (Blueprint $table) {
            $table->id('employee_ID');
            $table->string('username')->unique();
            $table->string('password_hash');

            // No Roles table in the ERD — plain column, add
            // ->constrained() later if you create that table.
            $table->unsignedBigInteger('role_id')->nullable();

            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees_table');
    }
};
