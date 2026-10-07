<?php
/**
 * Banco de dados dos leads (MySQL/MariaDB via PDO). Usado por api/leads.php.
 * Configuração DB_* no .env; tabela em db/schema.sql.
 *
 * Cada envio do formulário vira uma linha em `leads` antes de qualquer outro
 * destino. O resultado da chamada à API fica registrado na mesma linha
 * (api_status, api_erro...), para dar para reenviar o que falhou.
 */

declare(strict_types=1);

require_once __DIR__ . '/env.php';

/**
 * Conexão única por request. Devolve null se DB_NOME estiver vazio (etapa
 * desligada). Lança PDOException se o banco não responder.
 */
function conexaoBanco(): ?PDO
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $nome = env('DB_NOME');
    if (!$nome) return null;

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        env('DB_HOST', 'localhost'),
        (int) env('DB_PORTA', '3306'),
        $nome
    );
    $pdo = new PDO($dsn, (string) env('DB_USUARIO', ''), (string) env('DB_SENHA', ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $pdo->exec("SET time_zone = '" . date('P') . "'");
    return $pdo;
}

/** Grava o lead e devolve o id da linha. */
function salvarLead(PDO $pdo, array $lead): int
{
    $sql = 'INSERT INTO leads
              (nome, telefone, valor, prazo, origem, utm, ip, navegador, recebido_em, api_status)
            VALUES
              (:nome, :telefone, :valor, :prazo, :origem, :utm, :ip, :navegador, :recebido_em, :api_status)';
    $pdo->prepare($sql)->execute([
        'nome' => $lead['nome'],
        'telefone' => $lead['telefone'],
        'valor' => $lead['valor'],
        'prazo' => $lead['prazo'],
        'origem' => $lead['origem'],
        'utm' => json_encode((object) $lead['utm'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'ip' => $lead['ip'],
        'navegador' => $lead['navegador'],
        'recebido_em' => date('Y-m-d H:i:s', strtotime($lead['recebido_em'])),
        'api_status' => 'pendente',
    ]);
    return (int) $pdo->lastInsertId();
}

/** Grava o lead da página consignado-clt/ (tabela leads_clt) e devolve o id. */
function salvarLeadClt(PDO $pdo, array $lead): int
{
    $sql = 'INSERT INTO leads_clt
              (nome, cpf, telefone, origem, utm, ip, navegador, recebido_em, api_status)
            VALUES
              (:nome, :cpf, :telefone, :origem, :utm, :ip, :navegador, :recebido_em, :api_status)';
    $pdo->prepare($sql)->execute([
        'nome' => $lead['nome'],
        'cpf' => $lead['cpf'],
        'telefone' => $lead['telefone'],
        'origem' => $lead['origem'],
        'utm' => json_encode((object) $lead['utm'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'ip' => $lead['ip'],
        'navegador' => $lead['navegador'],
        'recebido_em' => date('Y-m-d H:i:s', strtotime($lead['recebido_em'])),
        'api_status' => 'pendente',
    ]);
    return (int) $pdo->lastInsertId();
}

/**
 * Registra o resultado da chamada à API na linha do lead.
 * $status: "enviado" ou "falhou". Com a URL da API vazia a chamada não
 * acontece e a linha fica "pendente" — pronta para ser enviada quando a API
 * for ligada. $tabela: "leads" (home) ou "leads_clt" (consignado-clt/).
 */
function registrarEnvioApi(PDO $pdo, int $id, string $status, ?string $erro = null, string $tabela = 'leads'): void
{
    if (!in_array($tabela, ['leads', 'leads_clt'], true)) {
        throw new InvalidArgumentException("tabela de leads desconhecida: {$tabela}");
    }
    $sql = 'UPDATE ' . $tabela . '
               SET api_status = :status,
                   api_erro = :erro,
                   api_tentativas = api_tentativas + 1,
                   api_enviado_em = IF(:status2 = \'enviado\', NOW(), api_enviado_em)
             WHERE id = :id';
    $pdo->prepare($sql)->execute([
        'status' => $status,
        'status2' => $status,
        'erro' => $erro === null ? null : mb_substr($erro, 0, 500),
        'id' => $id,
    ]);
}
