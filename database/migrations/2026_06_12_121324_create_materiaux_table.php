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
        Schema::create('materiaux', function (Blueprint $table) {
            $table->uuid('Materiau_id')->primary();
            $table->string('materiau_nom', 20);
            $table->string('type', 20);
            $table->decimal('supplement_prix', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiaux');
    }
};
