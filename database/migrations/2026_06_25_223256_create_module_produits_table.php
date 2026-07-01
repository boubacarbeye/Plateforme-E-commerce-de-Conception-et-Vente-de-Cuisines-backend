<?php

// database/migrations/2026_01_01_000003_create_module_produits_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('module_produits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom');
            $table->enum('categorie', [
                'meuble_bas',
                'meuble_haut',
                'colonne',
                'plan_travail',
                'evier',
                'robinetterie',
                'electromenager',
            ]);
            $table->unsignedInteger('largeur_cm');
            $table->unsignedInteger('hauteur_cm')->nullable();
            $table->unsignedInteger('profondeur_cm')->nullable();
            $table->decimal('prix_base', 10, 2);
            $table->string('image_url')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index(['categorie', 'actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_produits');
    }
};