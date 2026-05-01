<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sparepart_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('reference_type', ['purchase', 'sale', 'adjustment', 'opname']);
            $table->string('reference_id')->nullable();
            $table->integer('beginning_stock')->default(0);
            $table->integer('stock_in')->default(0);
            $table->integer('stock_out')->default(0);
            $table->integer('ending_stock')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['sparepart_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_cards');
    }
};
