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
        Schema::create('article_dette', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('dette_id')->constrained()->onDelete('cascade');
            $table->integer('quantite');
            $table->decimal('prixVente',10,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_dette');
    }
};
