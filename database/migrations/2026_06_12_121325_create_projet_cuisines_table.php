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
        Schema::create('projet_cuisines', function (Blueprint $table) {
            $table->uuid('ProjetCuisine_id')->primary();
            $table->foreignUuid('client_id')->nullable()->constrained('utilisateurs', 'Utilisateur_id');;
            $table->integer('longueur_cm');
            $table->integer('largeur_cm');
            $table->integer('hauteur_cm');
            $table->string('forme', 20);
            $table->decimal('prix_estime', 10, 2)->nullable();
            $table->string('statut', 20)->default('brouillon');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet_cuisines');
    }
};
