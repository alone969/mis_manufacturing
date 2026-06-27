<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // raw_material, finished_good
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('unit'); // kg, meters, pieces, rolls, etc.
            $table->decimal('minimum_quantity', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
