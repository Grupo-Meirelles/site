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
        <li><a href="tel:+5561982564974">(61) 98256-4974</a></li>
        <li><a href="mailto:contato@grupomeirelles.com.br">contato@grupomeirelles.com.br</a></li>
        <li>Av. das Araucárias, Sala 332</li>
        <li>Águas Claras — Brasília/DF</li>
      </ul>
    </div>
    <div>
      <ul class="rodape__redes">
        <li><a href="https://www.instagram.com/ogrupomeirelles/" target="_blank" rel="noopener" aria-label="Instagram do Grupo Meirelles">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.1" fill="currentColor" stroke="none"/></svg>
        </a></li>
        <li><a href="https://www.linkedin.com/company/grupo-meirelles/" target="_blank" rel="noopener" aria-label="LinkedIn do Grupo Meirelles">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9.75h4v11H3zM9.5 9.75h3.8v1.5h.05c.53-1 1.84-2.05 3.78-2.05 4.04 0 4.79 2.66 4.79 6.12v5.43h-4v-4.82c0-1.15-.02-2.63-1.6-2.63-1.6 0-1.85 1.25-1.85 2.55v4.9h-4z"/></svg>
        </a></li>
        <li><a href="https://www.facebook.com/ogrupomeirelles" target="_blank" rel="noopener" aria-label="Facebook do Grupo Meirelles">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M13.6 21v-8.2h2.75l.42-3.2H13.6V7.56c0-.93.26-1.56 1.59-1.56h1.7V3.14A22.8 22.8 0 0 0 14.42 3c-2.45 0-4.12 1.5-4.12 4.24V9.6H7.53v3.2h2.77V21z"/></svg>
        </a></li>
        <li><a href="https://wa.me/5561982564974" target="_blank" rel="noopener" aria-label="WhatsApp do Grupo Meirelles: (61) 98256-4974">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="M3.6 20.4l1.25-4.1a8.4 8.4 0 1 1 3.05 3z"/><path d="M9.2 7.9c-.5 0-1 .6-1 1.3 0 2.9 3.7 6.6 6.6 6.6.7 0 1.3-.5 1.3-1l-.2-.9-1.9-.8-.9.9a5.3 5.3 0 0 1-2.9-2.9l.9-.9-.8-1.9z" fill="currentColor" stroke="none"/></svg>
        </a></li>
      </ul>
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
