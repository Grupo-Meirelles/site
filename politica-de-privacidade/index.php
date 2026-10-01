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
    <p>O Grupo Meirelles é o nome comercial de <strong>LINK SERVIÇOS DE INFORMAÇÕES CADASTRAIS LTDA</strong>, inscrita no CNPJ sob o nº <strong>20.200.080/0001-36</strong>, com atendimento na Av. das Araucárias, Shopping, 3º andar, Sala 332, Águas Claras — Brasília/DF.</p>
    <p>A empresa atua na <strong>intermediação de soluções financeiras</strong>, conectando clientes interessados a instituições financeiras e parceiros comerciais, conforme as características e condições de cada operação.</p>
    <p>Para fins desta Política de Privacidade, a <strong>LINK SERVIÇOS DE INFORMAÇÕES CADASTRAIS LTDA</strong> é responsável pelo tratamento dos dados pessoais coletados por meio deste site, atuando como controladora, nos termos da Lei Geral de Proteção de Dados – LGPD.</p>
    
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
    <p>Caso você decida prosseguir com uma simulação ou operação, nossa equipe poderá solicitar outros dados e documentos necessários à análise e intermediação da solução financeira, como CPF, documento de identificação, contracheque, comprovantes e informações relacionadas ao seu vínculo com órgão, empresa ou fonte pagadora.</p>
    <p>Os dados solicitados poderão variar de acordo com a operação pretendida e com as exigências da instituição financeira ou parceiro responsável pela análise e eventual contratação.</p>
    <p>Como regra, não solicitamos dados pessoais sensíveis por meio deste site. Caso o tratamento dessa categoria de dados se torne necessário durante o atendimento, ele será realizado apenas quando houver fundamento legal e na medida necessária à finalidade correspondente.</p>
    <p>O site não é destinado a menores de 18 anos.</p>

    <h2 id="finalidades">3. Para que usamos os seus dados</h2>
    <div class="tabela-rolavel">
      <table>
        <thead><tr><th scope="col">Finalidade</th><th scope="col">Base legal (LGPD)</th></tr></thead>
        <tbody>
          <tr><td>Entrar em contato com você para atender ao pedido de simulação ou solicitação realizada.</td><td>Procedimentos preliminares relacionados a contrato, a pedido do titular (art. 7º, V).</td></tr>
          <tr><td>Analisar a solicitação apresentada, identificar alternativas compatíveis com o interesse do cliente e, quando aplicável, encaminhar os dados necessários às instituições financeiras e parceiros envolvidos na operação.</td><td>Procedimentos preliminares e execução de contrato (art. 7º, V).</td></tr>
          <tr><td>Garantir a segurança do site e do formulário, prevenir abusos, tentativas de fraude e envios automatizados.</td><td>Legítimo interesse (art. 7º, IX).</td></tr>
          <tr><td>Avaliar a origem dos pedidos de simulação e a efetividade dos canais de divulgação utilizados pela empresa, a partir dos parâmetros presentes no endereço de acesso.</td><td>Legítimo interesse (art. 7º, IX).</td></tr>
          <tr><td>Cumprir obrigações legais ou regulatórias e resguardar direitos da empresa em processos judiciais, administrativos ou arbitrais.</td><td>Cumprimento de obrigação legal ou regulatória e exercício regular de direitos (art. 7º, II e VI).</td></tr>
        </tbody>
      </table>
    </div>
    <p>Não utilizamos seus dados pessoais para finalidades incompatíveis com aquelas informadas nesta Política.</p>

    <h2 id="compartilhamento">4. Com quem compartilhamos</h2>
    <ul>
      <li><strong>Instituições financeiras e parceiros comerciais envolvidos na operação,</strong>, quando você decidir prosseguir com uma proposta e o compartilhamento for necessário para análise, apresentação de alternativas, formalização ou conclusão da solução financeira solicitada. Após o recebimento dos dados, essas instituições e parceiros poderão realizar o tratamento das informações sob sua própria responsabilidade, de acordo com suas obrigações legais, regulatórias e respectivas políticas de privacidade.</li>
      <li><strong>Fornecedores de tecnologia e serviços utilizados pela empresa</strong>, como serviços de hospedagem, envio de e-mails, sistemas de gestão de clientes (CRM) e outras ferramentas necessárias à operação do site e ao atendimento. Esses fornecedores poderão tratar dados pessoais em nome da empresa e de acordo com as finalidades contratadas.</li>
      <li><strong>Google Fonts</strong>, que fornece as fontes do site: ao carregar a página, o seu navegador se conecta aos servidores do Google, que recebem o seu endereço IP.</li>
      <li><strong>Reclame Aqui</strong>, que exibe o selo de reputação no rodapé: o selo é carregado dos servidores do Reclame Aqui (hospedados na Amazon Web Services), que recebem o seu endereço IP e dados do navegador.</li>
      <li><strong>Autoridades públicas</strong>, quando houver obrigação legal ou ordem judicial.</li>
    </ul>
    <p>Alguns desses fornecedores podem armazenar dados fora do Brasil. Nesses casos, a transferência segue as regras dos arts. 33 a 36 da LGPD.</p>

    <h2 id="cookies">5. Cookies e ferramentas de medição</h2>
    <p>Hoje este site não grava cookies próprios, nem de publicidade ou de medição, e não guarda informações no seu navegador.</p>
    <p>Se passarmos a usar ferramentas de medição ou de anúncios (como Google Analytics, Google Ads ou Meta Pixel), esta política será atualizada antes e, quando a lei exigir, pediremos o seu consentimento.</p>

    <h2 id="guarda">6. Por quanto tempo guardamos e como protegemos</h2>
    <p>Os pedidos de simulação e os dados relacionados ao atendimento serão mantidos pelo período necessário ao acompanhamento da solicitação e, após o último contato, pelo prazo de <strong>5 anos</strong>, ressalvadas as hipóteses em que sua conservação seja necessária para cumprimento de obrigação legal ou regulatória ou exercício regular de direitos.</p>
    <p>Quando a intermediação resultar em contratação ou operação, os dados e documentos relacionados poderão ser mantidos pelo período necessário ao cumprimento das obrigações legais, regulatórias e contratuais aplicáveis e à proteção dos direitos da empresa e dos demais envolvidos na operação.</p>
    <p>Encerradas as finalidades que justificam sua conservação, os dados serão eliminados ou anonimizados, ressalvadas as hipóteses de conservação autorizadas pela LGPD.</p>
    <p>Adotamos medidas técnicas e administrativas destinadas à proteção dos dados pessoais, incluindo conexão criptografada (HTTPS), restrição de acesso aos sistemas e informações dos clientes e mecanismos de proteção contra envios automatizados no formulário.</p>
    <p>Na hipótese de incidente de segurança que possa acarretar risco ou dano relevante aos titulares, serão adotadas as providências cabíveis e realizadas as comunicações exigidas pela LGPD e pela regulamentação da ANPD, nos prazos aplicáveis.

    <h2 id="direitos">7. Seus direitos</h2>
    <p>Nos termos da LGPD, você poderá solicitar, conforme aplicável:</p>
    <ul>
      <li>confirmação da existência de tratamento de seus dados pessoais;</li>
      <li>acesso aos dados;</li>
      <li>correção de dados incompletos, inexatos ou desatualizados;</li>
      <li>anonimização, bloqueio ou eliminação de dados desnecessários, excessivos ou tratados em desconformidade com a LGPD;</li>
      <li>portabilidade dos dados, observadas as normas aplicáveis;</li>
      <li>eliminação dos dados tratados com fundamento no consentimento, quando aplicável e ressalvadas as hipóteses legais de conservação;</li>
      <li>informação sobre as entidades públicas e privadas com as quais realizamos o compartilhamento de dados;</li>
      <li>informação sobre a possibilidade de não fornecer consentimento e sobre as consequências da negativa, quando o tratamento depender dessa base legal;</li>
      <li>revogação do consentimento, quando aplicável;</li>
      <li>oposição ao tratamento realizado com fundamento em uma das hipóteses de dispensa de consentimento, caso haja descumprimento da LGPD.</li>
    </ul>
    <p>Para exercer seus direitos, entre em contato por meio do canal de privacidade indicado abaixo. Para sua segurança, poderemos solicitar informações adicionais para confirmar sua identidade antes do atendimento.</p>

    <p>As solicitações serão analisadas e respondidas nos prazos aplicáveis previstos na legislação e na regulamentação da ANPD.</p>
    <p>O titular também poderá apresentar reclamação à Autoridade Nacional de Proteção de Dados (ANPD).</p>

    <h2 id="contato">8. Encarregado e contato</h2>
    <p>Encarregado pelo tratamento de dados pessoais: setor jurídico.</p>
    <p>Canal para assuntos de privacidade: juridico@grupomeirelles.com.br. Você também pode escrever para <a href="mailto:contato@grupomeirelles.com.br">contato@grupomeirelles.com.br</a>, ligar para <a href="tel:+5561982564974">(61) 98256-4974</a> ou ir até o nosso escritório em Águas Claras.</p>

    <h2 id="alteracoes">9. Alterações desta política</h2>
    <p>Esta política pode ser atualizada quando mudarmos a forma de tratar dados ou quando a lei exigir. A data da última atualização fica sempre no topo desta página. Mudanças relevantes serão destacadas aqui.</p>
  </div>
</article>

</main>

<?php require __DIR__ . '/../inc/rodape.php'; ?>
