<?php
/**
 * Leads do formulário do hero: validação, envio por e-mail e envio ao CRM.
 * Usado por api/leads.php. Configuração no .env — ver .env.example.
 */

declare(strict_types=1);

require_once __DIR__ . '/env.php';

const LEAD_VALOR_MIN = 2000;
const LEAD_VALOR_MAX = 80000;

/* ------------------------------------------------------------ validação -- */

/**
 * Normaliza e valida o corpo recebido. Devolve [lead, erros]; erros vazio = ok.
 */
function validarLead(array $entrada): array
{
    $erros = [];

    $nome = trim(preg_replace('/\s+/u', ' ', (string) ($entrada['nome'] ?? '')));
    if (count(explode(' ', $nome)) < 2 || mb_strlen($nome) < 5 || mb_strlen($nome) > 120 || preg_match('/[\p{C}<>]/u', $nome)) {
        $erros['nome'] = 'Informe seu nome completo.';
    }

    $telefone = preg_replace('/\D/', '', (string) ($entrada['telefone'] ?? ''));
    if (!preg_match('/^[1-9]{2}9?\d{8}$/', $telefone)) {
        $erros['telefone'] = 'Informe um WhatsApp válido com DDD.';
    }

    $valor = filter_var($entrada['valor'] ?? null, FILTER_VALIDATE_INT);
    if ($valor === false || $valor < LEAD_VALOR_MIN || $valor > LEAD_VALOR_MAX) {
        $erros['valor'] = 'Valor fora da faixa permitida.';
    }

    $prazo = filter_var($entrada['prazo'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 140]]);
    if ($prazo === false) {
        $erros['prazo'] = 'Prazo inválido.';
    }

    $origem = mb_substr(preg_replace('/[\p{C}]/u', '', (string) ($entrada['origem'] ?? '/')), 0, 500);

    $lead = [
        'nome' => $nome,
        'telefone' => $telefone,
        'valor' => $valor,
        'prazo' => $prazo,
        'origem' => $origem,
        'utm' => extrairUtm($origem),
        'recebido_em' => date('c'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'navegador' => mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
    ];

    return [$lead, $erros];
}

function extrairUtm(string $origem): array
{
    parse_str((string) parse_url($origem, PHP_URL_QUERY), $query);
    $utm = [];
    foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'] as $chave) {
        if (isset($query[$chave]) && is_string($query[$chave])) $utm[$chave] = mb_substr($query[$chave], 0, 200);
    }
    return $utm;
}

/**
 * Limite simples por IP (arquivo no diretório temporário do sistema).
 * Devolve false quando o IP passou do limite na janela.
 */
function dentroDoLimite(string $ip, int $maximo = 10, int $janelaSegundos = 600): bool
{
    $arquivo = sys_get_temp_dir() . '/gm-leads-' . hash('sha256', $ip);
    $agora = time();
    $f = @fopen($arquivo, 'c+');
    if ($f === false) return true; // sem onde registrar, não bloqueia o lead

    flock($f, LOCK_EX);
    $marcas = array_filter(
        array_map('intval', explode(',', (string) stream_get_contents($f))),
        fn ($t) => $t > $agora - $janelaSegundos
    );
    $permitido = count($marcas) < $maximo;
    if ($permitido) $marcas[] = $agora;

    ftruncate($f, 0);
    rewind($f);
    fwrite($f, implode(',', $marcas));
    flock($f, LOCK_UN);
    fclose($f);

    return $permitido;
}

/* --------------------------------------------------------------- e-mail -- */

function moedaBrl(int $valor): string
{
    return 'R$ ' . number_format($valor, 0, ',', '.');
}

function telefoneFormatado(string $d): string
{
    return strlen($d) === 11
        ? sprintf('(%s) %s-%s', substr($d, 0, 2), substr($d, 2, 5), substr($d, 7))
        : sprintf('(%s) %s-%s', substr($d, 0, 2), substr($d, 2, 4), substr($d, 6));
}

function textoEmailLead(array $lead): string
{
    $linhas = [
        'Novo pedido de simulação pelo site.',
        '',
        'Nome:         ' . $lead['nome'],
        'WhatsApp:     ' . telefoneFormatado($lead['telefone']) . '  —  https://wa.me/55' . $lead['telefone'],
        'Valor:        ' . moedaBrl($lead['valor']),
        'Prazo:        ' . $lead['prazo'] . ' meses',
        '',
        'Página:       ' . $lead['origem'],
    ];
    foreach ($lead['utm'] as $chave => $valor) {
        $linhas[] = str_pad($chave . ':', 14) . $valor;
    }
    $linhas[] = '';
    $linhas[] = 'Recebido:     ' . date('d/m/Y H:i:s', strtotime($lead['recebido_em']));
    $linhas[] = 'IP:           ' . $lead['ip'];
    $linhas[] = 'Navegador:    ' . $lead['navegador'];
    return implode("\r\n", $linhas) . "\r\n";
}

/** Envia o lead por e-mail. Lança RuntimeException em caso de falha. */
function enviarEmailLead(array $lead): void
{
    $para = array_filter(array_map('trim', explode(',', (string) env('LEADS_EMAIL_PARA'))));
    $de = (string) env('LEADS_EMAIL_DE');
    if (!$para || $de === '') {
        throw new RuntimeException('LEADS_EMAIL_PARA / LEADS_EMAIL_DE não configurados');
    }
    foreach (array_merge($para, [$de]) as $endereco) {
        if (!filter_var($endereco, FILTER_VALIDATE_EMAIL)) throw new RuntimeException("e-mail inválido na configuração: {$endereco}");
    }

    $nomeRemetente = env('LEADS_EMAIL_DE_NOME', 'Site Grupo Meirelles');
    $assunto = 'Novo lead do site — ' . $lead['nome'] . ' — ' . moedaBrl($lead['valor']);
    $dominio = substr(strrchr($de, '@'), 1);

    $cabecalhos = [
        'Date' => date('r'),
        'From' => mb_encode_mimeheader($nomeRemetente, 'UTF-8', 'B') . " <{$de}>",
        'Message-ID' => '<' . bin2hex(random_bytes(12)) . "@{$dominio}>",
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8',
        'Content-Transfer-Encoding' => 'base64',
    ];
    $corpo = rtrim(chunk_split(base64_encode(textoEmailLead($lead)), 76, "\r\n"));
    $assuntoCodificado = mb_encode_mimeheader($assunto, 'UTF-8', 'B', "\r\n");

    if (env('SMTP_HOST')) {
        $mensagem = '';
        foreach ($cabecalhos + ['To' => implode(', ', $para), 'Subject' => $assuntoCodificado] as $k => $v) {
            $mensagem .= "{$k}: {$v}\r\n";
        }
        smtpEnviar($de, $para, $mensagem . "\r\n" . $corpo . "\r\n");
        return;
    }

    // Sem SMTP configurado: usa o mail() do servidor (sendmail da hospedagem).
    $linhas = [];
    foreach ($cabecalhos as $k => $v) $linhas[] = "{$k}: {$v}";
    if (!mail(implode(', ', $para), $assuntoCodificado, $corpo, implode("\r\n", $linhas), '-f' . $de)) {
        throw new RuntimeException('mail() recusou a mensagem');
    }
}

/**
 * Cliente SMTP mínimo (STARTTLS/SSL + AUTH LOGIN), sem dependências.
 * SMTP_SEGURANCA: "tls" (STARTTLS, porta 587), "ssl" (porta 465) ou "nenhuma".
 */
function smtpEnviar(string $de, array $para, string $mensagem): void
{
    $host = (string) env('SMTP_HOST');
    $seguranca = strtolower((string) env('SMTP_SEGURANCA', 'tls'));
    $porta = (int) env('SMTP_PORTA', $seguranca === 'ssl' ? '465' : '587');
    $usuario = (string) env('SMTP_USUARIO', '');
    $senha = (string) env('SMTP_SENHA', '');

    $contexto = stream_context_create(['ssl' => ['peer_name' => $host, 'verify_peer' => true, 'verify_peer_name' => true]]);
    $conexao = @stream_socket_client(
        ($seguranca === 'ssl' ? 'ssl://' : 'tcp://') . "{$host}:{$porta}",
        $codigoErro, $textoErro, 10, STREAM_CLIENT_CONNECT, $contexto
    );
    if ($conexao === false) throw new RuntimeException("SMTP: não conectou em {$host}:{$porta} ({$textoErro})");
    stream_set_timeout($conexao, 15);

    $ler = function (array $esperados) use ($conexao): string {
        $resposta = '';
        while (($linha = fgets($conexao, 1024)) !== false) {
            $resposta .= $linha;
            if (strlen($linha) < 4 || $linha[3] === ' ') break;
        }
        $codigo = (int) substr($resposta, 0, 3);
        if (!in_array($codigo, $esperados, true)) throw new RuntimeException('SMTP: resposta inesperada: ' . trim($resposta));
        return $resposta;
    };
    $comando = function (string $linha, array $esperados) use ($conexao, $ler): string {
        fwrite($conexao, $linha . "\r\n");
        return $ler($esperados);
    };

    try {
        $ler([220]);
        $ehlo = 'EHLO ' . (preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['SERVER_NAME'] ?? '')) ?: 'localhost');
        $comando($ehlo, [250]);

        if ($seguranca === 'tls') {
            $comando('STARTTLS', [220]);
            $metodo = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT') ? STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT : 0);
            if (!stream_socket_enable_crypto($conexao, true, $metodo)) throw new RuntimeException('SMTP: falha no STARTTLS');
            $comando($ehlo, [250]);
        }

        if ($usuario !== '') {
            $comando('AUTH LOGIN', [334]);
            $comando(base64_encode($usuario), [334]);
            $comando(base64_encode($senha), [235]);
        }

        $comando("MAIL FROM:<{$de}>", [250]);
        foreach ($para as $destino) $comando("RCPT TO:<{$destino}>", [250, 251]);
        $comando('DATA', [354]);
        // Linhas começando com "." precisam ser duplicadas (RFC 5321, 4.5.2).
        $comando(preg_replace('/^\./m', '..', $mensagem) . '.', [250]);
        $comando('QUIT', [221]);
    } finally {
        fclose($conexao);
    }
}

/* ------------------------------------------------------------------ CRM -- */

/** Formato enviado ao CRM. Ajuste aqui quando o CRM exigir outros nomes de campo. */
function payloadCrm(array $lead): array
{
    return [
        'nome' => $lead['nome'],
        'telefone' => '55' . $lead['telefone'],
        'valor' => $lead['valor'],
        'prazo' => $lead['prazo'],
        'origem' => $lead['origem'],
        'utm' => (object) $lead['utm'],
        'recebido_em' => $lead['recebido_em'],
        'ip' => $lead['ip'],
    ];
}

/**
 * Envia o lead ao CRM (POST JSON em CRM_URL). Não faz nada se CRM_URL estiver
 * vazio — devolve false. Lança RuntimeException em caso de falha.
 */
function enviarLeadCrm(array $lead): bool
{
    $url = env('CRM_URL');
    if (!$url) return false;

    postJson($url, payloadCrm($lead), env('CRM_TOKEN'), (float) env('CRM_TIMEOUT', '8'), 'CRM');
    return true;
}

/**
 * POST JSON em $url ("Authorization: Bearer <token>" se houver token).
 * Lança RuntimeException se não houver resposta ou se ela não for 2xx;
 * $nome identifica o destino na mensagem de erro.
 */
function postJson(string $url, array $dados, ?string $token, float $timeout, string $nome): void
{
    $cabecalhos = ['Content-Type: application/json', 'Accept: application/json'];
    if ($token) $cabecalhos[] = 'Authorization: Bearer ' . $token;

    $contexto = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $cabecalhos),
        'content' => json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'timeout' => $timeout,
        'ignore_errors' => true,
    ]]);

    $resposta = @file_get_contents($url, false, $contexto);
    $status = 0;
    foreach ($http_response_header ?? [] as $linha) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $linha, $m)) $status = (int) $m[1];
    }
    if ($status === 0) {
        throw new RuntimeException("{$nome} sem resposta em {$url}");
    }
    if ($status < 200 || $status >= 300) {
        throw new RuntimeException("{$nome} respondeu HTTP {$status}: " . mb_substr((string) $resposta, 0, 300));
    }
}
