<?php
/**
 * Fechamento das páginas: rodapé, CTA fixa (só na home) e script.
 * Usa $raiz, $home e $secao definidos em inc/topo.php.
 */

declare(strict_types=1);
?>
<footer class="rodape">
  <div class="rodape__grade">
    <div>
      <img class="rodape__logo" src="<?= $raiz ?>img/logo-gm.png" alt="Grupo Meirelles" width="150" height="74" loading="lazy">
      <p>Correspondente bancário. Não somos instituição financeira e não cobramos nada adiantado.</p>
    </div>
    <!-- <div>
      <h3>Produtos</h3>
      <ul><li><a href="<?= $secao('produtos') ?>">Compra de consignado</a></li><li><a href="<?= $secao('produtos') ?>">Empréstimo consignado</a></li><li><a href="<?= $secao('produtos') ?>">Cartão consignado</a></li><li><a href="<?= $secao('produtos') ?>">Portabilidade</a></li><li><a href="<?= $secao('produtos') ?>">Refinanciamento</a></li></ul>
    </div> -->
    <div>
      <h3>Institucional</h3>
      <ul><li><a href="<?= $secao('sobre') ?>">Sobre nós</a></li><li><a href="<?= $secao('duvidas') ?>">Dúvidas frequentes</a></li><li><a href="<?= $raiz ?>politica-de-privacidade/">Política de privacidade</a></li></ul>
    </div>
    <div>
      <h3>Contato</h3>
      <ul>
        <li><a href="tel:+556130383302">(61) 3038-3302</a></li>
        <li><a href="mailto:contato@grupomeirelles.com.br">contato@grupomeirelles.com.br</a></li>
        <li>Av. das Araucárias, Sala 332</li>
        <li>Águas Claras — Brasília/DF</li>
      </ul>
    </div>
    <div>
      <div id="ra-verified-seal"><script type="text/javascript" id="ra-embed-verified-seal" src="https://s3.amazonaws.com/raichu-beta/ra-verified/bundle.js" data-id="a091aG9oYWV6UHdISXFVLTpncnVwby1tZWlyZWxsZXM=" data-target="ra-verified-seal" data-model="horizontal_2"></script></div>
    </div>
  </div>
  <p class="rodape__legal">20.200.080/0001-36 — LINK SERVIÇOS DE INFORMAÇÕES CADASTRAIS LTDA · Copyright <?php echo(date("Y")) ?> Grupo Meirelles</p>
</footer>

<?php if ($home): ?>
<a class="cta-fixa" href="#simulador">Simular agora</a>
<?php endif; ?>

<script src="<?= $raiz ?>js/main.js" defer></script>
</body>
</html>
