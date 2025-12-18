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
        // Dado que SQLite tiene limitaciones con ALTER TABLE ADD COLUMN NOT NULL
        // y que acordamos limpiar los datos existentes, lo más limpio es recrear la tabla.
        Schema::dropIfExists('community_comments');

        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Nuevas columnas obligatorias
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            
            // Hilos (opcional)
            $table->foreignId('parent_id')->nullable()->constrained('community_comments')->cascadeOnDelete();
            
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_comments');

        // Restaurar esquema anterior si se hace rollback
        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->boolean('is_featured')->default(false);
            $table->string('type')->default('general');
            $table->timestamps();
        });
    }
};
