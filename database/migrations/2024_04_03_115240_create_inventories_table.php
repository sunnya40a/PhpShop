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
        Schema::create('inventory', function (Blueprint $table) {
            $table->string('Item_list', 9)->primary();
            $table->string('description', 255);
            $table->integer('qty');
            $table->string('category', 255)->nullable(false);
            $table->timestamps();
            // Define foreign key constraint
            $table->foreign('category')
                ->references('description')
                ->on('category_list')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            // Index for the foreign key column
            $table->index('category', 'fk_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
