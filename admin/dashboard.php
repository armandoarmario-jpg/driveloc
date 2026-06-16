<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';

admin_required();

$carros_ativos = db()->query("SELECT COUNT(*) FROM carro WHERE status = 'ativo'")->fetchColumn();
$carros_vendidos = db()->query("SELECT COUNT(*) FROM carro WHERE status = 'vendido'")->fetchColumn();
$usuarios_ativos = db()->query("SELECT COUNT(*) FROM usuario WHERE status = 'ativo'")->fetchColumn();
$preco_medio = db()->query("SELECT COALESCE(ROUND(AVG(preco), 2), 0) FROM carro WHERE status = 'ativo'")->fetchColumn();
$total_favoritos = db()->query("SELECT COUNT(*) FROM favorito")->fetchColumn();

$ultimos = db()->query("SELECT c.*, u.nome AS anunciante FROM carro c JOIN usuario u ON c.usuario_id = u.id ORDER BY c.created_at DESC LIMIT 5")->fetchAll();

$status_badges = ['ativo' => 'badge-ativo', 'inativo' => 'badge-inativo', 'vendido' => 'badge-vendido'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
    <header>
        <a href="<?= url('index.php') ?>" class="logo">DriveLoc</a>
        <nav>
            <a href="<?= url('index.php') ?>">Catálogo</a>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['user_nome']) ?></span>
                <a href="<?= url('logout.php') ?>">Sair</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Painel Administrativo</h1>

        <div class="admin-menu">
            <a href="<?= url('admin/dashboard.php') ?>" class="active">Dashboard</a>
            <a href="<?= url('admin/carros/listar.php') ?>">Carros</a>
            <a href="<?= url('admin/usuarios/listar.php') ?>">Usuários</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $carros_ativos ?></div>
                <div class="stat-label">Carros Ativos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $carros_vendidos ?></div>
                <div class="stat-label">Vendidos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $usuarios_ativos ?></div>
                <div class="stat-label">Usuários Ativos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">R$ <?= number_format($preco_medio, 2, ',', '.') ?></div>
                <div class="stat-label">Preço Médio</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $total_favoritos ?></div>
                <div class="stat-label">Total Favoritos</div>
            </div>
        </div>

        <h2 style="font-size:1.3em;color:#444;margin-bottom:15px;">Últimos Carros Cadastrados</h2>

        <table>
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Marca / Modelo</th>
                    <th>Ano</th>
                    <th>Preço</th>
                    <th>Anunciante</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimos as $c): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['placa']) ?></strong></td>
                    <td><?= htmlspecialchars($c['marca']) ?> <?= htmlspecialchars($c['modelo']) ?></td>
                    <td><?= $c['ano_fabricacao'] ?>/<?= $c['ano_modelo'] ?></td>
                    <td>R$ <?= number_format($c['preco'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($c['anunciante']) ?></td>
                    <td><span class="badge <?= $status_badges[$c['status']] ?>"><?= ucfirst($c['status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
