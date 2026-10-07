-- Tabela de leads do formulário de simulação (MySQL 5.7+ / MariaDB 10.2+).
-- Rodar uma vez no banco configurado em DB_NOME:
--   mysql -u <usuario> -p <banco> < db/schema.sql

CREATE TABLE IF NOT EXISTS leads (
  id              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  nome            VARCHAR(120)     NOT NULL,
  telefone        VARCHAR(11)      NOT NULL COMMENT 'só dígitos: DDD + número',
  valor           INT UNSIGNED     NOT NULL COMMENT 'em reais',
  prazo           SMALLINT UNSIGNED NOT NULL COMMENT 'em meses',
  origem          VARCHAR(500)     NOT NULL DEFAULT '' COMMENT 'path + query string da página',
  utm             TEXT             NULL COMMENT 'JSON com utm_*, gclid, fbclid',
  ip              VARCHAR(45)      NOT NULL DEFAULT '',
  navegador       VARCHAR(300)     NOT NULL DEFAULT '',
  recebido_em     DATETIME         NOT NULL,

  -- Resultado da chamada à API (CRM_URL). "pendente" = ainda não enviado,
  -- seja porque a API está desligada ou porque o envio não chegou a acontecer.
  api_status      ENUM('pendente', 'enviado', 'falhou') NOT NULL DEFAULT 'pendente',
  api_tentativas  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  api_erro        VARCHAR(500)     NULL,
  api_enviado_em  DATETIME         NULL,

  PRIMARY KEY (id),
  KEY idx_recebido_em (recebido_em),
  KEY idx_api_status (api_status),
  KEY idx_telefone (telefone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leads do formulário da página consignado-clt/ (api/leads-clt.php).
-- CPF e telefone só com dígitos. Mesmo controle de envio à API (CLT_API_URL).
CREATE TABLE IF NOT EXISTS leads_clt (
  id              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  nome            VARCHAR(120)     NOT NULL,
  cpf             CHAR(11)         NOT NULL COMMENT 'só dígitos',
  telefone        VARCHAR(11)      NOT NULL COMMENT 'só dígitos: DDD + número',
  origem          VARCHAR(500)     NOT NULL DEFAULT '' COMMENT 'path + query string da página',
  utm             TEXT             NULL COMMENT 'JSON com utm_*, gclid, fbclid',
  ip              VARCHAR(45)      NOT NULL DEFAULT '',
  navegador       VARCHAR(300)     NOT NULL DEFAULT '',
  recebido_em     DATETIME         NOT NULL,

  api_status      ENUM('pendente', 'enviado', 'falhou') NOT NULL DEFAULT 'pendente',
  api_tentativas  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  api_erro        VARCHAR(500)     NULL,
  api_enviado_em  DATETIME         NULL,

  PRIMARY KEY (id),
  KEY idx_recebido_em (recebido_em),
  KEY idx_api_status (api_status),
  KEY idx_cpf (cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
