/* ==========================================================================
   Grupo Meirelles — script principal
   Sem dependências. Carregado com defer.

   Endpoints esperados (ver README.md para o contrato completo):
     GET  API.metricas -> { anos_empresa, clientes_atendidos, valor_liberado }
     POST API.leads     -> { nome, telefone, valor, prazo, origem, empresa }

   Avaliações do Google: renderizadas no servidor por inc/avaliacoes.php a
   partir de data/avaliacoes/*.json — ver README.md.
   ========================================================================== */

(function () {
  'use strict';

  var API = {
    metricas: '/api/metricas',
    leads: 'api/leads.php'
  };

  // Fixture usado quando a API não responde (ambiente local, homologação sem
  // back-end, ou indisponibilidade momentânea).
  var EXEMPLO = {
    metricas: 'data/metricas.example.json'
  };

  // Tenta a API; se falhar, cai para o fixture equivalente.
  function buscarJson(url, urlReserva) {
    function pegar(u) {
      return fetch(u, { headers: { Accept: 'application/json' } }).then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status + ' em ' + u);
        return r.json();
      });
    }
    return pegar(url).catch(function (erro) {
      console.warn('[api]', erro.message, '— usando dados de exemplo');
      return pegar(urlReserva);
    });
  }

  var MENOS_MOVIMENTO = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Seleciona o elemento do menu
const menu = document.getElementById('topoInterno');
// Escuta o evento de rolagem da página
window.addEventListener('scroll', () => {
    // Verifica se a rolagem passou de 50 pixels
    if (window.scrollY > 50) {
        menu.classList.add('rollon'); // Adiciona a nova classe
    } else {
        menu.classList.remove('rollon'); // Remove se voltar ao topo
    }
});

  /* ------------------------------------------------------ formatadores -- */

  var moeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });
  var inteiro = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 });
  var umaCasa = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

  // Formata o número de acordo com data-formato do elemento contador.
  function formatar(valor, formato) {
    switch (formato) {
      case 'anos':
        return '+' + inteiro.format(Math.round(valor));
      case 'compacto':
        // 100000 -> "100 mil"; 1250000 -> "1,2 mi"
        if (valor >= 1e6) return umaCasa.format(valor / 1e6).replace(',0', '') + ' mi';
        if (valor >= 1000) return inteiro.format(Math.round(valor / 1000)) + ' mil';
        return inteiro.format(Math.round(valor));
      case 'milhoes':
        if (valor >= 1e9) return 'R$ ' + umaCasa.format(valor / 1e9).replace(',0', '') + ' bi';
        return 'R$ ' + inteiro.format(Math.round(valor / 1e6)) + ' mi';
      case 'nota':
        return umaCasa.format(valor) + ' ★';
      case 'moeda':
        return moeda.format(valor);
      default:
        return inteiro.format(Math.round(valor));
    }
  }

  /* --------------------------------------------------------- contadores -- */

  // Anima de 0 até o valor final quando o elemento entra na viewport.
  // Respeita prefers-reduced-motion (escreve o valor final direto).
  var DURACAO = 1600;

  function easeOutCubic(t) { return 1 - Math.pow(1 - t, 3); }

  function animar(el) {
    var alvo = parseFloat(el.dataset.valor);
    var formato = el.dataset.formato;
    if (isNaN(alvo)) return;

    function concluir() {
      el.textContent = formatar(alvo, formato);
      el.removeAttribute('data-animando');
      clearTimeout(el._rede);
    }

    if (MENOS_MOVIMENTO) { concluir(); return; }

    // Rede de segurança: aconteça o que acontecer com o rAF (aba oculta, throttle,
    // erro), o número final é escrito. O contador nunca fica parado em zero.
    clearTimeout(el._rede);
    el._rede = setTimeout(concluir, DURACAO + 400);

    el.setAttribute('data-animando', '');
    var inicio = null;

    function passo(agora) {
      if (inicio === null) {
        inicio = agora;
        el.textContent = formatar(0, formato); // zera só quando a animação começa
      }
      var progresso = Math.min((agora - inicio) / DURACAO, 1);
      el.textContent = formatar(alvo * easeOutCubic(progresso), formato);
      if (progresso < 1) requestAnimationFrame(passo);
      else concluir();
    }

    requestAnimationFrame(passo);
  }

  // Os valores estáticos do HTML permanecem no DOM até a animação de fato iniciar.
  var contadores = Array.prototype.slice.call(document.querySelectorAll('[data-contador]'));

  var observador = null;

  if ('IntersectionObserver' in window) {
    observador = new IntersectionObserver(function (entradas) {
      entradas.forEach(function (entrada) {
        if (!entrada.isIntersecting) return;
        animar(entrada.target);
        observador.unobserve(entrada.target);
      });
    }, { threshold: .4 });

    contadores.forEach(function (el) { observador.observe(el); });
  } else {
    contadores.forEach(animar);
  }

  // Atualiza o alvo de um contador já observado (ou já animado) e reanima.
  function atualizarContador(el, novoValor) {
    el.dataset.valor = novoValor;
    var visivel = el.getBoundingClientRect().top < window.innerHeight && el.getBoundingClientRect().bottom > 0;
    if (visivel || !observador) {
      animar(el);
      if (observador) observador.unobserve(el);
    }
  }

  /* ------------------------------------------------- métricas (API) ------ */

  function carregarMetricas() {
    buscarJson(API.metricas, EXEMPLO.metricas)
      .then(function (dados) {
        contadores.forEach(function (el) {
          if (el.dataset.fonte === 'avaliacoes') return;
          var chave = el.dataset.chave;
          if (chave && typeof dados[chave] === 'number') atualizarContador(el, dados[chave]);
        });
      })
      .catch(function (erro) {
        // Último recurso: mantém os valores estáticos do HTML.
        console.warn('[metricas] usando valores estáticos:', erro.message);
      });
  }

  /* --------------------------------------------------------- simulador --- */

  var PRAZO = 72;          // meses usados na estimativa exibida
  var TAXA_MES = 0.0172;   // taxa de referência; ajustar conforme convênio

  function parcela(valor, meses, taxa) {
    return valor * (taxa * Math.pow(1 + taxa, meses)) / (Math.pow(1 + taxa, meses) - 1);
  }

  var campoValor = document.getElementById('valor');
  var saidaValor = document.getElementById('valorSaida');
  var saidaParcela = document.getElementById('parcelaSaida');
  var saidaPrazo = document.getElementById('prazoSaida');

  function atualizarSimulacao() {
    if (!campoValor) return;
    var v = Number(campoValor.value);
    // Cada saída é opcional: o bloco da parcela pode estar fora do HTML.
    if (saidaValor) saidaValor.textContent = moeda.format(v);
    if (saidaParcela) saidaParcela.textContent = moeda.format(parcela(v, PRAZO, TAXA_MES));
    if (saidaPrazo) saidaPrazo.textContent = PRAZO;
  }

  if (campoValor) {
    campoValor.addEventListener('input', atualizarSimulacao);
    atualizarSimulacao();
  }

  /* ------------------------------------------------ formulário de lead --- */

  var form = document.getElementById('simulador');
  var campoNome = document.getElementById('nome');
  var campoTel = document.getElementById('telefone');
  var estado = document.getElementById('estadoForm');
  var botao = document.getElementById('enviar');

  function mascaraTelefone(valor) {
    var d = valor.replace(/\D/g, '').slice(0, 11);
    if (d.length <= 2) return d.length ? '(' + d : '';
    if (d.length <= 6) return '(' + d.slice(0, 2) + ') ' + d.slice(2);
    if (d.length <= 10) return '(' + d.slice(0, 2) + ') ' + d.slice(2, 6) + '-' + d.slice(6);
    return '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
  }

  if (campoTel) {
    campoTel.addEventListener('input', function () {
      campoTel.value = mascaraTelefone(campoTel.value);
    });
  }

  function marcarErro(campo, idErro, invalido) {
    var erro = document.getElementById(idErro);
    campo.setAttribute('aria-invalid', invalido ? 'true' : 'false');
    if (erro) erro.hidden = !invalido;
    return !invalido;
  }

  function validar() {
    var nomeOk = marcarErro(campoNome, 'erroNome', campoNome.value.trim().split(/\s+/).length < 2);
    var digitos = campoTel.value.replace(/\D/g, '');
    var telOk = marcarErro(campoTel, 'erroTelefone', digitos.length < 10);
    return nomeOk && telOk;
  }

  function mostrarEstado(mensagem, tipo) {
    if (!estado) return;
    estado.textContent = mensagem;
    estado.dataset.tipo = tipo;
    estado.hidden = false;
  }

  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!validar()) {
        mostrarEstado('Confira os campos destacados.', 'erro');
        return;
      }

      var payload = {
        nome: campoNome.value.trim(),
        telefone: campoTel.value.replace(/\D/g, ''),
        valor: Number(campoValor.value),
        prazo: PRAZO,
        origem: location.pathname + location.search,
        empresa: document.getElementById('empresa').value // honeypot
      };

      botao.disabled = true;
      botao.textContent = 'Enviando…';
      estado.hidden = true;

      fetch(API.leads, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
        .then(function (r) {
          return r.json().catch(function () { return {}; }).then(function (resposta) {
            if (r.status === 422 && resposta.campos) {
              marcarErro(campoNome, 'erroNome', !!resposta.campos.nome);
              marcarErro(campoTel, 'erroTelefone', !!resposta.campos.telefone);
            }
            if (!r.ok) throw Object.assign(new Error('HTTP ' + r.status), { status: r.status, resposta: resposta });
            return resposta;
          });
        })
        .then(function () {
          form.reset();
          atualizarSimulacao();
          mostrarEstado('Recebemos seus dados. Um especialista entra em contato pelo WhatsApp.', 'ok');
          // Evento para o pixel / GA4 — o time de tráfego usa isto como conversão.
          window.dataLayer = window.dataLayer || [];
          window.dataLayer.push({ event: 'lead_simulacao', valor: payload.valor });
        })
        .catch(function (erro) {
          console.error('[lead]', erro.message);
          if (erro.status === 422 || erro.status === 429) {
            mostrarEstado(erro.resposta.erro, 'erro');
            return;
          }
          mostrarEstado('Não conseguimos enviar agora. Chame no WhatsApp (61) 98256-4974.', 'erro');
        })
        .finally(function () {
          botao.disabled = false;
          botao.textContent = 'Quero minha simulação';
        });
    });
  }

  /* -------------------------------------------------- menu e CTA fixa ---- */

  var botaoMenu = document.getElementById('menu');
  var nav = document.getElementById('nav');

  if (botaoMenu && nav) {
    var alternarMenu = function (abrir) {
      if (abrir) nav.setAttribute('data-aberto', '');
      else nav.removeAttribute('data-aberto');
      botaoMenu.setAttribute('aria-expanded', String(abrir));
      botaoMenu.setAttribute('aria-label', abrir ? 'Fechar menu' : 'Abrir menu');
      // Painel de tela cheia: trava a rolagem da página enquanto está aberto.
      document.documentElement.style.overflow = abrir ? 'hidden' : '';
    };

    botaoMenu.addEventListener('click', function () {
      alternarMenu(!nav.hasAttribute('data-aberto'));
    });

    nav.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') alternarMenu(false);
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.hasAttribute('data-aberto')) {
        alternarMenu(false);
        botaoMenu.focus();
      }
    });

    // Voltou para o desktop com o menu aberto: fecha e destrava a rolagem.
    window.matchMedia('(min-width: 900px)').addEventListener('change', function (m) {
      if (m.matches) alternarMenu(false);
    });
  }

  // Topo fixo: ganha fundo assim que a página sai do início.
  var topo = document.getElementById('topo');
  if (topo) {
    var marcarRolagem = function () {
      if (window.scrollY > 8) topo.setAttribute('data-rolado', '');
      else topo.removeAttribute('data-rolado');
    };
    window.addEventListener('scroll', marcarRolagem, { passive: true });
    marcarRolagem(); // página aberta já rolada (recarregar, link com #âncora)
  }

  // Dúvidas frequentes: abre e fecha o <details> animando a altura (e um fade
  // na resposta). O clique no <summary> é interceptado para o fechamento
  // também ser animado — o navegador esconderia o conteúdo na hora. Clicar de
  // novo no meio da animação inverte o movimento a partir da altura atual.
  if (!MENOS_MOVIMENTO && 'animate' in document.documentElement) {
    document.querySelectorAll('.faq__item').forEach(function (item) {
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
        var borda = item.offsetHeight - item.clientHeight;
        var alturaFinal = abrindo ? item.offsetHeight : resumo.offsetHeight + borda;
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

  // Carrossel de avaliações: a rolagem é nativa (CSS scroll-snap); os botões
  // avançam/voltam uma "página" e desligam nas pontas. Sem sobra para rolar
  // (poucas avaliações), os botões somem.
  //
  // Autoplay: avança uma página a cada AUTOPLAY_MS e, na última, volta ao
  // início. Pausa com o mouse em cima, com foco de teclado dentro, com a seção
  // fora da tela ou a aba em segundo plano. Qualquer rolagem (dedo, setas)
  // reinicia a contagem. Com prefers-reduced-motion o autoplay não liga.
  var carrossel = document.querySelector('.carrossel');
  if (carrossel) {
    var faixa = carrossel.querySelector('.avaliacoes');
    var controles = carrossel.querySelector('.carrossel__controles');
    var anterior = carrossel.querySelector('[data-carrossel="anterior"]');
    var proximo = carrossel.querySelector('[data-carrossel="proximo"]');
    var AUTOPLAY_MS = 6000;
    var autoplay = !MENOS_MOVIMENTO;
    var mouseEmCima = false;
    var naTela = !('IntersectionObserver' in window); // sem IO, considera visível
    var temporizador = null;

    var atualizarCarrossel = function () {
      var fim = faixa.scrollWidth - faixa.clientWidth;
      controles.hidden = fim <= 1;
      anterior.disabled = faixa.scrollLeft <= 1;
      proximo.disabled = faixa.scrollLeft >= fim - 1;
    };
    var rolar = function (sentido) {
      faixa.scrollBy({ left: sentido * faixa.clientWidth, behavior: MENOS_MOVIMENTO ? 'auto' : 'smooth' });
    };

    var podeRodar = function () {
      return autoplay && !mouseEmCima && naTela && !document.hidden &&
        !controles.hidden && !carrossel.querySelector(':focus-visible');
    };
    var agendar = function () {
      clearTimeout(temporizador);
      if (podeRodar()) temporizador = setTimeout(avancar, AUTOPLAY_MS);
    };
    var avancar = function () {
      if (proximo.disabled) faixa.scrollTo({ left: 0, behavior: 'smooth' });
      else rolar(1);
      agendar(); // a rolagem também reagenda; este cobre o caso de ela não acontecer
    };

    anterior.addEventListener('click', function () { rolar(-1); });
    proximo.addEventListener('click', function () { rolar(1); });
    faixa.addEventListener('scroll', function () { atualizarCarrossel(); agendar(); }, { passive: true });
    window.addEventListener('resize', function () { atualizarCarrossel(); agendar(); });
    atualizarCarrossel();

    if (autoplay) {
      carrossel.addEventListener('mouseenter', function () { mouseEmCima = true; agendar(); });
      carrossel.addEventListener('mouseleave', function () { mouseEmCima = false; agendar(); });
      carrossel.addEventListener('focusin', agendar);
      carrossel.addEventListener('focusout', function () { setTimeout(agendar, 0); });
      document.addEventListener('visibilitychange', agendar);
      if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (entradas) {
          naTela = entradas[0].isIntersecting;
          agendar();
        }, { threshold: 0.3 }).observe(carrossel);
      }
      agendar();
    }
  }

  // Parallax da foto da seção "sobre": a foto (130% da altura da seção)
  // desliza até 11% da própria altura para cada lado enquanto a seção cruza a tela.
  var sobre = document.getElementById('sobre');
  var fotoSobre = sobre && sobre.querySelector('.sobre__foto');
  if (fotoSobre && !MENOS_MOVIMENTO) {
    var agendado = false;
    var moverFoto = function () {
      agendado = false;
      var r = sobre.getBoundingClientRect();
      var alturaTela = window.innerHeight;
      if (r.bottom < 0 || r.top > alturaTela) return; // fora da tela
      var progresso = (alturaTela - r.top) / (alturaTela + r.height); // 0 → 1
      var deslocamento = (progresso - 0.5) * 0.22 * fotoSobre.offsetHeight;
      fotoSobre.style.transform = 'translate3d(0,' + deslocamento.toFixed(1) + 'px,0)';
    };
    var agendar = function () {
      if (!agendado) { agendado = true; window.requestAnimationFrame(moverFoto); }
    };
    window.addEventListener('scroll', agendar, { passive: true });
    window.addEventListener('resize', agendar);
    moverFoto();
  }

  // A CTA fixa só aparece depois que o formulário do hero sai da tela.
  var ctaFixa = document.querySelector('.cta-fixa');
  if (ctaFixa && form && 'IntersectionObserver' in window) {
    new IntersectionObserver(function (entradas) {
      entradas.forEach(function (entrada) {
        if (entrada.isIntersecting) ctaFixa.removeAttribute('data-visivel');
        else ctaFixa.setAttribute('data-visivel', '');
      });
    }, { threshold: 0 }).observe(form);
  }

  /* -------------------------------------------------------------- init -- */

  carregarMetricas();
})();
