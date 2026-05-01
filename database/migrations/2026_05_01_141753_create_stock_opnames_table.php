<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number')->unique();
            $table->date('opname_date');
            $table->string('period'); // Format: YYYY-MM
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->enum('status', ['draft', 'in_progress', 'completed', 'verified'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
