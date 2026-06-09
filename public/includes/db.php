<?php
// Persistência em JSON para demonstração acadêmica
$DATA_DIR = __DIR__ . '/../data';
if (!is_dir($DATA_DIR)) mkdir($DATA_DIR, 0777, true);

$DB_FILE = $DATA_DIR . '/db.json';

function uid() {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
}

function getDB(): array {
    global $DB_FILE;
    if (file_exists($DB_FILE)) {
        $data = json_decode(file_get_contents($DB_FILE), true);
        if ($data) return $data;
    }
    // seed inicial
    $db = [
        'vehicles' => [
            ['id' => uid(), 'placa' => 'ABC-1D23', 'marca' => 'Toyota', 'modelo' => 'Corolla', 'ano' => 2023, 'cor' => 'Prata', 'diaria' => 180, 'status' => 'disponivel'],
            ['id' => uid(), 'placa' => 'DEF-4E56', 'marca' => 'Honda', 'modelo' => 'Civic', 'ano' => 2022, 'cor' => 'Preto', 'diaria' => 200, 'status' => 'alugado'],
            ['id' => uid(), 'placa' => 'GHI-7F89', 'marca' => 'Volkswagen', 'modelo' => 'Nivus', 'ano' => 2024, 'cor' => 'Branco', 'diaria' => 220, 'status' => 'disponivel'],
            ['id' => uid(), 'placa' => 'JKL-0G12', 'marca' => 'Fiat', 'modelo' => 'Pulse', 'ano' => 2023, 'cor' => 'Vermelho', 'diaria' => 160, 'status' => 'manutencao'],
            ['id' => uid(), 'placa' => 'MNO-3H45', 'marca' => 'Jeep', 'modelo' => 'Compass', 'ano' => 2024, 'cor' => 'Cinza', 'diaria' => 320, 'status' => 'disponivel'],
            ['id' => uid(), 'placa' => 'PQR-6I78', 'marca' => 'Hyundai', 'modelo' => 'HB20', 'ano' => 2022, 'cor' => 'Azul', 'diaria' => 140, 'status' => 'alugado'],
        ],
        'rentals' => [],
        'users' => [
            ['id' => uid(), 'nome' => 'Admin Master', 'email' => 'admin@driveloc.com', 'role' => 'admin', 'status' => 'ativo', 'criadoEm' => date('c')],
            ['id' => uid(), 'nome' => 'João Operador', 'email' => 'joao@driveloc.com', 'role' => 'operador', 'status' => 'ativo', 'criadoEm' => date('c')],
            ['id' => uid(), 'nome' => 'Maria Silva', 'email' => 'maria@driveloc.com', 'role' => 'operador', 'status' => 'pendente', 'criadoEm' => date('c')],
        ],
    ];
    saveDB($db);
    return $db;
}

function saveDB(array $db): void {
    global $DB_FILE;
    file_put_contents($DB_FILE, json_encode($db, JSON_PRETTY_PRINT));
}

function fmtBRL(float $n): string {
    return 'R$ ' . number_format($n, 2, ',', '.');
}
function fmtDate(string $iso): string {
    return date('d/m/Y', strtotime($iso));
}

function badgeClass(string $status): string {
    return match ($status) {
        'disponivel', 'ativo' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'alugado', 'ativa' => 'bg-blue-100 text-blue-700 border-blue-200',
        'manutencao', 'pendente' => 'bg-amber-100 text-amber-700 border-amber-200',
        'bloqueado' => 'bg-rose-100 text-rose-700 border-rose-200',
        'encerrada' => 'bg-slate-100 text-slate-600 border-slate-200',
        default => 'bg-gray-100 text-gray-700',
    };
}
function badgeLabel(string $status): string {
    return match ($status) {
        'disponivel' => 'Disponível',
        'alugado' => 'Alugado',
        'manutencao' => 'Manutenção',
        'ativo' => 'Ativo',
        'pendente' => 'Pendente',
        'bloqueado' => 'Bloqueado',
        'ativa' => 'Ativa',
        'encerrada' => 'Encerrada',
        default => ucfirst($status),
    };
}

function h(string $t): string {
    return htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
}
