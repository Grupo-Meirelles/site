<?php
/**
 * Abertura das páginas: <head> e topo fixo com o menu. Usado por index.php e
 * pelas páginas internas (ex.: politica-de-privacidade/index.php).
 *
 * Variáveis esperadas (definidas pela página antes do require):
 *   $pagina['titulo']     <title>
 *   $pagina['descricao']  meta description
 *   $pagina['raiz']       caminho relativo até a raiz do site: '' na home,
 *                         '../' numa página dentro de uma pasta. Prefixa CSS,
 *                         imagens e os links para as seções da home.
 *   $pagina['home']       true só na home: âncoras sem prefixo e link "pular
 *                         para a simulação" (o formulário só existe lá).
 */

declare(strict_types=1);

$raiz = $pagina['raiz'] ?? '';
$home = $pagina['home'] ?? false;
// Âncoras das seções da home: "#produtos" na própria home, "../#produtos" fora dela.
$secao = fn (string $id): string => ($home ? '' : $raiz) . '#' . $id;
$h = fn (string $t): string => htmlspecialchars($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $h($pagina['titulo']) ?></title>
<meta name="description" content="<?= $h($pagina['descricao']) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $raiz ?>css/styles.css">
<link rel="icon" href="img/favicon.webp" type="image/webp">

</head>
<body>

<?php if ($home): ?>
<a class="pular" href="#simulador">Pular para a simulação</a>
<?php else: ?>
<a class="pular" href="#conteudo">Pular para o conteúdo</a>
<?php endif; ?>

<header class="topo" id="topo">
  <div class="topo__interno" id="topoInterno">
    <a class="topo__marca" href="<?= $home ? '#topo' : ($raiz !== '' ? $raiz : './') ?>"><img src="<?= $raiz ?>img/grupo_meirelles_logo.svg" alt="Grupo Meirelles" width="160" height="79"></a>

    <nav class="topo__nav" id="nav" aria-label="Navegação principal">
      <a href="<?= $secao('produtos') ?>">Consignado</a>
      <a href="<?= $secao('depoimentos') ?>">Quem já contratou</a>
      <a href="<?= $secao('sobre') ?>">Sobre nós</a>
      <a class="topo__tel" href="https://wa.me/5561982564974" target="_blank">(61) 98256-4974</a>
    </nav>

    <a class="btn btn--primario topo__cta" href="<?= $secao('simulador') ?>">Simular agora</a>

    <button class="topo__menu" id="menu" type="button" aria-expanded="false" aria-controls="nav" aria-label="Abrir menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

