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
        Schema::table('dettes', function (Blueprint $table) {
            $table->dropColumn('montantVerser');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->decimal('montantVerser', 8, 2);
        });
    }
};
