<?php

// database/migrations/2026_01_01_000004_create_projet_cuisines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projet_cuisines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->nullable();          // nullable : visiteur non connecté (RG-05)
            $table->foreign('client_id')
                  ->references('id')->on('utilisateurs')
                  ->nullOnDelete();

            $table->string('nom')->nullable();
            $table->unsignedInteger('longueur_cm');
            $table->unsignedInteger('largeur_cm');
            $table->unsignedInteger('hauteur_cm');
            $table->enum('forme', ['lineaire', 'en_L']);    // RG-01
            $table->decimal('prix_estime', 12, 2)->default(0); // RG-03
            $table->enum('statut', ['brouillon', 'devis_demande', 'traite'])
                  ->default('brouillon');
            $table->timestamps();

            $table->index(['client_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projet_cuisines');
    }
};
