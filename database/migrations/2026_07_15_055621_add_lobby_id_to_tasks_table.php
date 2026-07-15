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
        Schema::table('tasks', function (Blueprint $table) {
            // Adds the lobby tracking column and sets up cascading deletes
            $table->foreignId('lobby_id')
                  ->after('id') // Adjust this placement to your choice
                  ->constrained()
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['lobby_id']);
            $table->dropColumn('lobby_id');
        });
    }
};
