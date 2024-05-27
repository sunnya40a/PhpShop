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
        Schema::create('paymentstatuses', function (Blueprint $table) {
            $table->tinyIncrements('id'); // Creates an unsigned tiny integer auto-incrementing primary key
            $table->unsignedTinyInteger('code'); // Creates an unsigned tiny integer
            $table->string('status', 15);
            $table->boolean('onpurchase')->default(false);
            $table->boolean('onsale')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymentstatuses');
    }
};
