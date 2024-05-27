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
        Schema::create('salesHistories', function (Blueprint $table) {
            $table->id();
            $table->Integer('InvoiceNO')->length(7);
            $table->unsignedTinyInteger('sn');
            //$table->unsignedInteger('PO')->length(11)->zerofill()->primary();
            $table->date('Sdate');
            // Adjust data types based on your needs
            $table->string('item_list', 9); // Consider increasing length if needed
            $table->string('description', 255);
            $table->unsignedTinyInteger('qty'); // Use unsignedSmallInteger if quantity is always positive
            $table->string('unit', 3);
            $table->decimal('price', 10, 2);
            $table->string('user', 10); // Consider increasing length if needed
            $table->timestamps();
            // Define foreign key constraint
            $table->foreign('unit')
                ->references('unit')
                ->on('unitlists')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            // Optional index for faster category-based search/filtering
            $table->index('InvoiceNO');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salesHistories');
    }
};
