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

require_once __DIR__ . '/env.php';

$raiz = $pagina['raiz'] ?? '';
$home = $pagina['home'] ?? false;
// Âncoras das seções da home: "#produtos" na própria home, "../#produtos" fora dela.
$secao = fn (string $id): string => ($home ? '' : $raiz) . '#' . $id;
$h = fn (string $t): string => htmlspecialchars($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// Tags de marketing/medição — IDs no .env (ver .env.example). Vazio = tag
// desligada. Cada ID é validado pelo formato, para nada estranho ir parar no HTML.
$idTag = function (string $chave, string $formato): string {
    $valor = trim((string) env($chave, ''));
    return preg_match($formato, $valor) ? $valor : '';
};
$tags = [
    'gtm' => $idTag('GTM_ID', '/^GTM-[A-Z0-9]{4,12}$/'),
    'ga4' => $idTag('GA4_ID', '/^G-[A-Z0-9]{4,15}$/'),
    'metaPixel' => $idTag('META_PIXEL_ID', '/^\d{6,20}$/'),
];
$verificacaoGoogle = $idTag('GOOGLE_SITE_VERIFICATION', '/^[A-Za-z0-9_-]{10,100}$/');
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
<link rel="icon" href="<?= $raiz ?>img/favicon.webp" type="image/webp">
<?php if ($verificacaoGoogle): ?>
<!-- Google Search Console: só verifica a propriedade do site; não grava cookies nem coleta dados. -->
<meta name="google-site-verification" content="<?= $h($verificacaoGoogle) ?>">
<?php endif; ?>
<script>
  // Consentimento de cookies (LGPD) e carregamento das tags.
  //
  // Precisa vir ANTES de qualquer tag. Tudo opcional começa negado (Consent
  // Mode do Google) e NENHUMA tag é baixada antes de a pessoa aceitar:
  //   GA4 ............ só com "Medição"
  //   Meta Pixel ..... só com "Marketing"
  //   GTM ............ com "Medição" ou "Marketing" (as tags dentro dele seguem
  //                    o Consent Mode; configure-as no GTM com as exigências
  //                    de consentimento de cada uma)
  // A escolha fica no cookie gm_consentimento. O aviso e a janela de
  // configuração estão no main.js, que chama gmAplicarConsentimento() quando a
  // pessoa escolhe. Os IDs vêm do .env (GTM_ID, GA4_ID, META_PIXEL_ID).
  window.dataLayer = window.dataLayer || [];
  function gtag() { dataLayer.push(arguments); }
  gtag('consent', 'default', {
    analytics_storage: 'denied', ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied',
    functionality_storage: 'granted', security_storage: 'granted', wait_for_update: 500
  });

  (function () {
    var TAGS = <?= json_encode($tags) ?>;
    var carregadas = {};

    function script(src) {
      var s = document.createElement('script');
      s.async = true; s.src = src;
      document.head.appendChild(s);
    }

    var carregar = {
      gtm: function () {
        dataLayer.push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
        script('https://www.googletagmanager.com/gtm.js?id=' + TAGS.gtm);
      },
      ga4: function () {
        gtag('js', new Date());
        gtag('config', TAGS.ga4);
        script('https://www.googletagmanager.com/gtag/js?id=' + TAGS.ga4);
      },
      metaPixel: function () {
        // Código base do Meta Pixel (equivalente ao snippet oficial).
        var f = window.fbq = function () { f.callMethod ? f.callMethod.apply(f, arguments) : f.queue.push(arguments); };
        if (!window._fbq) window._fbq = f;
        f.push = f; f.loaded = true; f.version = '2.0'; f.queue = [];
        script('https://connect.facebook.net/en_US/fbevents.js');
        fbq('consent', 'grant');
        fbq('init', TAGS.metaPixel);
        fbq('track', 'PageView');
      }
    };

    function uma(nome) {
      if (!TAGS[nome] || carregadas[nome]) return;
      carregadas[nome] = true;
      carregar[nome]();
    }

    // c = { medicao: bool, marketing: bool } — chamado aqui com a escolha
    // salva e pelo main.js quando a pessoa escolhe no aviso.
    window.gmAplicarConsentimento = function (c) {
      c = c || {};
      gtag('consent', 'update', {
        analytics_storage: c.medicao ? 'granted' : 'denied',
        ad_storage: c.marketing ? 'granted' : 'denied',
        ad_user_data: c.marketing ? 'granted' : 'denied',
        ad_personalization: c.marketing ? 'granted' : 'denied'
      });
      if (c.medicao) uma('ga4');
      if (c.marketing) uma('metaPixel');
      if (c.medicao || c.marketing) uma('gtm');
      // Revogou depois de carregar: o Consent Mode já desliga o Google; o Pixel
      // é pausado aqui. O script em si só sai da página no próximo carregamento.
      if (!c.marketing && window.fbq) fbq('consent', 'revoke');
    };

    var m = document.cookie.match(/(?:^|; )gm_consentimento=([^;]*)/);
    try { window.gmConsentimento = m ? JSON.parse(decodeURIComponent(m[1])) : null; } catch (e) { window.gmConsentimento = null; }
    if (window.gmConsentimento) window.gmAplicarConsentimento(window.gmConsentimento);
  })();
</script>
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

