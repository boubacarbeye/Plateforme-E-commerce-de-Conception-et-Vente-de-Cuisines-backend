<?php

// database/migrations/2026_01_01_000002_create_materiaux_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materiaux', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom');                         // couleur / finition / poignée / matériau
            $table->enum('type', ['couleur', 'finition', 'poignee', 'materiau']);
            $table->decimal('supplement_prix', 10, 2)->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiaux');
    }
};