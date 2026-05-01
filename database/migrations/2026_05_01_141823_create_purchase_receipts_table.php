<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->date('receipt_date');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['draft', 'approved'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_receipts');
    }
};
