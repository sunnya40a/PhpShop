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
        Schema::create('purchaseHistory', function (Blueprint $table) {
            $table->unsignedInteger('PO')->length(10)->primary();
            //$table->unsignedInteger('PO')->length(11)->zerofill()->primary();
            $table->date('Pdate');
            // Adjust data types based on your needs
            $table->string('item_list', 9); // Consider increasing length if needed
            $table->string('material_desc', 255);
            $table->unsignedSmallInteger('qty'); // Use unsignedSmallInteger if quantity is always positive
            $table->string('unit', 3);
            $table->decimal('u_price', 10, 2);
            $table->decimal('p_price', 10, 2);
            $table->string('user', 10); // Consider increasing length if needed
            $table->string('category', 255)->nullable(false);
            $table->unsignedTinyInteger('supplier_id'); // Consider increasing length if needed
            $table->date('Rdate')->nullable();
            $table->unsignedTinyInteger('paid_status');
            $table->timestamps();
            // Define foreign key constraint
            $table->foreign('category')
                ->references('description')
                ->on('category_list')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            // Define foreign key constraint
            $table->foreign('unit')
                ->references('unit')
                ->on('unitlists')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            //Define foreign key constraint
            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            // Define foreign key constraint
            $table->foreign('paid_status')
                ->references('id')
                ->on('paymentstatuses')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            // Optional index for faster category-based search/filtering
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchaseHistory');
    }
};
