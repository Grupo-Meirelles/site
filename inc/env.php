<?php
/**
 * Lê a configuração de um arquivo .env (KEY=VALOR, uma por linha).
 *
 * Procura primeiro um nível acima da raiz do site (fora da pasta pública — o
 * lugar mais seguro em produção) e depois na própria raiz. Variáveis de
 * ambiente reais do servidor têm prioridade sobre o arquivo.
 */

declare(strict_types=1);

function env(string $chave, ?string $padrao = null): ?string
{
    static $arquivo = null;

    if ($arquivo === null) {
        $arquivo = [];
        foreach ([dirname(__DIR__, 2) . '/.env', dirname(__DIR__) . '/.env'] as $caminho) {
            if (!is_readable($caminho)) continue;
            foreach (file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $linha) {
                $linha = trim($linha);
                if ($linha === '' || $linha[0] === '#' || !str_contains($linha, '=')) continue;
                [$k, $v] = array_map('trim', explode('=', $linha, 2));
                if (strlen($v) >= 2 && ($v[0] === '"' || $v[0] === "'") && $v[-1] === $v[0]) {
                    $v = substr($v, 1, -1);
                }
                $arquivo[$k] = $v;
            }
            break;
        }
    }

    $real = getenv($chave);
    if ($real !== false && $real !== '') return $real;
    $valor = $arquivo[$chave] ?? '';
    return $valor !== '' ? $valor : $padrao;
}
