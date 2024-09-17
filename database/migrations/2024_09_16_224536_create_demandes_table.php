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
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('dette_id')->nullable(); // Relier à une dette
            $table->decimal('montant', 10, 2);
            $table->json('articles'); // Liste des articles avec la quantité et le prix de vente
            $table->string('status')->default('en attente'); // Statut de la demande
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('dette_id')->references('id')->on('dettes')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
