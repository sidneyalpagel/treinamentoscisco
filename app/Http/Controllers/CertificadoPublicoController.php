<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificadoPublicoController extends Controller
{
    /**
     * Exibe o certificado (versão para impressão / PDF via navegador).
     */
    public function mostrar(Certificado $certificado): View
    {
        $certificado->load('inscricao.treinamento');

        return view('public.certificado', compact('certificado'));
    }

    /**
     * Página pública de validação de certificado por código.
     */
    public function validar(Request $request): View
    {
        $codigo = trim((string) $request->query('codigo', ''));
        $certificado = null;
        $buscou = $request->has('codigo') && $codigo !== '';

        if ($buscou) {
            $certificado = Certificado::with('inscricao.treinamento')
                ->where('codigo', strtoupper($codigo))
                ->first();
        }

        return view('public.validar', compact('certificado', 'codigo', 'buscou'));
    }
}
