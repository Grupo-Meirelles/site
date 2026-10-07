// Página Consignado CLT: máscaras e validação do formulário de simulação,
// tela de sucesso e barra fixa de "Simular agora".
(function () {
  'use strict';

  var URL_LEADS = '../api/leads-clt.php';
  var form = document.getElementById('form-simulacao');
  var botaoEnviar = form.querySelector('.btn-enviar');
  var estadoEnvio = document.getElementById('estado-envio');
  var sucesso = document.getElementById('sucesso');
  var barra = document.getElementById('barra-fixa');
  var campos = {
    nome: document.getElementById('nome'),
    cpf: document.getElementById('cpf'),
    whats: document.getElementById('whats'),
    aceite: document.getElementById('aceite')
  };

  function digitos(v) {
    return String(v || '').replace(/\D/g, '');
  }

  function mascaraCpf(v) {
    var d = digitos(v).slice(0, 11);
    if (d.length <= 3) return d;
    if (d.length <= 6) return d.slice(0, 3) + '.' + d.slice(3);
    if (d.length <= 9) return d.slice(0, 3) + '.' + d.slice(3, 6) + '.' + d.slice(6);
    return d.slice(0, 3) + '.' + d.slice(3, 6) + '.' + d.slice(6, 9) + '-' + d.slice(9);
  }

  function mascaraFone(v) {
    var d = digitos(v).slice(0, 11);
    if (d.length === 0) return '';
    if (d.length <= 2) return '(' + d;
    if (d.length <= 7) return '(' + d.slice(0, 2) + ') ' + d.slice(2);
    return '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
  }

  function cpfValido(v) {
    var d = digitos(v);
    if (d.length !== 11 || /^(\d)\1{10}$/.test(d)) return false;
    var s = 0, r, i;
    for (i = 0; i < 9; i++) s += Number(d[i]) * (10 - i);
    r = (s * 10) % 11; if (r === 10) r = 0;
    if (r !== Number(d[9])) return false;
    s = 0;
    for (i = 0; i < 10; i++) s += Number(d[i]) * (11 - i);
    r = (s * 10) % 11; if (r === 10) r = 0;
    return r === Number(d[10]);
  }

  function foneValido(v) {
    var d = digitos(v);
    // DDD sem zero (11 a 99) + 9 + 8 dígitos — mesma regra do servidor.
    return /^[1-9][1-9]9\d{8}$/.test(d);
  }

  function nomeValido(v) {
    return v.trim().split(/\s+/).filter(Boolean).length >= 2;
  }

  function mostrarErro(chave, mostrar) {
    document.getElementById('erro-' + chave).hidden = !mostrar;
    if (chave !== 'aceite') campos[chave].setAttribute('aria-invalid', mostrar ? 'true' : 'false');
  }

  // Máscaras enquanto digita; o erro some assim que o campo é editado.
  campos.nome.addEventListener('input', function () { mostrarErro('nome', false); });
  campos.cpf.addEventListener('input', function () {
    campos.cpf.value = mascaraCpf(campos.cpf.value);
    mostrarErro('cpf', false);
  });
  campos.whats.addEventListener('input', function () {
    campos.whats.value = mascaraFone(campos.whats.value);
    mostrarErro('whats', false);
  });
  campos.aceite.addEventListener('change', function () { mostrarErro('aceite', false); });

  form.addEventListener('submit', function (ev) {
    ev.preventDefault();
    var erros = {
      nome: !nomeValido(campos.nome.value),
      cpf: !cpfValido(campos.cpf.value),
      whats: !foneValido(campos.whats.value),
      aceite: !campos.aceite.checked
    };
    var primeiroErro = null;
    Object.keys(erros).forEach(function (chave) {
      mostrarErro(chave, erros[chave]);
      if (erros[chave] && !primeiroErro) primeiroErro = campos[chave];
    });
    if (primeiroErro) { primeiroErro.focus(); return; }

    // api/leads-clt.php salva no banco e repassa nome, CPF e telefone à API.
    var payload = {
      nome: campos.nome.value.trim(),
      cpf: digitos(campos.cpf.value),
      telefone: digitos(campos.whats.value),
      aceite: campos.aceite.checked,
      origem: location.pathname + location.search,
      empresa: document.getElementById('empresa').value // honeypot
    };

    botaoEnviar.disabled = true;
    botaoEnviar.textContent = 'Enviando…';
    estadoEnvio.hidden = true;

    fetch(URL_LEADS, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (r) {
        return r.json().catch(function () { return {}; }).then(function (resposta) {
          if (!r.ok) throw Object.assign(new Error('HTTP ' + r.status), { status: r.status, resposta: resposta });
          return resposta;
        });
      })
      .then(function () {
        document.getElementById('sucesso-nome').textContent = payload.nome.split(/\s+/)[0];
        document.getElementById('sucesso-whats').textContent = campos.whats.value;
        form.hidden = true;
        sucesso.hidden = false;
        // Evento para o pixel / GA4 — o time de tráfego usa isto como conversão.
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event: 'lead_consignado_clt' });
      })
      .catch(function (erro) {
        console.error('[lead-clt]', erro.message);
        var campoServidor = { nome: 'nome', cpf: 'cpf', telefone: 'whats', aceite: 'aceite' };
        var camposComErro = (erro.status === 422 && erro.resposta && erro.resposta.campos) || {};
        Object.keys(camposComErro).forEach(function (chave) {
          if (campoServidor[chave]) mostrarErro(campoServidor[chave], true);
        });
        if (erro.status === 422) return; // os erros já aparecem embaixo de cada campo
        estadoEnvio.textContent = erro.status === 429
          ? erro.resposta.erro
          : 'Não conseguimos enviar agora. Chame no WhatsApp (61) 98256-4974.';
        estadoEnvio.hidden = false;
      })
      .finally(function () {
        botaoEnviar.disabled = false;
        botaoEnviar.textContent = 'Quero minha simulação';
      });
  });

  document.getElementById('nova-simulacao').addEventListener('click', function () {
    form.reset();
    estadoEnvio.hidden = true;
    ['nome', 'cpf', 'whats', 'aceite'].forEach(function (chave) { mostrarErro(chave, false); });
    sucesso.hidden = true;
    form.hidden = false;
    campos.nome.focus();
  });

  // Mostra a barra inferior quando o cabeçalho (80px) sai da tela.
  function checarBarra() {
    barra.hidden = window.scrollY <= 80;
  }
  window.addEventListener('scroll', checarBarra, { passive: true });
  checarBarra();

  // Parallax do hero: o fundo desce numa fração da rolagem, então o conteúdo
  // parece passar mais rápido que a imagem. Depois que o hero sai da tela o
  // valor para de mudar. Desligado com prefers-reduced-motion. (O navegador já
  // dispara "scroll" no máximo uma vez por quadro.)
  var heroImagem = document.querySelector('.hero-imagem');
  var hero = heroImagem && heroImagem.parentElement;
  if (heroImagem && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    var VELOCIDADE_FUNDO = 0.4; // 0 = fundo rola junto com a página; 1 = fundo parado na tela
    var moverFundo = function () {
      var rolado = Math.min(window.scrollY, hero.offsetTop + hero.offsetHeight);
      heroImagem.style.transform = 'translate3d(0,' + (rolado * VELOCIDADE_FUNDO).toFixed(1) + 'px,0)';
    };
    window.addEventListener('scroll', moverFundo, { passive: true });
    moverFundo(); // página aberta já rolada (recarregar no meio)
  }

  // Dúvidas frequentes: mesmo efeito da home (js/main.js). Abre e fecha o
  // <details> animando a altura, com fade na resposta. O clique no <summary> é
  // interceptado para o fechamento também ser animado — o navegador esconderia
  // o conteúdo na hora. Clicar de novo no meio da animação inverte o movimento.
  var menosMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!menosMovimento && 'animate' in document.documentElement) {
    document.querySelectorAll('.faq-item').forEach(function (item) {
      var resumo = item.querySelector('summary');
      var resposta = resumo.nextElementSibling;
      var animacao = null;
      var fade = null;
      var abrindo = false;

      resumo.addEventListener('click', function (e) {
        e.preventDefault();
        abrindo = animacao ? !abrindo : !item.open;

        var alturaInicial = item.offsetHeight;
        var opacidadeInicial = animacao && resposta ? +getComputedStyle(resposta).opacity : (abrindo ? 0 : 1);
        if (animacao) animacao.cancel();
        if (fade) fade.cancel();
        if (abrindo) item.open = true;
        // Fechado = só o <summary> + padding e borda do item (.faq-item tem padding vertical).
        var estilo = getComputedStyle(item);
        var alturaFechada = resumo.offsetHeight +
          parseFloat(estilo.paddingTop) + parseFloat(estilo.paddingBottom) +
          parseFloat(estilo.borderTopWidth) + parseFloat(estilo.borderBottomWidth);
        var alturaFinal = abrindo ? item.offsetHeight : alturaFechada;
        var opcoes = { duration: 300, easing: 'cubic-bezier(.2, .7, .2, 1)' };

        item.style.overflow = 'hidden';
        animacao = item.animate({ height: [alturaInicial + 'px', alturaFinal + 'px'] }, opcoes);
        if (resposta) fade = resposta.animate({ opacity: [opacidadeInicial, abrindo ? 1 : 0] }, opcoes);
        animacao.onfinish = function () {
          if (!abrindo) item.open = false;
          item.style.overflow = '';
          animacao = null;
        };
      });
    });
  }
})();
