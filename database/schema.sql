CREATE DATABASE IF NOT EXISTS driveloc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE driveloc;

CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'comum') NOT NULL DEFAULT 'comum',
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE carro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10) NOT NULL UNIQUE,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    ano_fabricacao INT NOT NULL,
    ano_modelo INT NOT NULL,
    cor VARCHAR(30) NOT NULL,
    combustivel ENUM('gasolina', 'etanol', 'flex', 'diesel', 'eletrico', 'hibrido') NOT NULL,
    quilometragem INT NOT NULL,
    cambio ENUM('manual', 'automatico', 'semi-automatico', 'cvt') NOT NULL,
    portas TINYINT NOT NULL,
    carroceria ENUM('sedan', 'hatch', 'suv', 'pickup', 'coupe', 'conversivel', 'minivan', 'perua') NOT NULL,
    preco DECIMAL(11, 2) NOT NULL,
    descricao TEXT,
    image_path VARCHAR(255),
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    status ENUM('ativo', 'inativo', 'vendido') NOT NULL DEFAULT 'ativo',
    usuario_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
) ENGINE=InnoDB;

CREATE TABLE favorito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    carro_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (usuario_id, carro_id),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (carro_id) REFERENCES carro(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO usuario (nome, email, telefone, senha, tipo, status)
VALUES ('Administrador', 'admin@driveloc.com', '(11) 99999-9999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ativo');

INSERT INTO usuario (nome, email, telefone, senha, tipo, status) VALUES
('Carlos Silva', 'carlos@email.com', '(11) 91234-5678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'comum', 'ativo'),
('Maria Souza', 'maria@email.com', '(21) 98765-4321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'comum', 'ativo'),
('João Oliveira', 'joao@email.com', '(31) 99876-5432', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'comum', 'ativo'),
('Ana Costa', 'ana@email.com', '(41) 98765-1234', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'comum', 'ativo');

INSERT INTO carro (placa, marca, modelo, ano_fabricacao, ano_modelo, cor, combustivel, quilometragem, cambio, portas, carroceria, preco, descricao, cidade, estado, status, usuario_id) VALUES
('ABC1D23', 'Volkswagen', 'T-Cross 1.0 TSI', 2023, 2024, 'Branco', 'flex', 15000, 'automatico', 5, 'suv', 119990.00, 'SUV completo com central multimídia, câmera de ré e sensores. Único dono, revisões em dia.', 'São Paulo', 'SP', 'ativo', 2),
('EFG4H56', 'Fiat', 'Strada Volcano 1.3', 2022, 2023, 'Vermelho', 'flex', 32000, 'manual', 4, 'pickup', 89990.00, 'Pickup cabine dupla, ar condicionado, direção elétrica e vidros elétricos.', 'Belo Horizonte', 'MG', 'ativo', 3),
('IJK7L89', 'Chevrolet', 'Onix LT 1.0', 2021, 2022, 'Prata', 'flex', 45000, 'manual', 4, 'hatch', 65990.00, 'Hatch compacto econômico, perfeito para cidade. Manutenção em concessionária.', 'Rio de Janeiro', 'RJ', 'ativo', 4),
('MNO0P12', 'Toyota', 'Corolla Altis 2.0', 2023, 2024, 'Preto', 'flex', 8000, 'cvt', 4, 'sedan', 159990.00, 'Sedã premium com bancos em couro, teto solar, sensor de estacionamento e faréis Full LED.', 'Curitiba', 'PR', 'ativo', 5),
('QRS3T45', 'Honda', 'Civic Touring 1.5 Turbo', 2022, 2023, 'Azul', 'gasolina', 22000, 'cvt', 4, 'sedan', 149990.00, 'Sedã esportivo com motor turbo, painel digital, Honda Sensing completo e rodas aro 18.', 'Salvador', 'BA', 'ativo', 2);