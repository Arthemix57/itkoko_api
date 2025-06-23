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
        Schema::create('publicites', function (Blueprint $table) {
            $table->id();
            $table->string('libelle'); // Publicite name
            $table->text('description')->nullable();
            $table->datetime('date_debut'); // Start date of the advertisement
            $table->datetime('date_fin'); // End date of the advertisement
            $table->boolean('is_active')->default(true); //
            $table->string('image'); // Image URL or path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicites');
    }
};
