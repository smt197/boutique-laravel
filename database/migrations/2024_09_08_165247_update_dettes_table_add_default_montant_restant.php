<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDettesTableAddDefaultMontantRestant extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->decimal('montantRestant', 8, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->decimal('montantRestant', 8, 2)->nullable()->change();
        });
    }
}
