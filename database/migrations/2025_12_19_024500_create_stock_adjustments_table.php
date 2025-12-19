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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->default(0); // Quantity adjusted
            $table->string('reason')->nullable(); // Reason for adjustment eg 'damage', 'theft', 'correction', expired, etc.
            $table->enum('type', ['addition', 'subtraction'])->default('subtraction'); // Type of adjustment
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who made the adjustment
            $table->timestamp('adjusted_at')->useCurrent(); // Timestamp of adjustment
            $table->string('note')->nullable(); // Additional note for the adjustment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
