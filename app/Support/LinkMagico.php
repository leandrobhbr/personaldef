<?php

namespace App\Support;

use App\Models\Acesso;
use App\Models\Aluno;
use Illuminate\Support\Str;

/**
 * Link mágico do aplicativo: o Denilson gera um link (e um código de 6 dígitos) por aluno e envia pelo
 * WhatsApp. Ao abrir o link (ou digitar o código no app instalado), o aparelho recebe um token próprio.
 * Link e código valem uma vez só, por 7 dias. No banco ficam só os hashes.
 */
class LinkMagico
{
    public const VALIDADE_DIAS = 7;

    /** @return array{acesso: Acesso, link: string, codigo: string} */
    public static function gerar(?Aluno $aluno, string $perfil = 'ALU'): array
    {
        // token curto (6 letras/números, com ao menos uma letra para não confundir com o código)
        do {
            $token = Str::random(6);
        } while (ctype_digit($token) || self::acessoPendente()->where('ace_link_hash', self::hash($token))->exists());
        do {
            $codigo = (string) random_int(100000, 999999);
        } while (self::acessoPendente()->where('ace_codigo_hash', self::hash($codigo))->exists());

        $acesso = Acesso::create([
            'alu_id' => $aluno?->alu_id,
            'ace_perfil' => $perfil,
            'ace_link_hash' => self::hash($token),
            'ace_codigo_hash' => self::hash($codigo),
            'ace_link_expira' => now()->addDays(self::VALIDADE_DIAS),
        ]);

        return ['acesso' => $acesso, 'link' => rtrim((string) config('aplicativo.url'), '/').'/#/entrar/'.$token, 'codigo' => $codigo];
    }

    /** Troca o token do link ou o código de 6 dígitos pelo token do aparelho; null se inválido, usado ou vencido. */
    public static function trocar(string $tokenOuCodigo, ?string $aparelho): ?array
    {
        $campo = preg_match('/^\d{6}$/', $tokenOuCodigo) ? 'ace_codigo_hash' : 'ace_link_hash';
        $acesso = self::acessoPendente()->where($campo, self::hash($tokenOuCodigo))->first();
        if (! $acesso) {
            return null;
        }

        $tokenAparelho = Str::random(60);
        $acesso->forceFill([
            'ace_link_usado_em' => now(),
            'ace_codigo_hash' => null,
            'ace_token_hash' => self::hash($tokenAparelho),
            'ace_aparelho' => Str::limit((string) $aparelho, 250, ''),
            'ace_ultimo_uso' => now(),
        ])->save();

        return ['acesso' => $acesso, 'token' => $tokenAparelho];
    }

    /** Acesso do personal pela senha fixa: já nasce usado, com o token do aparelho. */
    public static function entrarPersonal(?string $aparelho): array
    {
        $tokenAparelho = Str::random(60);
        $acesso = Acesso::create([
            'ace_perfil' => 'PER',
            'ace_link_hash' => self::hash(Str::random(40)),
            'ace_link_expira' => now(),
            'ace_link_usado_em' => now(),
            'ace_token_hash' => self::hash($tokenAparelho),
            'ace_aparelho' => Str::limit((string) $aparelho, 250, ''),
            'ace_ultimo_uso' => now(),
        ]);

        return ['acesso' => $acesso, 'token' => $tokenAparelho];
    }

    public static function hash(string $valor): string
    {
        return hash_hmac('sha256', $valor, (string) config('app.key'));
    }

    private static function acessoPendente()
    {
        return Acesso::query()->whereNull('ace_link_usado_em')->whereNull('ace_revogado_em')->where('ace_link_expira', '>', now());
    }
}
