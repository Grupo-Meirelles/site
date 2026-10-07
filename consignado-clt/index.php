<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Empréstimo CLT — Grupo Meirelles</title>
<meta name="description" content="Empréstimo consignado CLT com parcelas descontadas direto do salário. Simule grátis e receba a proposta no WhatsApp.">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="../css/emprestimo-clt.css">
<link rel="icon" href="../img/favicon.webp" type="image/webp">
<script src="../js/emprestimo-clt.js" defer></script>
</head>
<body>
<div class="pagina">

<!-- HERO -->
<section class="hero">
<div aria-hidden="true" class="hero-imagem"></div>
<div aria-hidden="true" class="hero-sombra"></div>
<div class="hero-conteudo">
<div class="hero-texto">
<div class="hero-rotulo">
    <img src="../img/grupo_meirelles_logo.svg" alt="Grupo Meirelles" class="topo-logo">
    <span class="rotulo">EMPRÉSTIMO CONSIGNADO CLT</span>
</div>
<h1 class="hero-titulo"><span class="destaque">Empréstimo CLT</span> rápido para tudo que você precisa</h1>
<p class="hero-subtitulo">Seu empréstimo com as menores taxas de juros e a segurança que você merece.</p>
<div class="hero-acoes">
<a href="#como-funciona" class="btn-contorno">Como funciona</a>
</div>
</div>

<!-- FORM CARD -->
<div id="simular" class="card-form">

<form class="form" id="form-simulacao" novalidate>
<div class="campo">
<span class="form-rotulo">SIMULAÇÃO GRATUITA</span>
</div>

<div class="form-campos">
<div class="campo">
<input id="nome" type="text" autocomplete="name" placeholder="Nome completo" aria-label="Nome completo" class="campo-input">
<span class="erro-campo" id="erro-nome" hidden>Informe nome e sobrenome.</span>
</div>
<div class="campo">
<input id="cpf" type="text" inputmode="numeric" autocomplete="off" placeholder="CPF" aria-label="CPF" class="campo-input">
<span class="erro-campo" id="erro-cpf" hidden>CPF inválido. Confira os números.</span>
</div>
<div class="campo">
<input id="whats" type="tel" inputmode="numeric" autocomplete="tel-national" placeholder="WhatsApp com DDD" aria-label="WhatsApp com DDD" class="campo-input">
<span class="erro-campo" id="erro-whats" hidden>Informe o DDD e o número com 9 dígitos.</span>
</div>
<div class="campo">
<label class="aceite">
<input id="aceite" type="checkbox" class="aceite-checkbox">
<span>Autorizo o contato pelo WhatsApp e o uso dos meus dados para a simulação, conforme a <a href="#politica">Política de Privacidade</a> (LGPD).</span>
</label>
<span class="erro-campo" id="erro-aceite" hidden>É preciso autorizar para continuar.</span>
</div>
</div>

<!-- Honeypot: invisível para pessoas; se vier preenchido, o servidor descarta o envio. -->
<div class="campo-armadilha" aria-hidden="true">
<label for="empresa">Empresa</label>
<input type="text" id="empresa" name="empresa" tabindex="-1" autocomplete="off">
</div>

<button type="submit" class="btn-enviar">Quero minha simulação</button>
<p class="estado-envio" id="estado-envio" role="alert" hidden></p>
</form>

<div class="sucesso" id="sucesso" hidden>
<div class="sucesso-icone">
<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#bcd0ec" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>
</div>
<h2 class="sucesso-titulo">Pronto, <span id="sucesso-nome"></span>!</h2>
<p class="texto">Recebemos seu pedido de simulação. Um especialista vai chamar você no WhatsApp <strong class="texto-forte" id="sucesso-whats"></strong> em breve.</p>
<button type="button" class="btn-secundario" id="nova-simulacao">Fazer outra simulação</button>
</div>

</div>
</div>
</section>

<!-- COMO FUNCIONA -->
<section id="como-funciona" class="secao-como-funciona">
<div class="secao-conteudo">
<div class="secao-cabecalho">
<span class="rotulo">COMO FUNCIONA</span>
<h2 class="titulo-secao">Três passos até o dinheiro na conta</h2>
</div>
<div class="grade-passos">
<div class="passo">
<span class="passo-numero">01</span>
<h3 class="passo-titulo">Preencha seus dados</h3>
<p class="texto">Nome, CPF e WhatsApp. Só isso para começar a simulação.</p>
</div>
<div class="passo">
<span class="passo-numero">02</span>
<h3 class="passo-titulo">Receba a proposta no WhatsApp</h3>
<p class="texto">O banco consulta sua margem e envia o valor liberado, parcela, taxa e CET antes de qualquer contratação.</p>
</div>
<div class="passo">
<span class="passo-numero">03</span>
<h3 class="passo-titulo">Dinheiro na conta</h3>
<p class="texto">Aprovado e assinado, o valor cai na sua conta e as parcelas são descontadas direto do salário.</p>
</div>
</div>
</div>
</section>

<!-- VANTAGENS -->
<section id="vantagens">
<div class="secao-conteudo">
<div class="secao-cabecalho">
<span class="rotulo">VANTAGENS</span>
<h2 class="titulo-secao">Crédito com a segurança</h2>
</div>
<div class="grade-vantagens">
<div class="vantagem">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#7da3d9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 17l6-6 4 4 8-8"></path><path d="M14 7h7v7"></path></svg>
<h3 class="vantagem-titulo">Taxas menores</h3>
<p class="vantagem-texto">Por ter desconto em folha, o banco tem garantia de pagamento e por isso as taxas são menores.</p>
</div>
<div class="vantagem">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#7da3d9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18"></path><path d="M7 15h4"></path></svg>
<h3 class="vantagem-titulo">Desconto em folha</h3>
<p class="vantagem-texto">A parcela sai direto do salário. Sem boleto, sem atraso, sem juros por esquecimento.</p>
</div>
<div class="vantagem">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#7da3d9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 4v5c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V7l8-4z"></path><path d="M9 12l2 2 4-4"></path></svg>
<h3 class="vantagem-titulo">Facilidade de contratação</h3>
<p class="vantagem-texto">O processo de contratação é 100% online.</p>
</div>
<div class="vantagem">
<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#7da3d9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12z"></path></svg>
<h3 class="vantagem-titulo">Aprovação mais simples</h3>
<p class="vantagem-texto">A análise de crédito é mais rápida e com menos exigências, especialmente para quem tem o nome negativado.</p>
</div>
</div>
</div>
</section>

<!-- QUEM PODE -->
<section class="secao-quem-pode">
<div class="quem-pode-conteudo">
<div class="quem-pode-cabecalho">
<span class="rotulo">QUEM PODE CONTRATAR</span>
<h2 class="titulo-secao">Tem carteira assinada? Você pode simular.</h2>
</div>
<div class="requisitos">
<div class="requisito">
<span class="requisito-numero">01</span>
<p class="texto-grande">Trabalhadores com carteira assinada (CLT), incluindo empregados domésticos e rurais.</p>
</div>
<div class="requisito">
<span class="requisito-numero">02</span>
<p class="texto-grande">Margem consignável disponível: a soma das parcelas respeita o limite legal sobre o salário.</p>
</div>
<div class="requisito">
<span class="requisito-numero">03</span>
<p class="texto-grande">CPF regular e maior de 18 anos. A aprovação depende de análise de crédito.</p>
</div>
</div>
</div>
</section>

<!-- CTA BAND -->
<section class="secao-cta">
<div class="cta">
<div class="cta-faixa"></div>
<div class="cta-texto">
<h2 class="cta-titulo">Dívida cara no cartão ou no cheque especial?</h2>
<p class="texto-grande">Use o consignado CLT para quitar e pagar menos juros por mês.</p>
</div>
<a href="#simular" class="btn-cta">Simular agora</a>
</div>
</section>

<!-- FAQ -->
<section id="duvidas" class="secao-duvidas">
<div class="duvidas-conteudo">
<div class="duvidas-cabecalho">
<span class="rotulo">DÚVIDAS FREQUENTES</span>
<h2 class="titulo-secao">Perguntas que todo mundo faz</h2>
</div>
<div class="faq-lista">
<details class="faq-item">
<summary class="faq-pergunta">O que é o empréstimo consignado CLT?</summary>
<p class="faq-resposta">É um empréstimo para trabalhadores com carteira assinada em que as parcelas são descontadas direto do salário. Como o risco é menor, as taxas tendem a ser mais baixas que as de outras modalidades.</p>
</details>
<details class="faq-item">
<summary class="faq-pergunta">Minha empresa fica sabendo?</summary>
<p class="faq-resposta">O desconto aparece na folha de pagamento, então o empregador é informado sobre a parcela. Detalhes como o motivo do empréstimo não são compartilhados.</p>
</details>
<details class="faq-item">
<summary class="faq-pergunta">E se eu sair da empresa?</summary>
<p class="faq-resposta">Em caso de desligamento, as verbas rescisórias e a garantia do FGTS, quando contratada, podem ser usadas para abater o saldo. O restante pode seguir no novo emprego ou ser renegociado.</p>
</details>
<details class="faq-item">
<summary class="faq-pergunta">Simular tem algum custo?</summary>
<p class="faq-resposta">Não. A simulação é gratuita e sem compromisso. Você só contrata se a proposta fizer sentido para você.</p>
</details>
</div>
</div>
</section>

<!-- FOOTER -->
<footer>
<div class="rodape-conteudo">
<div class="rodape-topo">
<img src="../img/grupo_meirelles_logo.svg" alt="Grupo Meirelles" class="rodape-logo">
<div class="rodape-links">
<a id="politica" href="/politica-de-privacidade" class="rodape-link">Política de Privacidade</a>
<!-- <a href="#termos" class="rodape-link">Termos de Uso</a>
<a href="#ouvidoria" class="rodape-link">Ouvidoria</a> -->
</div>
</div>
<div class="rodape-divisor"></div>
<p class="rodape-aviso">LINK SERVIÇOS DE INFORMAÇÕES CADASTRAIS LTDA · CNPJ 20.200.080/0001-36 · Av. das Araucárias, Sala 332 Águas Claras, Brasília – DF. Atuamos na intermediação de soluções financeiras, conectando clientes interessados a instituições financeiras e parceiros comerciais, conforme as características e condições de cada operação, nos termos da Resolução CMN nº 4.935/2021. Crédito sujeito a análise e aprovação. Taxa de juros, Custo Efetivo Total (CET) e valor das parcelas são informados antes da contratação. Não cobramos nenhum valor antecipado para liberar empréstimo.</p>
<p class="rodape-copyright">© 2026 Grupo Meirelles. Todos os direitos reservados.</p>
</div>
<div class="rodape-espaco"></div>
</footer>

<!-- BARRA FIXA -->
<div class="barra-fixa" id="barra-fixa" hidden>
<div class="barra-fixa-conteudo">
<span class="barra-fixa-texto">Simule seu <span class="destaque">Empréstimo CLT</span> grátis</span>
<a href="#simular" class="btn-simular">Simular agora</a>
</div>
</div>

</div>
</body>
</html>
