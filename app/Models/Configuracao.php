<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Armazena configurações da plataforma no formato chave-valor.
 * Valores sensíveis (senhas) ficam cifrados no banco.
 */
class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = ['chave', 'valor'];

    /** Chaves cujo valor é armazenado cifrado. */
    public const SECRETAS = ['smtp_password', 'jitsi_app_secret'];

    /** Lê o valor de uma configuração (decifra se for sensível). */
    public static function valor(string $chave, mixed $default = null): mixed
    {
        $registro = static::query()->where('chave', $chave)->first();

        if (! $registro || $registro->valor === null) {
            return $default;
        }

        if (in_array($chave, self::SECRETAS, true)) {
            try {
                return Crypt::decryptString($registro->valor);
            } catch (\Throwable) {
                return $default;
            }
        }

        return $registro->valor;
    }

    /** Grava/atualiza uma configuração (cifra se for sensível). */
    public static function definir(string $chave, mixed $valor): void
    {
        if ($valor !== null && $valor !== '' && in_array($chave, self::SECRETAS, true)) {
            $valor = Crypt::encryptString((string) $valor);
        }

        static::query()->updateOrCreate(['chave' => $chave], ['valor' => $valor]);
    }

    /** Retorna um mapa chave => valor para a lista de chaves informada. */
    public static function mapa(array $chaves): array
    {
        $saida = [];
        foreach ($chaves as $chave) {
            $saida[$chave] = static::valor($chave);
        }

        return $saida;
    }
}
