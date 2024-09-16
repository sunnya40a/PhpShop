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
        Schema::create('suppliers', function (Blueprint $table) {
            //$table->id();
            $table->tinyIncrements('id');
            $table->string('s_name', 30)->unique();
            $table->string('mobile1', 10);
            $table->string('mobile2', 10)->nullable();
            $table->string('c_person', 20);
            $table->string('contact_info', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
