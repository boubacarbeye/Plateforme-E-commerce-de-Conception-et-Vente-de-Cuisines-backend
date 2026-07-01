<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('module_produits', function (Blueprint $table) {
            $table->string('model_3d_url')->nullable()->after('image_url');
        });
    }

    public function down(): void
    {
        Schema::table('module_produits', function (Blueprint $table) {
            $table->dropColumn('model_3d_url');
        });
    }
};