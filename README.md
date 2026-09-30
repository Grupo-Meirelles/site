# Grupo Meirelles — site (PHP, CSS, JS)

Sem build e sem dependências. `index.php` + `css/styles.css` + `js/main.js`.
Precisa de PHP 8.1+ no servidor (avaliações e envio do formulário). Configuração no `.env`.

```
site/
├── index.php
├── politica-de-privacidade/index.php   página /politica-de-privacidade
├── api/leads.php                  recebe o formulário: banco + API (CRM) + e-mail
├── inc/
│   ├── avaliacoes.php             lê data/avaliacoes/ e monta os cartões
│   ├── duvidas.php                lê data/duvidas/ e monta as dúvidas frequentes
│   ├── topo.php / rodape.php      <head>, menu e rodapé compartilhados pelas páginas
│   ├── leads.php                  validação, e-mail (SMTP/mail()) e envio ao CRM
│   ├── db.php                     conexão MySQL e gravação dos leads
│   └── env.php                    lê o .env
├── .env.example                   modelo de configuração (copie para .env)
├── .htaccess                      bloqueia .env, inc/ e db/ no Apache
├── db/schema.sql                  tabela `leads` (rodar uma vez)
├── css/styles.css
├── js/main.js
├── img/
└── data/
    ├── metricas.example.json      fixture usado em ambiente local
    ├── avaliacoes/
    │   ├── 01-fulano.json ...     uma avaliação do Google por arquivo
    │   ├── _resumo.json           nota média, total e link do perfil
    │   └── _modelo.json           exemplo para copiar (ignorado)
    └── duvidas/
        ├── 01-pergunta.json ...   uma dúvida frequente por arquivo
        └── _modelo.json           exemplo para copiar (ignorado)
```

Abrir localmente: `php -S localhost:8000` na pasta do site e acesse http://localhost:8000.
Para testar o formulário localmente, preencha o `.env` (ver `POST api/leads.php`).
Quando a API de métricas não responder — ambiente local, homologação sem back-end ou
queda momentânea — o script cai automaticamente para `data/metricas.example.json`,
então a página nunca aparece vazia. Em produção, com a API no ar, o fixture nunca é usado.
As avaliações não dependem de API — ver seção própria abaixo.

---

## Contratos de API

Os endpoints ficam no topo de `js/main.js` (objeto `API`). Em produção o script chama
caminhos relativos do mesmo domínio; ajuste ali se a API estiver em outro host (e libere CORS).

### `GET /api/metricas`

Alimenta os contadores animados. Números crus, sem formatação — o front formata.

```json
{
  "anos_empresa": 15,
  "clientes_atendidos": 100000,
  "valor_liberado": 500000000,
  "atualizado_em": "2026-09-21T09:00:00-03:00"
}
```

O vínculo é feito por `data-chave` no HTML:

| `data-chave`         | `data-formato` | Exibe        |
|----------------------|----------------|--------------|
| `anos_empresa`       | `anos`         | `+15`        |
| `clientes_atendidos` | `compacto`     | `100 mil`    |
| `valor_liberado`     | `milhoes`      | `R$ 500 mi`  |
| `nota_media`         | `nota`         | `4,9 ★`      |

Para adicionar uma métrica nova basta criar o elemento com `data-contador`,
`data-chave`, `data-formato` e um `data-valor` estático de fallback. Nenhum JS a mais.

**Cache:** os valores mudam pouco. Sirva com `Cache-Control: public, max-age=300`
e calcule no back-end (job diário), não a cada request.

### Avaliações do Google — mantidas à mão, sem API

Nada de proxy nem chave de API no servidor. As avaliações ficam em arquivos JSON
versionados junto do site, **uma por arquivo** em `data/avaliacoes/`. A cada acesso,
`inc/avaliacoes.php` lê a pasta e o `index.php` já entrega os cartões prontos no HTML
(bom para SEO e sem estado de "carregando"). Adicionar uma avaliação é só soltar um
novo `.json` na pasta — não há índice nem script para rodar.

São exibidas todas as avaliações com nota ≥ 4 e texto não vazio, na ordem do nome
do arquivo (`01-`, `02-`, `03-`...), num carrossel: 3 por vez no desktop, 2 no
tablet e 1 no celular (que desliza com o dedo). Arquivos que começam com `_` são ignorados.

**Para adicionar/editar uma avaliação:**

1. Copie `data/avaliacoes/_modelo.json` para um novo arquivo, ex.
   `data/avaliacoes/07-fulano.json`, e preencha olhando o perfil do Google:

   ```json
   {
     "autor": "Rosângela M.",
     "nota": 5,
     "texto": "Fiz a portabilidade de dois contratos...",
     "data": "2026-08-14T14:22:00-03:00",
     "foto": "",
     "vinculo": "Servidora — GDF"
   }
   ```

   `foto` (URL) e `vinculo` são opcionais: sem foto, gera um avatar com as iniciais;
   `vinculo` é um contexto que o Google não fornece (cargo/órgão), preencha se souber.
   Respeite os termos do Google: não edite o texto da avaliação, mantenha a atribuição.

2. Para trocar os números gerais do perfil (nota média, total de avaliações, link),
   edite `data/avaliacoes/_resumo.json` — não é uma avaliação, é ignorado na lista.

3. Suba o novo arquivo para o servidor. Pronto — aparece no próximo acesso.

Um arquivo com JSON inválido ou sem `autor`/`nota`/`texto` é pulado (e registrado no
log de erros do PHP) sem derrubar os outros. Se nenhuma avaliação passar no filtro, a
seção mostra um aviso com link para o perfil do Google.

### Dúvidas frequentes — uma por arquivo, sem mexer no código

Mesmo esquema das avaliações: cada pergunta é um `.json` em `data/duvidas/`, lido a
cada acesso por `inc/duvidas.php` e entregue pronto no HTML.

```json
{
  "pergunta": "Preciso ter nome limpo?",
  "resposta": "Não. Como a parcela é descontada direto do contracheque..."
}
```

- **Adicionar:** copie `data/duvidas/_modelo.json` para um novo arquivo (ex.
  `07-minha-pergunta.json`), preencha e suba para o servidor.
- **Remover:** apague o arquivo da pergunta.
- **Reordenar:** a ordem segue o nome do arquivo (`01-`, `02-`...); renomeie para trocar.

Texto simples, sem HTML (tags aparecem como texto); quebras de linha (`\n`) são
mantidas. Arquivos que começam com `_` são ignorados. Um JSON inválido ou sem
`pergunta`/`resposta` é pulado e registrado no log do PHP com o prefixo `[duvidas]`.
Sem nenhuma pergunta válida, a seção inteira some da página.

### `POST api/leads.php`

Enviado pelo formulário do hero. Implementado em `api/leads.php` + `inc/leads.php`.

```json
{
  "nome": "Maria Silva",
  "telefone": "61981171464",
  "valor": 25000,
  "prazo": 72,
  "origem": "/?utm_source=google&utm_campaign=...",
  "empresa": ""
}
```

`telefone` vai só com dígitos (DDD + número). `origem` carrega o path e a query string
completa; o servidor extrai os UTMs (`utm_*`, `gclid`, `fbclid`). `empresa` é um
honeypot — campo escondido que só robôs preenchem; se vier preenchido, o envio é
descartado em silêncio.

**O que o servidor faz, em ordem:**

1. Valida os campos (nome com 2+ palavras, celular/fixo com DDD, valor entre
   R$ 2 mil e R$ 80 mil) e limita a 10 envios por IP a cada 10 minutos.
2. **Banco**: grava o lead na tabela `leads` (MySQL/MariaDB, `DB_*` no `.env`) com
   `api_status = 'pendente'`. Com `DB_NOME` vazio a etapa é pulada. Se o banco
   falhar, o lead segue para a API e o e-mail mesmo assim. Crie a tabela uma vez:
   `mysql -u <usuario> -p <banco> < db/schema.sql`.
3. **API (CRM)** — formato logo abaixo. O resultado vai para a linha do lead:
   `api_status` vira `enviado` ou `falhou` (com a mensagem em `api_erro`) e
   `api_tentativas` soma 1. Com a API desligada a linha fica `pendente`. Para achar
   o que precisa ser reenviado: `SELECT * FROM leads WHERE api_status <> 'enviado'`.
4. **E-mail** para `LEADS_EMAIL_PARA` com nome, WhatsApp (com link `wa.me`), valor,
   prazo, página/UTMs, data/hora, IP e navegador. Usa SMTP se `SMTP_HOST` estiver
   preenchido; senão, o `mail()` da hospedagem.

**Chamada à API (passo 3):** `POST` JSON em `CRM_URL` (com `Authorization: Bearer CRM_TOKEN`, se
   houver). Com `CRM_URL` vazio a etapa é pulada. O formato enviado fica em
   `payloadCrm()` em `inc/leads.php` — é o único lugar a mexer para adaptar a um CRM
   específico:

   ```json
   {
     "nome": "Maria Silva",
     "telefone": "5561981171464",
     "valor": 25000,
     "prazo": 72,
     "origem": "/?utm_source=google",
     "utm": { "utm_source": "google" },
     "recebido_em": "2026-09-28T15:02:10-03:00",
     "ip": "200.100.50.25"
   }
   ```

**Respostas:**

| Status | Quando |
|---|---|
| `200` | ao menos um destino (banco, API ou e-mail) recebeu o lead |
| `422` | campos inválidos — `campos` diz quais; o front marca os campos |
| `429` | limite por IP atingido |
| `502` | banco, API e e-mail falharam — o front sugere chamar no WhatsApp |

Uma falha isolada (ex.: CRM fora do ar, e-mail ok) não aparece para o visitante; vai
para o log de erros do PHP com o prefixo `[leads]`. Monitore esse log.

Em `2xx` o front limpa o formulário, mostra a confirmação e dispara
`dataLayer.push({event: 'lead_simulacao'})` — use esse evento como conversão no GA4
e no Google Ads.

**Configuração (`.env`).** Copie `.env.example` para `.env` e preencha. Em produção,
coloque o `.env` **um nível acima da pasta pública** do site — o código procura lá
primeiro. Se ele precisar ficar na raiz do site, o `.htaccess` bloqueia o acesso no
Apache; no nginx, adicione `location ~ /\.(?!well-known) { deny all; }` e
`location ^~ /inc/ { deny all; }`.

Use SMTP de uma conta do próprio domínio (ex.: `site@grupomeirelles.com.br`): o
`mail()` de hospedagem compartilhada costuma cair no spam ou ser bloqueado.

**Ainda falta:** o formulário não tem checkbox de consentimento LGPD. Data/hora e IP
de cada envio já vão no e-mail e no CRM, mas o texto de consentimento precisa ser
definido com o jurídico.

---

## Pontos de atenção

**Simulação.** `TAXA_MES = 0.0172` em `js/main.js` é uma taxa de referência fixa e
`PRAZO = 72` é fixo. A parcela exibida é estimativa — se o jurídico exigir precisão,
troque por uma tabela de taxas por convênio vinda da API.

**Números do HTML.** Os valores estáticos (`+15 anos`, `100 mil`, `R$ 500 mi`) são
fallback para quando a API de métricas não responde e para o primeiro paint antes do
fetch — mantenha-os próximos do real. Nota média e total de avaliações não são
estáticos: vêm de `data/avaliacoes/_resumo.json`, com `4,9` e `312` no topo do
`index.php` como padrão caso o arquivo falte.

**Imagens.** As fotos em `img/` vieram dos mockups. Substitua por versões otimizadas
(WebP, ~1600px de largura para os heros) antes de publicar.

**Acessibilidade.** Contadores respeitam `prefers-reduced-motion` (escrevem o valor
final sem animar). Formulário tem labels, `aria-invalid` e mensagens de erro ligadas.
Os textos sobre as fotos do hero dependem do véu escuro no CSS — se trocar a imagem,
confira o contraste.
# site
# site
