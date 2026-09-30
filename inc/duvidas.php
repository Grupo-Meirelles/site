<?php
/**
 * Dúvidas frequentes — mantidas à mão, uma por arquivo, em data/duvidas/.
 *
 * A pasta é lida a cada request: para adicionar uma pergunta basta soltar um
 * novo .json lá (ex.: 07-minha-pergunta.json); para remover, apague o arquivo.
 * A ordem de exibição segue o nome do arquivo. Arquivos que começam com "_"
 * são ignorados (_modelo.json é o exemplo de preenchimento).
 *
 * Formato: { "pergunta": "...", "resposta": "..." } — texto simples, sem HTML.
 */

declare(strict_types=1);

const DUVIDAS_DIR = __DIR__ . '/../data/duvidas';

/** Perguntas exibíveis (com pergunta e resposta), na ordem do nome do arquivo. */
function listarDuvidas(): array
{
    $arquivos = glob(DUVIDAS_DIR . '/*.json') ?: [];
    sort($arquivos);

    $duvidas = [];
    foreach ($arquivos as $arquivo) {
        if (basename($arquivo)[0] === '_') continue;

        $conteudo = @file_get_contents($arquivo);
        $d = $conteudo === false ? null : json_decode($conteudo, true);
        if (!is_array($d)) {
            error_log('[duvidas] JSON inválido ou ilegível: ' . basename($arquivo));
            continue;
        }
        $pergunta = trim((string) ($d['pergunta'] ?? ''));
        $resposta = trim((string) ($d['resposta'] ?? ''));
        if ($pergunta === '' || $resposta === '') {
            error_log('[duvidas] faltam pergunta/resposta: ' . basename($arquivo));
            continue;
        }
        $duvidas[] = ['pergunta' => $pergunta, 'resposta' => $resposta];
    }
    return $duvidas;
}

function itemDuvida(array $d): string
{
    $esc = fn (string $t) => htmlspecialchars($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return '<details class="faq__item"><summary>' . $esc($d['pergunta']) . '</summary>'
        . '<p>' . $esc($d['resposta']) . '</p></details>';
}
