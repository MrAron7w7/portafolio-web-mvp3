<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('issuer');
            $table->string('credential_id')->nullable();
            $table->string('credential_url')->nullable();
            $table->date('issued_at');
            $table->date('expires_at')->nullable();
            $table->text('description')->nullable();
            $table->string('certificate_image')->nullable();
            $table->boolean('does_not_expire')->default(false);
            $table->timestamps();
            
            $table->index('portfolio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
