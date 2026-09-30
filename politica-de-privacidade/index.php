<?php
/**
 * Política de privacidade — servida em /politica-de-privacidade/ (pasta com
 * index.php: funciona no Apache, no nginx e no `php -S` sem regra de rewrite).
 *
 * Trechos marcados com <mark class="a-definir"> dependem de decisão da empresa
 * ou do jurídico e precisam ser preenchidos antes de publicar.
 */

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

$pagina = [
    'titulo' => 'Política de privacidade — Grupo Meirelles',
    'descricao' => 'Como o Grupo Meirelles coleta, usa, compartilha e protege seus dados pessoais, e como exercer seus direitos pela LGPD.',
    'raiz' => '../',
    'home' => false,
];
$atualizadaEm = '30 de setembro de 2026';

require __DIR__ . '/../inc/topo.php';
?>
<main id="conteudo">

<section class="pagina-topo">
  <div class="pagina-topo__interno">
    <p class="rotulo rotulo--claro">Institucional</p>
    <h1>Política de privacidade</h1>
    <p class="pagina-topo__sub">Última atualização: <?= $atualizadaEm ?></p>
  </div>
</section>

<article class="texto-legal">
  <nav class="texto-legal__indice" aria-label="Nesta página">
    <p class="rotulo">Nesta página</p>
    <ol>
      <li><a href="#quem-somos">Quem somos</a></li>
      <li><a href="#dados">Dados que coletamos</a></li>
      <li><a href="#finalidades">Para que usamos</a></li>
      <li><a href="#compartilhamento">Com quem compartilhamos</a></li>
      <li><a href="#cookies">Cookies e medição</a></li>
      <li><a href="#guarda">Guarda e segurança</a></li>
      <li><a href="#direitos">Seus direitos</a></li>
      <li><a href="#contato">Encarregado e contato</a></li>
      <li><a href="#alteracoes">Alterações</a></li>
    </ol>
  </nav>

  <div class="texto-legal__corpo">
    <p class="texto-legal__intro">Esta política explica, em linguagem direta, quais dados pessoais o Grupo Meirelles recebe quando você usa este site ou fala com a nossa equipe, para que eles servem, com quem são compartilhados e como você pode exercer os seus direitos previstos na Lei Geral de Proteção de Dados (Lei nº 13.709/2018 — LGPD).</p>

    <h2 id="quem-somos">1. Quem somos</h2>
    <p>O Grupo Meirelles tem por razão social <strong>LINK SERVIÇOS DE INFORMAÇÕES CADASTRAIS LTDA</strong>, inscrita no CNPJ sob o nº <strong>20.200.080/0001-36</strong>, com atendimento na Av. das Araucárias, Shopping, 3º andar, Sala 332, Águas Claras — Brasília/DF. Somos correspondente bancário: não somos instituição financeira e não cobramos nenhum valor adiantado.</p>
    <p>Para os dados tratados neste site, somos o <strong>controlador</strong>, ou seja, quem decide como e para que eles são usados.</p>

    <h2 id="dados">2. Dados que coletamos</h2>
    <h3>O que você informa no formulário de simulação</h3>
    <ul>
      <li>nome completo;</li>
      <li>número de WhatsApp ou telefone, com DDD;</li>
      <li>valor desejado e prazo da simulação.</li>
    </ul>
    <h3>O que é registrado automaticamente no envio</h3>
    <ul>
      <li>data e hora do envio;</li>
      <li>endereço IP e dados do navegador (tipo e versão);</li>
      <li>a página de onde o formulário foi enviado e os parâmetros de campanha presentes no endereço (por exemplo <code>utm_source</code>, <code>gclid</code> ou <code>fbclid</code>), que indicam por qual anúncio ou link você chegou.</li>
    </ul>
    <h3>No atendimento</h3>
    <p>Se você decidir seguir com uma simulação ou contratação, a nossa equipe pode pedir outros dados e documentos necessários à análise pelos bancos, como CPF, contracheque e dados do seu vínculo com o órgão ou empresa. Nesse caso, você é informado sobre o que é pedido e por quê no próprio atendimento.</p>
    <p>Não pedimos dados sensíveis (como origem racial, saúde ou religião) e o site não é destinado a menores de 18 anos.</p>

    <h2 id="finalidades">3. Para que usamos os seus dados</h2>
    <div class="tabela-rolavel">
      <table>
        <thead><tr><th scope="col">Finalidade</th><th scope="col">Base legal (LGPD)</th></tr></thead>
        <tbody>
          <tr><td>Entrar em contato com você para apresentar a simulação pedida.</td><td>Procedimentos preliminares a um contrato, a seu pedido (art. 7º, V).</td></tr>
          <tr><td>Comparar propostas e encaminhar a sua solicitação aos bancos parceiros, quando você decide seguir.</td><td>Procedimentos preliminares e execução de contrato (art. 7º, V).</td></tr>
          <tr><td>Proteger o formulário contra abuso e robôs (por exemplo, limitando o número de envios por endereço IP) e prevenir fraudes.</td><td>Legítimo interesse (art. 7º, IX) e prevenção à fraude.</td></tr>
          <tr><td>Saber quais anúncios e canais trazem pedidos de simulação, a partir dos parâmetros de campanha.</td><td>Legítimo interesse (art. 7º, IX).</td></tr>
          <tr><td>Cumprir obrigações legais e regulatórias e nos defender em processos.</td><td>Obrigação legal e exercício regular de direitos (art. 7º, II e VI).</td></tr>
        </tbody>
      </table>
    </div>
    <p>Não vendemos seus dados e não os usamos para finalidades diferentes das descritas aqui.</p>

    <h2 id="compartilhamento">4. Com quem compartilhamos</h2>
    <ul>
      <li><strong>Bancos e instituições financeiras parceiras</strong>, apenas quando você decide seguir com uma proposta, para que eles façam a análise e a contratação.</li>
      <li><strong>Fornecedores que operam o site e o atendimento</strong>, como hospedagem, envio de e-mail e o sistema de gestão de clientes (CRM) que recebe os pedidos de simulação. Eles tratam os dados em nosso nome e só para essas finalidades.</li>
      <li><strong>Google Fonts</strong>, que fornece as fontes do site: ao carregar a página, o seu navegador se conecta aos servidores do Google, que recebem o seu endereço IP.</li>
      <li><strong>Reclame Aqui</strong>, que exibe o selo de reputação no rodapé: o selo é carregado dos servidores do Reclame Aqui (hospedados na Amazon Web Services), que recebem o seu endereço IP e dados do navegador.</li>
      <li><strong>Autoridades públicas</strong>, quando houver obrigação legal ou ordem judicial.</li>
    </ul>
    <p>Alguns desses fornecedores podem armazenar dados fora do Brasil. Nesses casos, a transferência segue as regras dos arts. 33 a 36 da LGPD.</p>

    <h2 id="cookies">5. Cookies e ferramentas de medição</h2>
    <p>Hoje este site não grava cookies próprios, nem de publicidade ou de medição, e não guarda informações no seu navegador.</p>
    <p>Se passarmos a usar ferramentas de medição ou de anúncios (como Google Analytics, Google Ads ou Meta Pixel), esta política será atualizada antes e, quando a lei exigir, pediremos o seu consentimento.</p>

    <h2 id="guarda">6. Por quanto tempo guardamos e como protegemos</h2>
    <p>Os pedidos de simulação ficam guardados por <mark class="a-definir">[prazo a definir pela empresa — ex.: 12 meses]</mark> depois do último contato, para darmos continuidade ao atendimento. Dados ligados a uma contratação são mantidos pelo tempo exigido por lei e pelas regras dos bancos parceiros. Passados esses prazos, os dados são apagados ou anonimizados.</p>
    <p>Usamos conexão criptografada (HTTPS), acesso restrito aos sistemas e às informações dos clientes e bloqueio de envios automatizados no formulário. Nenhum sistema é totalmente imune a incidentes; se algum acontecer e puder trazer risco relevante a você, comunicaremos você e a Autoridade Nacional de Proteção de Dados (ANPD), como determina a lei.</p>

    <h2 id="direitos">7. Seus direitos</h2>
    <p>A qualquer momento, você pode pedir:</p>
    <ul>
      <li>confirmação de que tratamos seus dados e acesso a eles;</li>
      <li>correção de dados incompletos, inexatos ou desatualizados;</li>
      <li>anonimização, bloqueio ou eliminação de dados desnecessários ou tratados em desacordo com a LGPD;</li>
      <li>portabilidade dos dados a outro fornecedor;</li>
      <li>eliminação dos dados tratados com o seu consentimento, e revogação desse consentimento;</li>
      <li>informação sobre com quem compartilhamos seus dados;</li>
      <li>oposição a um tratamento feito com base no legítimo interesse, quando ele não estiver de acordo com a lei.</li>
    </ul>
    <p>Para exercer qualquer um deles, fale com a gente pelo canal indicado abaixo. Podemos pedir uma confirmação de identidade antes de atender, para proteger você. Respondemos nos prazos previstos na LGPD. Você também pode apresentar reclamação à ANPD pelo site <a href="https://www.gov.br/anpd" target="_blank" rel="noopener">gov.br/anpd</a>.</p>

    <h2 id="contato">8. Encarregado e contato</h2>
    <p>Encarregado pelo tratamento de dados pessoais: <mark class="a-definir">[nome do encarregado a definir]</mark>.</p>
    <p>Canal para assuntos de privacidade: <mark class="a-definir">[e-mail a definir — ex.: privacidade@grupomeirelles.com.br]</mark>. Você também pode escrever para <a href="mailto:contato@grupomeirelles.com.br">contato@grupomeirelles.com.br</a>, ligar para <a href="tel:+5561982564974">(61) 98256-4974</a> ou ir até o nosso escritório em Águas Claras.</p>

    <h2 id="alteracoes">9. Alterações desta política</h2>
    <p>Esta política pode ser atualizada quando mudarmos a forma de tratar dados ou quando a lei exigir. A data da última atualização fica sempre no topo desta página. Mudanças relevantes serão destacadas aqui.</p>
  </div>
</article>

</main>

<?php require __DIR__ . '/../inc/rodape.php'; ?>
