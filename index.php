<?php
require __DIR__ . '/inc/avaliacoes.php';
require __DIR__ . '/inc/duvidas.php';
date_default_timezone_set('America/Sao_Paulo');

// Padrões usados se _resumo.json faltar algum campo.
$resumo = resumoAvaliacoes();
$notaMedia = $resumo['nota_media'] ?? 4.9;
$totalAvaliacoes = $resumo['total'] ?? 312;
$urlPerfil = $resumo['url_perfil'] ?? '#';
$avaliacoes = listarAvaliacoes();
$duvidas = listarDuvidas(); // data/duvidas/*.json

$pagina = [
    'titulo' => 'Grupo Meirelles — Empréstimo consignado para servidor público e CLT',
    'descricao' => 'Compra de consignado, portabilidade, refinanciamento e cartão consignado. Simule em 2 minutos, sem consulta ao SPC. Loja física em Águas Claras, Brasília.',
    'raiz' => '',
    'home' => true,
];
require __DIR__ . '/inc/topo.php';
?>
<main>

<section class="hero">
  <video autoplay muted loop playsinline class="hero_bg_video">
        <source src="img/video-background.mp4" type="video/mp4">
        Seu navegador não suporta vídeos de fundo.
  </video>
  <div class="hero__interno">
    <div class="hero__texto">
      <p class="rotulo rotulo--claro">Servidor público e CLT</p>
      <h1>O <span class="hero__destaque">Grupo Meirelles</span><br>
      tem o Consignado<br>
      certo para você.</h1>
      <!-- <p class="hero__sub">Arraste o valor, deixe seu telefone e um especialista compara as propostas de todos os bancos parceiros com as condições reais do seu convênio.</p> -->

      <!-- <dl class="metricas metricas--hero" data-metricas>
        <div class="metrica">
          <dt class="metrica__valor" data-contador data-formato="anos" data-chave="anos_empresa" data-valor="15">+15 anos</dt>
          <dd class="metrica__rotulo">de empresa</dd>
        </div>
        <div class="metrica">
          <dt class="metrica__valor" data-contador data-formato="compacto" data-chave="clientes_atendidos" data-valor="100000">100 mil</dt>
          <dd class="metrica__rotulo">clientes atendidos</dd>
        </div>
        <div class="metrica">
          <dt class="metrica__valor" data-contador data-formato="milhoes" data-chave="valor_liberado" data-valor="500000000">R$ 500 mi</dt>
          <dd class="metrica__rotulo">em valor liberado</dd>
        </div>
      </dl> -->
    </div>

    <div class="hero__form">
      <form class="simulador" id="simulador" novalidate>
        <p class="rotulo">Quanto você precisa</p>
        <output class="simulador__valor" id="valorSaida" for="valor">R$ 25.000</output>

        <input class="simulador__range" type="range" id="valor" name="valor"
               min="2000" max="80000" step="1000" value="25000"
               aria-label="Valor desejado" aria-describedby="valorSaida">
        <div class="simulador__limites"><span>R$ 2 mil</span><span>R$ 80 mil</span></div>

        <!-- <p class="simulador__parcela">
          <span id="prazoSaida">72</span>x de <strong id="parcelaSaida">R$ 610</strong>
          <small>Simulação. A taxa final varia por convênio.</small>
        </p> -->

        <div class="campo">
          <label class="campo__label" for="nome">Nome completo</label>
          <input class="campo__input" type="text" id="nome" name="nome" placeholder="Seu nome completo"
                 autocomplete="name" required minlength="5">
          <span class="campo__erro" id="erroNome" hidden>Informe seu nome completo.</span>
        </div>

        <div class="campo">
          <label class="campo__label" for="telefone">WhatsApp com DDD</label>
          <input class="campo__input" type="tel" id="telefone" name="telefone" placeholder="(61) 90000-0000"
                 autocomplete="tel" inputmode="numeric" maxlength="16" required>
          <span class="campo__erro" id="erroTelefone" hidden>Informe um WhatsApp válido com DDD.</span>
        </div>

        <!-- Honeypot: invisível para pessoas; se vier preenchido, o servidor descarta o envio. -->
        <div class="campo-armadilha" aria-hidden="true">
          <label for="empresa">Empresa</label>
          <input type="text" id="empresa" name="empresa" tabindex="-1" autocomplete="off">
        </div>

        <button class="btn btn--escuro btn--bloco" type="submit" id="enviar">Quero minha simulação</button>
        <p class="simulador__miudas">Simular é gratuito e não consultamos seu SPC.</p>
        <p class="simulador__estado" id="estadoForm" role="status" aria-live="polite" hidden></p>
      </form>
    </div>
  </div>
</section>

<section class="faixa-metricas" aria-label="Nossos números">
  <dl class="metricas metricas--faixa" data-metricas>
    <div class="metrica">
      <dt class="metrica__valor" data-contador data-formato="anos" data-chave="anos_empresa" data-valor="15">+15</dt>
      <dd class="metrica__rotulo">anos de empresa</dd>
    </div>
    <div class="metrica">
      <dt class="metrica__valor" data-contador data-formato="compacto" data-chave="clientes_atendidos" data-valor="100000">100 mil</dt>
      <dd class="metrica__rotulo">clientes atendidos</dd>
    </div>
    <div class="metrica">
      <dt class="metrica__valor" data-contador data-formato="milhoes" data-chave="valor_liberado" data-valor="500000000">R$ 500 mi</dt>
      <dd class="metrica__rotulo">em valor liberado</dd>
    </div>
  </dl>
</section>

<section class="secao" id="produtos">
  <div class="secao__cabecalho">
    <h2>Cinco caminhos para pagar menos juros.</h2>
    <a class="link-seta" href="#como-funciona">Ver como funciona</a>
  </div>

  <ul class="produtos">
    <li class="produto produto--destaque">
      <!-- <p class="rotulo rotulo--claro">Nosso carro-chefe</p> -->
      <h3>Compra de consignado</h3>
      <p>Como a portabilidade, mas somos nós que quitamos a dívida no outro banco.</p>
      <a class="link-seta link-seta--claro" href="#simulador">Simular</a>
    </li>
    <li class="produto">
      <h3>Empréstimo consignado</h3>
      <p>Desconto em folha, sem consulta ao SPC, com as menores taxas do mercado.</p>
      <a class="link-seta" href="#simulador">Simular</a>
    </li>
    <li class="produto">
      <h3>Cartão consignado</h3>
      <p>Limite alto, saque em conta e fatura descontada do contracheque.</p>
      <a class="link-seta" href="#simulador">Simular</a>
    </li>
    <li class="produto">
      <h3>Portabilidade</h3>
      <p>Traga o contrato de outro banco e reduza a parcela sem esticar o prazo.</p>
      <a class="link-seta" href="#simulador">Simular</a>
    </li>
    <li class="produto">
      <h3>Refinanciamento</h3>
      <p>Já pagou parte? Libere troco na conta mantendo a parcela que cabe.</p>
      <a class="link-seta" href="#simulador">Simular</a>
    </li>
  </ul>
</section>

<section class="secao secao--escura" id="como-funciona">
  <div class="passos">
    <div class="passos__titulo">
      <p class="rotulo rotulo--claro">Como funciona</p>
      <h2>Quatro passos. Sem sair de casa.</h2>
    </div>
    <ol class="passos__lista">
      <li><span class="passo__num">01</span><h3>Você simula aqui</h3><p>Arrasta o valor e deixa nome e WhatsApp.</p></li>
      <li><span class="passo__num">02</span><h3>Consultamos a margem</h3><p>Verificamos o convênio e comparamos os bancos.</p></li>
      <li><span class="passo__num">03</span><h3>Você escolhe</h3><p>Taxa, prazo e parcela de cada opção, por escrito.</p></li>
      <li><span class="passo__num">04</span><h3>Dinheiro na conta</h3><p>Assinatura digital e crédito normalmente no mesmo dia a depender da operação.</p></li>
    </ol>
  </div>
</section>

<section class="secao" id="depoimentos">
  <div class="secao__cabecalho">
    <div>
      <p class="rotulo">Quem já contratou</p>
      <h2>Avaliações reais no Google.</h2>
    </div>
    <p class="google-resumo">
      <strong><?= notaFormatada($notaMedia) ?></strong>
      <span class="estrelas" aria-hidden="true"><?= estrelas($notaMedia) ?></span>
      <span class="google-resumo__txt"><span><?= inteiroFormatado($totalAvaliacoes) ?></span> avaliações no Google</span>
      <a class="link-seta" href="<?= e($urlPerfil) ?>" target="_blank" rel="noopener">Ver todas</a>
    </p>
  </div>

<?php if ($avaliacoes): ?>
  <div class="carrossel" role="region" aria-roledescription="carrossel" aria-label="Avaliações de clientes">
    <ul class="avaliacoes" id="avaliacoes" tabindex="0">
<?php foreach ($avaliacoes as $avaliacao): ?>
      <?= cartaoAvaliacao($avaliacao) ?>

<?php endforeach; ?>
    </ul>
    <!-- Ligados pelo main.js; ficam ocultos sem JS ou quando todas cabem na tela. -->
    <div class="carrossel__controles" hidden>
      <button class="carrossel__botao" type="button" data-carrossel="anterior" aria-controls="avaliacoes" aria-label="Avaliações anteriores">←</button>
      <button class="carrossel__botao" type="button" data-carrossel="proximo" aria-controls="avaliacoes" aria-label="Próximas avaliações">→</button>
    </div>
  </div>
<?php else: ?>
  <p class="aviso">Não foi possível carregar as avaliações agora. <a href="<?= e($urlPerfil) ?>" target="_blank" rel="noopener">Veja no Google</a>.</p>
<?php endif; ?>
</section>

<!-- Versão anterior da seção "sobre" (foto ao lado do texto): -->
<section class="sobre" id="sobre">
  <img class="sobre__foto" src="img/diretoria.jpg" alt="Equipe do Grupo Meirelles reunida na loja de Águas Claras" loading="lazy">
  <div class="sobre__texto">
    <p class="rotulo">Sobre nós</p>
    <h2>Gente de verdade</h2>
    <p>O Grupo Meirelles nasceu do vislumbre e da coragem de uma mulher que deixou sua cidade natal para construir uma nova história na capital do país.</p>

    <p>O que começou como um sonho e uma visão de futuro se transformou em uma empresa que hoje reúne mais de 40 pessoas, mantém um escritório aberto de segunda a sexta e já atendeu mais de 13 mil clientes.</p>

    <p>Nosso trabalho é simples: facilitar a transição dos seus contratos de consignado de outros bancos, sempre buscando as melhores ofertas entre nossos bancos parceiros e uma condição que faça sentido para o seu contracheque.</p>

    <p>Mais do que mudar contratos, ajudamos nossos clientes a encontrar oportunidades melhores para o seu dinheiro, com atendimento próximo, clareza e segurança em cada etapa.</p>
    <div class="sobre__acoes">
      <a class="btn btn--primario" href="https://maps.google.com/?q=Av.+das+Araucárias+Águas+Claras+Brasília" target="_blank" rel="noopener">Como chegar</a>
      <a class="btn btn--vazado" href="#simulador">Simular agora</a>
    </div>
  </div>
</section>


<!-- <section class="sobre" id="sobre">
  <div class="sobre__texto">
    <p class="rotulo rotulo--claro">SOBRE NÓS</p>
    <h2>Gente de verdade</h2>
    <p>O Grupo Meirelles nasceu do vislumbre e da coragem de uma mulher que deixou sua cidade natal para construir uma nova história na capital do país.</p>

    <p>O que começou como um sonho e uma visão de futuro se transformou em uma empresa que hoje reúne 42 pessoas, mantém um escritório aberto de segunda a sexta e já atendeu mais de 30 mil clientes.</p>

    <p>Nosso trabalho é simples: facilitar a transição dos seus contratos de consignado de outros bancos, sempre buscando as melhores ofertas entre nossos bancos parceiros e uma condição que faça sentido para o seu contracheque.</p>

    <p>Mais do que mudar contratos, ajudamos nossos clientes a encontrar oportunidades melhores para o seu dinheiro, com atendimento próximo, clareza e segurança em cada etapa.</p>
    <p>O Grupo Meirelles nasceu em Brasília atendendo servidores públicos que estavam cansados de apenas promessas. </p><p>Hoje somos 42 pessoas, com um escritório aberto de segunda a sexta e mais de 30 mil clientes satisfeitos. O trabalho é simples de descrever: comparar as propostas dos bancos parceiros e explicar, sem pressa, qual delas é melhor para o seu contracheque.</p>
    <div class="sobre__acoes">
      <a class="btn btn--branco" href="https://maps.google.com/?q=Av.+das+Araucárias+Águas+Claras+Brasília" target="_blank" rel="noopener">Como chegar</a>
      <a class="btn btn--vazado" href="#simulador">Simular agora</a>
    </div>
  </div>
</section> -->

<?php if ($duvidas): ?>
<section class="secao" id="duvidas">
  <div class="faq">
    <div class="faq__titulo">
      <p class="rotulo">Dúvidas frequentes</p>
      <h2>Antes de assinar, tire estas dúvidas.</h2>
    </div>
    <div class="faq__lista">
<?php foreach ($duvidas as $duvida): ?>
      <?= itemDuvida($duvida) ?>

<?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="cta">
  <h2>Sua margem pode estar parada há anos.</h2>
  <a class="btn btn--branco" href="#simulador">Simular em 2 minutos</a>
</section>

</main>

<?php require __DIR__ . '/inc/rodape.php'; ?>
