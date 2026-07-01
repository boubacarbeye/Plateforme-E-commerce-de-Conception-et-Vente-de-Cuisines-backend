<?php

// database/migrations/2026_01_01_000005_create_projet_modules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projet_modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('projet_id');
            $table->foreign('projet_id')
                  ->references('id')->on('projet_cuisines')
                  ->cascadeOnDelete();

            $table->uuid('module_id');
            $table->foreign('module_id')
                  ->references('id')->on('module_produits')
                  ->restrictOnDelete();

            $table->uuid('materiau_id')->nullable();
            $table->foreign('materiau_id')
                  ->references('id')->on('materiaux')
                  ->nullOnDelete();

            $table->unsignedInteger('position_x');
            $table->unsignedInteger('position_y');
            $table->unsignedInteger('quantite')->default(1);
            $table->timestamps();

            $table->index(['projet_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projet_modules');
    }
};