<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->onDelete('cascade');
            $table->foreignId('sparepart_id')->constrained();
            $table->integer('system_stock')->default(0);
            $table->integer('physical_stock')->default(0);
            $table->integer('difference')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_counted')->default(false);
            $table->timestamps();

            $table->unique(['stock_opname_id', 'sparepart_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
