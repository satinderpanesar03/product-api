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
        Schema::create('variation_combination_variation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_combination_id')->constrained()->onDelete('cascade');
            $table->foreignId('variation_id')->constrained()->onDelete('cascade');
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_combination_variation');
    }
};
