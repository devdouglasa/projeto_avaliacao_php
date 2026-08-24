
CREATE DATABASE IF NOT EXISTS `jm_informatica`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `jm_informatica`;

-- Usuários do sistema (funcionários)
CREATE TABLE IF NOT EXISTS `user` (
    `id_user`    BIGINT(20)   NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(150) NOT NULL,
    `email`      VARCHAR(100) NOT NULL,
    `password`   VARCHAR(45)  NOT NULL,
    `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
    `update_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `ativo`      TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id_user`),
    UNIQUE KEY `uk_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Serviços prestados pelos funcionários
-- Status: Pendente quando finished_at IS NULL; Finalizado quando finished_at possui data.
CREATE TABLE IF NOT EXISTS `service` (
    `id_service`      BIGINT(20)    NOT NULL AUTO_INCREMENT,
    `description`     VARCHAR(45)   NOT NULL,
    `price`           DECIMAL(11,3) NOT NULL,
    `created_at`      DATETIME      DEFAULT CURRENT_TIMESTAMP,
    `update_at`       DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `finished_at`     DATETIME      DEFAULT NULL,
    `commission_user` DECIMAL(11,3) DEFAULT NULL,
    `user_id_user`    BIGINT(20)    NOT NULL,
    PRIMARY KEY (`id_service`),
    KEY `idx_service_user` (`user_id_user`),
    KEY `idx_service_finished` (`finished_at`),
    CONSTRAINT `fk_service_user`
        FOREIGN KEY (`user_id_user`) REFERENCES `user` (`id_user`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Senhas armazenadas em SHA1 para caber em VARCHAR(45), conforme o modelo.
INSERT INTO `user` (`name`, `email`, `password`, `ativo`) VALUES
('José Silva',   'jose.silva@jminformatica.com',   SHA1('123456'), 1),
('Maria Santos', 'maria.santos@jminformatica.com', SHA1('123456'), 1),
('Carlos Souza', 'carlos.souza@jminformatica.com', SHA1('123456'), 1);

INSERT INTO `service`
    (`description`, `price`, `created_at`, `finished_at`, `commission_user`, `user_id_user`)
VALUES
('Troca de Tela de Notebook',      425.000,  '2026-08-10 09:00:00', NULL, NULL, 1),
('Conserto de carregador',         100.000,  '2026-08-12 10:30:00', '2026-08-15 16:00:00', 5.000, 1),
('Troca de pasta térmica',          80.000,  '2026-08-14 11:00:00', NULL, NULL, 1),
('Instalação de Office 2016',      150.000,  '2026-08-16 08:20:00', NULL, NULL, 1),
('Reparo de Sistema Operacional',  250.000,  '2026-08-18 14:10:00', NULL, NULL, 1),
('Troca de Memória',               180.000,  '2026-08-19 15:40:00', NULL, NULL, 1),
('Troca de Tela LED',              425.000,  '2026-08-20 09:15:00', NULL, NULL, 2),
('Limpeza de Computador',          100.000,  '2026-08-21 13:00:00', '2026-08-22 17:30:00', 5.000, 2),
('Formatação e backup',           1200.000,  '2026-08-11 10:00:00', '2026-08-13 18:00:00', 120.000, 3),
('Servidor e cabeamento',        12500.000,  '2026-08-05 08:00:00', NULL, NULL, 3);
