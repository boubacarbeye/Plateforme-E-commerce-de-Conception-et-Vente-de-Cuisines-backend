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
        Schema::create('projet_modules', function (Blueprint $table) {
            $table->uuid('ProjetModule_id')->primary();
            $table->foreignUuid('projet_id')->constrained('projet_cuisines', 'ProjetCuisine_id');
            $table->foreignUuid('module_id')->constrained('module_produits', 'ModuleProduit_id');
            $table->foreignUuid('materiau_id')->constrained('materiaux', 'Materiau_id');
            $table->integer('position_x');
            $table->integer('position_y');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet_modules');
    }
};
