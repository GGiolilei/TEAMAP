<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_id')->unique(); // Custom string/UUID report identifier
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_user_id')->constrained('users')->onDelete('cascade');
            $table->text('reason'); // Note explaining why they are reported
            $table->enum('status', ['pending', 'resolved', 'dismissed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};