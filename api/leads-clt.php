<?php
/**
 * POST api/leads-clt.php — recebe o lead do formulário da página consignado-clt/.
 * Corpo JSON: { nome, cpf, telefone, aceite, origem, empresa }
 *
 * 1. salva os dados no banco, tabela leads_clt (DB_* no .env; pulado se DB_NOME vazio)
 * 2. envia nome, cpf e telefone à API (CLT_API_URL / CLT_API_TOKEN no .env;
 *    pulado se vazio) e registra o resultado na linha do lead
 *
 * Responde 200 se ao menos um dos destinos recebeu o lead — assim uma queda
 * em um deles não faz o visitante ver erro. Falhas vão para o log do PHP.
 * Sem e-mail de propósito: o lead tem CPF, que não deve circular por e-mail.
 */

declare(strict_types=1);

require __DIR__ . '/../inc/leads-clt.php';
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

[$lead, $erros] = validarLeadClt($entrada);
if ($erros) {
    responder(422, ['ok' => false, 'erro' => 'Confira os campos.', 'campos' => $erros]);
}

$entregue = false;

// 1. Banco. Se falhar, o lead ainda segue para a API.
$pdo = null;
$idLead = null;
try {
    $pdo = conexaoBanco();
    if ($pdo) {
        $idLead = salvarLeadClt($pdo, $lead);
        $entregue = true;
    }
} catch (Throwable $e) {
    error_log('[leads-clt] banco falhou: ' . $e->getMessage());
}

// 2. API, com o resultado gravado na linha salva no passo 1.
$statusApi = null;
$erroApi = null;
try {
    if (enviarLeadClt($lead)) {
        $statusApi = 'enviado';
        $entregue = true;
    }
} catch (Throwable $e) {
    $statusApi = 'falhou';
    $erroApi = $e->getMessage();
    error_log('[leads-clt] API falhou: ' . $erroApi);
}
if ($statusApi && $idLead) {
    try {
        registrarEnvioApi($pdo, $idLead, $statusApi, $erroApi, 'leads_clt');
    } catch (Throwable $e) {
        error_log("[leads-clt] banco: não registrou o envio à API do lead {$idLead}: " . $e->getMessage());
    }
}

if (!$entregue) {
    responder(502, ['ok' => false, 'erro' => 'Não foi possível registrar o pedido agora.']);
}

responder(200, ['ok' => true]);
