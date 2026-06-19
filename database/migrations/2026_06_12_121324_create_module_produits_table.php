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
        Schema::create('module_produits', function (Blueprint $table) {
            $table->uuid('ModuleProduit_id')->primary();
            $table->string('ModuleProduit_nom', 20);
            $table->string('categorie', 20);
            $table->integer('largeur_cm')->default(60);
            $table->decimal('prix_base', 10, 2);
            $table->string('image_url', 30)->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_produits');
    }
};
