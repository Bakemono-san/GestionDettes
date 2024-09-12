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
        Schema::table('article_dette', function (Blueprint $table) {
            // Adding soft deletes column
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_dette', function (Blueprint $table) {
            // Dropping soft deletes column
            $table->dropSoftDeletes();
        });
    }
};
