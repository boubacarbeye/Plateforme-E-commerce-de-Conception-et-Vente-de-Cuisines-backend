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
        Schema::create('devis', function (Blueprint $table) {
            $table->uuid('Devis_id')->primary();
            
            // Clé étrangère vers ProjetCuisine
            $table->foreignUuid('projet_id')->constrained('projet_cuisines', 'ProjetCuisine_id');
            
            $table->decimal('montant_total', 10, 2);
            $table->string('pdf_url', 20)->nullable();
            $table->timestamp('date_creation')->useCurrent();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
