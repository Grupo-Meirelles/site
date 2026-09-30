<?php
/**
 * POST api/leads.php — recebe o lead do formulário do hero.
 *
 * 1. salva os dados no banco (DB_* no .env; pulado se DB_NOME vazio)
 * 2. envia os dados à API (CRM_URL / CRM_TOKEN no .env; pulado se vazio) e
 *    registra o resultado na linha do lead
 * 3. envia os dados por e-mail (LEADS_EMAIL_*, SMTP_* no .env)
 *
 * Responde 200 se ao menos um dos destinos recebeu o lead — assim uma queda
 * em um deles não faz o visitante ver erro. Falhas vão para o log do PHP.
 */

declare(strict_types=1);

require __DIR__ . '/../inc/leads.php';
require __DIR__ . '/../inc/db.php';

date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function responder(int $status, array $corpo): never
{
    http_response_code($status);
    echo json_encode($corpo, JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    responder(405, ['ok' => false, 'erro' => 'Método não permitido.']);
}

$entrada = json_decode((string) file_get_contents('php://input', false, null, 0, 10000), true);
if (!is_array($entrada)) {
    responder(400, ['ok' => false, 'erro' => 'Corpo inválido.']);
}

// Honeypot: campo invisível para pessoas. Robô preencheu -> finge sucesso e descarta.
if (!empty($entrada['empresa'])) {
    responder(200, ['ok' => true]);
}

if (!dentroDoLimite($_SERVER['REMOTE_ADDR'] ?? '')) {
    responder(429, ['ok' => false, 'erro' => 'Muitas tentativas. Tente de novo em alguns minutos.']);
}

[$lead, $erros] = validarLead($entrada);
if ($erros) {
    responder(422, ['ok' => false, 'erro' => 'Confira os campos.', 'campos' => $erros]);
}

$entregue = false;

// 1. Banco. Se falhar, o lead ainda segue para a API e o e-mail.
$pdo = null;
$idLead = null;
try {
    $pdo = conexaoBanco();
    if ($pdo) {
        $idLead = salvarLead($pdo, $lead);
        $entregue = true;
    }
} catch (Throwable $e) {
    error_log('[leads] banco falhou: ' . $e->getMessage());
}

// 2. API, com o resultado gravado na linha salva no passo 1.
$statusApi = null;
$erroApi = null;
try {
    if (enviarLeadCrm($lead)) {
        $statusApi = 'enviado';
        $entregue = true;
    }
} catch (Throwable $e) {
    $statusApi = 'falhou';
    $erroApi = $e->getMessage();
    error_log('[leads] API falhou: ' . $erroApi);
}
if ($statusApi && $idLead) {
    try {
        registrarEnvioApi($pdo, $idLead, $statusApi, $erroApi);
    } catch (Throwable $e) {
        error_log("[leads] banco: não registrou o envio à API do lead {$idLead}: " . $e->getMessage());
    }
}

// 3. E-mail.
try {
    enviarEmailLead($lead);
    $entregue = true;
} catch (Throwable $e) {
    error_log('[leads] e-mail falhou: ' . $e->getMessage());
}

if (!$entregue) {
    responder(502, ['ok' => false, 'erro' => 'Não foi possível registrar o pedido agora.']);
}

responder(200, ['ok' => true]);
