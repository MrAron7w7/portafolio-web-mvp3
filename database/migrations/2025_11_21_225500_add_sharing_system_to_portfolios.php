<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sistema de Enlaces Compartidos para Portafolios
     * 
     * Este esquema permite:
     * - Control de acceso granular (propietario, enlace público)
     * - Permisos de enlace (ver, ver y editar)
     * - Restricción por correos autorizados
     * - Tokens seguros no adivinables
     */
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            // Modo de acceso principal: owner_only | link
            $table->enum('access_mode', ['owner_only', 'link'])->default('owner_only')->after('is_public');
            
            // Permiso del enlace: view | view_edit
            $table->enum('link_permission', ['view', 'view_edit'])->default('view')->after('access_mode');
            
            // Tipo de audiencia del enlace: any_with_link | emails_only
            $table->enum('link_audience_type', ['any_with_link', 'emails_only'])->default('any_with_link')->after('link_permission');
            
            // Token seguro para el enlace compartido (UUID)
            $table->string('share_token', 64)->nullable()->unique()->after('link_audience_type');
            
            // Fecha de creación del enlace (opcional, para tracking)
            $table->timestamp('link_created_at')->nullable()->after('share_token');
            
            // Índice para búsqueda rápida por token
            $table->index('share_token');
        });

        // Tabla para correos autorizados cuando link_audience_type = emails_only
        Schema::create('portfolio_authorized_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('invited_by_name')->nullable(); // Nombre de quien invitó
            $table->timestamp('invited_at')->useCurrent();
            $table->timestamp('accessed_at')->nullable(); // Cuándo accedió por primera vez
            $table->timestamps();

            // Evitar duplicados de email por portfolio
            $table->unique(['portfolio_id', 'email']);
            
            // Índice para búsqueda rápida
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_authorized_emails');
        
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropIndex(['share_token']);
            $table->dropColumn([
                'access_mode',
                'link_permission',
                'link_audience_type',
                'share_token',
                'link_created_at',
            ]);
        });
    }
};
