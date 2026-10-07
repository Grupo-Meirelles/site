<?php
/**
 * Leads do formulário da página consignado-clt/: validação e envio à API.
 * Usado por api/leads-clt.php. Configuração CLT_API_* no .env — ver .env.example.
 *
 * Reaproveita de inc/leads.php o limite por IP (dentroDoLimite), a extração de
 * UTM (extrairUtm) e o POST JSON (postJson).
 */

declare(strict_types=1);

require_once __DIR__ . '/leads.php';

/**
 * Normaliza e valida o corpo recebido. Devolve [lead, erros]; erros vazio = ok.
 * As regras são as mesmas do js/emprestimo-clt.js — o servidor não confia no navegador.
 */
function validarLeadClt(array $entrada): array
{
    $erros = [];

    $nome = trim(preg_replace('/\s+/u', ' ', (string) ($entrada['nome'] ?? '')));
    if (count(explode(' ', $nome)) < 2 || mb_strlen($nome) < 5 || mb_strlen($nome) > 120 || preg_match('/[\p{C}<>]/u', $nome)) {
        $erros['nome'] = 'Informe nome e sobrenome.';
    }

    $cpf = preg_replace('/\D/', '', (string) ($entrada['cpf'] ?? ''));
    if (!cpfValido($cpf)) {
        $erros['cpf'] = 'CPF inválido. Confira os números.';
    }

    // Celular: DDD (11 a 99) + 9 + 8 dígitos.
    $telefone = preg_replace('/\D/', '', (string) ($entrada['telefone'] ?? ''));
    if (!preg_match('/^[1-9][1-9]9\d{8}$/', $telefone)) {
        $erros['telefone'] = 'Informe o DDD e o número com 9 dígitos.';
    }

    // Autorização LGPD de contato e uso dos dados — obrigatória.
    if (($entrada['aceite'] ?? false) !== true) {
        $erros['aceite'] = 'É preciso autorizar para continuar.';
    }

    $origem = mb_substr(preg_replace('/[\p{C}]/u', '', (string) ($entrada['origem'] ?? '/')), 0, 500);

    $lead = [
        'nome' => $nome,
        'cpf' => $cpf,
        'telefone' => $telefone,
        'origem' => $origem,
        'utm' => extrairUtm($origem),
        'recebido_em' => date('c'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'navegador' => mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
    ];

    return [$lead, $erros];
}

/** CPF com 11 dígitos e dígitos verificadores corretos (rejeita 000..., 111...). */
function cpfValido(string $cpf): bool
{
    if (!preg_match('/^\d{11}$/', $cpf) || preg_match('/^(\d)\1{10}$/', $cpf)) return false;
    for ($posicao = 9; $posicao <= 10; $posicao++) {
        $soma = 0;
        for ($i = 0; $i < $posicao; $i++) $soma += (int) $cpf[$i] * ($posicao + 1 - $i);
        $digito = ($soma * 10) % 11 % 10;
        if ($digito !== (int) $cpf[$posicao]) return false;
    }
    return true;
}

/** Formato enviado à API. Ajuste aqui quando a API exigir outros nomes de campo. */
function payloadClt(array $lead): array
{
    return [
        'nome' => $lead['nome'],
        'cpf' => $lead['cpf'],
        'telefone' => $lead['telefone'],
    ];
}

/**
 * Envia o lead à API (POST JSON em CLT_API_URL). Não faz nada se CLT_API_URL
 * estiver vazio — devolve false. Lança RuntimeException em caso de falha.
 */
function enviarLeadClt(array $lead): bool
{
    $url = env('CLT_API_URL');
    if (!$url) return false;

    postJson($url, payloadClt($lead), env('CLT_API_TOKEN'), (float) env('CLT_API_TIMEOUT', '8'), 'API CLT');
    return true;
}
