<?php

// database/migrations/2026_01_01_000006_create_devis_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('projet_id');
            $table->foreign('projet_id')
                ->references('id')->on('projet_cuisines')
                ->cascadeOnDelete();

            $table->string('numero')->unique();             // ex: DEV-2026-0001
            $table->decimal('montant_total', 12, 2);        // RG-03 + RG-04
            $table->string('pdf_url')->nullable();          // RG-04
            $table->enum('statut', ['brouillon', 'envoye', 'accepte', 'refuse'])
                ->default('brouillon');
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamps();

            $table->index('projet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
