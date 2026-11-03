<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aparelhos inscritos para receber o aviso de nova versão do aplicativo (Web Push).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_push_assinatura', function (Blueprint $table) {
            $table->bigIncrements('pus_id');
            $table->string('pus_endpoint', 500)->unique();
            $table->string('pus_p256dh', 255);
            $table->string('pus_auth', 255);
            $table->foreignId('ace_id')->nullable()->constrained('tb_acesso', 'ace_id')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_push_assinatura');
    }
};
