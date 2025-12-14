<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'info', 'success', 'warning', 'error'
            $table->string('title');
            $table->text('message');
            $table->boolean('is_global')->default(false); // Para todos los usuarios
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Para usuario específico
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('data')->nullable(); // Datos adicionales
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};