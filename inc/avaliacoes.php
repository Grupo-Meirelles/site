<?php
/**
 * Avaliações do Google — mantidas à mão, uma por arquivo, em data/avaliacoes/.
 *
 * A pasta é lida a cada request: para adicionar uma avaliação basta soltar um
 * novo .json lá (ex.: 07-fulano.json). A ordem de exibição segue o nome do arquivo.
 * Todas as que passam no filtro entram no carrossel da seção de avaliações.
 *
 * Arquivos que começam com "_" não são avaliações:
 *   _resumo.json  -> nota_media / total / url_perfil do perfil do Google
 *   _modelo.json  -> exemplo de preenchimento, ignorado
 */

declare(strict_types=1);

const AVALIACOES_DIR = __DIR__ . '/../data/avaliacoes';
const AVALIACOES_NOTA_MINIMA = 4;

function lerJson(string $arquivo): ?array
{
    $conteudo = @file_get_contents($arquivo);
    $dados = $conteudo === false ? null : json_decode($conteudo, true);
    if (!is_array($dados)) {
        error_log('[avaliacoes] JSON inválido ou ilegível: ' . basename($arquivo));
        return null;
    }
    return $dados;
}

/** Números gerais do perfil. Chaves ausentes ficam null e o HTML usa o padrão. */
function resumoAvaliacoes(): array
{
    $dados = lerJson(AVALIACOES_DIR . '/_resumo.json') ?? [];
    return [
        'nota_media' => is_numeric($dados['nota_media'] ?? null) ? (float) $dados['nota_media'] : null,
        'total' => is_numeric($dados['total'] ?? null) ? (int) $dados['total'] : null,
        'url_perfil' => is_string($dados['url_perfil'] ?? null) ? $dados['url_perfil'] : null,
    ];
}

/** Avaliações exibíveis: nota >= 4 e com texto, na ordem do nome do arquivo. */
function listarAvaliacoes(): array
{
    $arquivos = glob(AVALIACOES_DIR . '/*.json') ?: [];
    sort($arquivos);

    $avaliacoes = [];
    foreach ($arquivos as $arquivo) {
        if (basename($arquivo)[0] === '_') continue;

        $a = lerJson($arquivo);
        if ($a === null) continue;
        if (empty($a['autor']) || empty($a['texto']) || !is_numeric($a['nota'] ?? null)) {
            error_log('[avaliacoes] faltam autor/nota/texto: ' . basename($arquivo));
            continue;
        }
        if ($a['nota'] < AVALIACOES_NOTA_MINIMA) continue;

        $avaliacoes[] = $a;
    }
    return $avaliacoes;
}

/* ------------------------------------------------------------ formatação -- */

function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function estrelas(float $nota): string
{
    $cheias = (int) round($nota);
    return str_repeat('★', $cheias) . str_repeat('☆', 5 - $cheias);
}

function notaFormatada(float $nota): string
{
    return number_format($nota, 1, ',', '.');
}

function inteiroFormatado(int $valor): string
{
    return number_format($valor, 0, ',', '.');
}

function dataRelativa(?string $iso): string
{
    if (!$iso || ($momento = strtotime($iso)) === false) return '';
    $dias = intdiv(time() - $momento, 86400);
    if ($dias < 1) return 'hoje';
    if ($dias < 30) return 'há ' . $dias . ($dias === 1 ? ' dia' : ' dias');
    $meses = intdiv($dias, 30);
    if ($meses < 12) return 'há ' . $meses . ($meses === 1 ? ' mês' : ' meses');
    $anos = intdiv($meses, 12);
    return 'há ' . $anos . ($anos === 1 ? ' ano' : ' anos');
}

function iniciais(string $nome): string
{
    $partes = array_slice(preg_split('/\s+/u', trim($nome)) ?: [], 0, 2);
    return mb_strtoupper(implode('', array_map(fn ($p) => mb_substr($p, 0, 1), $partes)));
}

function cartaoAvaliacao(array $a): string
{
    $nota = (float) $a['nota'];
    $meta = implode(' · ', array_filter([$a['vinculo'] ?? '', dataRelativa($a['data'] ?? null)]));

    $foto = !empty($a['foto'])
        ? '<img class="avaliacao__foto" src="' . e($a['foto']) . '" alt="" loading="lazy" width="36" height="36">'
        : '<span class="avaliacao__foto" style="display:grid;place-content:center;font:600 12px Inter,sans-serif;color:#1d3557">' . e(iniciais($a['autor'])) . '</span>';

    return '<li class="avaliacao">'
        . '<div class="avaliacao__estrelas" aria-label="' . e((string) $a['nota']) . ' de 5 estrelas">' . estrelas($nota) . '</div>'
        . '<p class="avaliacao__texto">“' . e($a['texto']) . '”</p>'
        . '<div class="avaliacao__autor">' . $foto
        . '<div><div class="avaliacao__nome">' . e($a['autor']) . '</div>'
        . '<div class="avaliacao__meta">' . e($meta) . '</div></div>'
        . '</div>'
        . '</li>';
}
