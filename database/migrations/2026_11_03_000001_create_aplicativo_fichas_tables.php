<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aplicativo de fichas: catálogo e fichas trazidos do 69fit (mesmos nomes de tabelas e colunas,
 * para facilitar a importação) + alunos, telefones e acessos por link mágico.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_musculo', function (Blueprint $table) {
            $table->bigIncrements('mus_id');
            $table->string('mus_musculo', 256);
            $table->text('mus_descricao')->nullable();
            $table->char('mus_situacao', 2)->default('AT');
        });

        Schema::create('tb_exercicio', function (Blueprint $table) {
            $table->bigIncrements('exe_id');
            $table->string('exe_exercicio', 100);
            $table->char('exe_situacao', 2)->default('AT');
            $table->string('exe_video', 256)->nullable();
            $table->string('exe_imagem', 256)->nullable();
            $table->string('exe_gif', 256)->nullable();
            $table->string('exe_audio', 256)->nullable();
            $table->text('exe_explicacao')->nullable();
            $table->foreignId('mus_id')->constrained('tb_musculo', 'mus_id');
        });

        foreach (['tb_metodo' => 'met', 'tb_cadencia' => 'cad', 'tb_pausa' => 'pau'] as $tabela => $p) {
            Schema::create($tabela, function (Blueprint $table) use ($p, $tabela) {
                $table->bigIncrements("{$p}_id");
                $table->string("{$p}_".substr($tabela, 3), 256);
                $table->text("{$p}_descricao")->nullable();
                $table->char("{$p}_situacao", 2)->default('AT');
            });
        }

        Schema::create('tb_aluno', function (Blueprint $table) {
            $table->bigIncrements('alu_id');
            $table->string('alu_nome', 150);
            $table->text('alu_obs')->nullable();
            $table->char('alu_situacao', 2)->default('AT');
            $table->timestamps();
        });

        Schema::create('tb_aluno_telefone', function (Blueprint $table) {
            $table->bigIncrements('tel_id');
            $table->foreignId('alu_id')->constrained('tb_aluno', 'alu_id')->cascadeOnDelete();
            $table->string('tel_numero', 20); // só dígitos, com DDI (5514999999999)
            $table->boolean('tel_principal')->default(false);
            $table->unique(['alu_id', 'tel_numero']);
        });

        // tre_aluno nulo = ficha do Denilson; tre_publico = aparece na área livre
        Schema::create('tb_treino', function (Blueprint $table) {
            $table->bigIncrements('tre_id');
            $table->string('tre_titulo', 256);
            $table->text('tre_obs_professor')->nullable();
            $table->dateTime('tre_data_cadastro');
            $table->char('tre_situacao', 2)->default('AT'); // AT IN
            $table->integer('tre_ordem')->default(1);
            $table->boolean('tre_publico')->default(false);
            $table->foreignId('alu_id')->nullable()->constrained('tb_aluno', 'alu_id')->cascadeOnDelete();
        });

        Schema::create('tb_treino_exercicio', function (Blueprint $table) {
            $table->bigIncrements('tex_id');
            $table->char('tex_situacao', 2)->default('AT');
            $table->integer('tex_ordem')->default(1);
            $table->foreignId('tre_id')->constrained('tb_treino', 'tre_id')->cascadeOnDelete();
            $table->foreignId('exe_id')->constrained('tb_exercicio', 'exe_id');
            $table->foreignId('mus_id')->nullable()->constrained('tb_musculo', 'mus_id');
            $table->foreignId('pau_id')->nullable()->constrained('tb_pausa', 'pau_id');
            $table->foreignId('cad_id')->nullable()->constrained('tb_cadencia', 'cad_id');
            $table->foreignId('met_id')->nullable()->constrained('tb_metodo', 'met_id');
            $table->string('tex_tempo', 10)->nullable();
            $table->string('tex_serie', 10)->nullable();
            $table->string('tex_repeticao', 10)->nullable();
            $table->string('tex_carga', 10)->nullable();
            $table->text('tex_obs')->nullable();
            $table->string('tex_grupo_cor', 10)->nullable();
        });

        // Acesso por link mágico: o link (ou o código de 6 dígitos) é trocado por um token do aparelho
        Schema::create('tb_acesso', function (Blueprint $table) {
            $table->bigIncrements('ace_id');
            $table->foreignId('alu_id')->nullable()->constrained('tb_aluno', 'alu_id')->cascadeOnDelete();
            $table->char('ace_perfil', 3)->default('ALU'); // ALU aluno · PER personal (Denilson)
            $table->string('ace_link_hash', 64)->unique();
            $table->string('ace_codigo_hash', 64)->nullable();
            $table->dateTime('ace_link_expira');
            $table->dateTime('ace_link_usado_em')->nullable();
            $table->string('ace_token_hash', 64)->nullable()->unique();
            $table->string('ace_aparelho', 255)->nullable();
            $table->dateTime('ace_ultimo_uso')->nullable();
            $table->dateTime('ace_revogado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['tb_acesso', 'tb_treino_exercicio', 'tb_treino', 'tb_aluno_telefone', 'tb_aluno', 'tb_pausa', 'tb_cadencia', 'tb_metodo', 'tb_exercicio', 'tb_musculo'] as $tabela) {
            Schema::dropIfExists($tabela);
        }
    }
};
