<?php
/**
 * Diagnóstico TEMPORÁRIO da gravação de leads. Apague do servidor depois de usar.
 *
 * Uso: defina DIAG_TOKEN=<algo longo e aleatório> no .env e abra
 *   https://seudominio/api/diagnostico.php?token=<o mesmo valor>
 * Sem token válido responde 404. Não mostra a senha do banco. O teste de
 * gravação roda dentro de uma transação desfeita no fim (nada fica na tabela).
 */

declare(strict_types=1);

require __DIR__ . '/../inc/env.php';

$token = (string) env('DIAG_TOKEN', '');
if (strlen($token) < 16 || !hash_equals($token, (string) ($_GET['token'] ?? ''))) {
    http_response_code(404);
    exit;
}

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');

function linha(bool $ok, string $texto): void
{
    echo ($ok ? '[ OK ]  ' : '[FALHA] ') . $texto . "\n";
}

// 1. PHP
linha(PHP_VERSION_ID >= 80100, 'PHP ' . PHP_VERSION . ' (o envio do formulário exige 8.1 ou mais novo)');
linha(extension_loaded('pdo_mysql'), 'extensão pdo_mysql ' . (extension_loaded('pdo_mysql') ? 'carregada' : 'AUSENTE — ative no painel'));

// 2. .env
$candidatos = [dirname(__DIR__, 2) . '/.env', dirname(__DIR__) . '/.env'];
$usado = null;
foreach ($candidatos as $c) {
    if (is_readable($c)) { $usado = $c; break; }
}
linha($usado !== null, '.env ' . ($usado ? "lido de {$usado}" : 'NÃO encontrado em: ' . implode(' | ', $candidatos)));

// 3. Configuração do banco
$host = env('DB_HOST', 'localhost');
$porta = (int) env('DB_PORTA', '3306');
$nome = env('DB_NOME');
$usuario = env('DB_USUARIO');
linha((bool) $nome, 'DB_NOME = ' . ($nome ?: '(vazio — a gravação no banco fica desligada)'));
linha((bool) $usuario, 'DB_USUARIO = ' . ($usuario ?: '(vazio)'));
linha(env('DB_SENHA') !== null, 'DB_SENHA ' . (env('DB_SENHA') !== null ? 'preenchida' : '(vazia)'));
linha($host !== 'localhost', "DB_HOST = {$host}" . ($host === 'localhost' ? '  ← na Locaweb costuma ser o endereço do servidor MySQL mostrado no painel' : '') . " | porta {$porta}");

if (!$nome || !extension_loaded('pdo_mysql')) exit;

// 4. Conexão, tabela e permissão de gravação
try {
    $pdo = new PDO("mysql:host={$host};port={$porta};dbname={$nome};charset=utf8mb4", (string) $usuario, (string) env('DB_SENHA', ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    linha(true, 'conexão com o banco');
} catch (Throwable $e) {
    linha(false, 'conexão com o banco: ' . $e->getMessage());
    exit;
}

$tabela = $pdo->query("SHOW TABLES LIKE 'leads'")->fetchColumn();
linha((bool) $tabela, $tabela ? 'tabela leads existe' : 'tabela leads NÃO existe — importe db/schema.sql pelo phpMyAdmin');
if (!$tabela) exit;

try {
    $pdo->beginTransaction();
    $pdo->exec("INSERT INTO leads (nome, telefone, valor, prazo, recebido_em) VALUES ('Diagnóstico Teste', '61999999999', 2000, 72, NOW())");
    $pdo->exec("UPDATE leads SET api_status = 'enviado', api_tentativas = api_tentativas + 1 WHERE id = " . (int) $pdo->lastInsertId());
    $pdo->rollBack();
    linha(true, 'INSERT e UPDATE de teste (desfeitos em seguida)');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    linha(false, 'gravação de teste: ' . $e->getMessage());
}

// 5. Outros destinos do lead (para saber o que mais deveria estar recebendo)
echo "\n";
linha((bool) env('CRM_URL'), 'CRM_URL ' . (env('CRM_URL') ? 'preenchida' : 'vazia — chamada à API desligada'));
linha((bool) env('LEADS_EMAIL_PARA'), 'LEADS_EMAIL_PARA ' . (env('LEADS_EMAIL_PARA') ?: 'vazio — e-mail desligado'));
echo "\nApague este arquivo do servidor quando terminar.\n";
