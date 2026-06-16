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
    Schema::create('utilisateurs', function (Blueprint $table) {
        $table->uuid('Utilisateur_id')->primary();
        $table->string('nom', 30);
        $table->string('prenom', 30);
        $table->string('email', 30)->unique();
        $table->string('motDePasse', 255);
        $table->string('telephone', 30)->nullable();
        $table->string('role', 30);
        $table->timestamp('dateCreation')->useCurrent();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
