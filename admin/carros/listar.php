<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/auth.php';

admin_required();

$carros = db()->query("SELECT c.*, u.nome AS anunciante FROM carro c JOIN usuario u ON c.usuario_id = u.id ORDER BY c.created_at DESC")->fetchAll();

$status_badges = ['ativo' => 'badge-ativo', 'inativo' => 'badge-inativo', 'vendido' => 'badge-vendido'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Carros - Admin</title>
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
        <h1 class="page-title">Gerenciar Carros</h1>

        <div class="admin-menu">
            <a href="<?= url('admin/dashboard.php') ?>">Dashboard</a>
            <a href="<?= url('admin/carros/listar.php') ?>" class="active">Carros</a>
            <a href="<?= url('admin/usuarios/listar.php') ?>">Usuários</a>
        </div>

        <div style="margin-bottom:20px;">
            <a href="<?= url('admin/carros/cadastrar.php') ?>" class="btn btn-success">+ Novo Carro</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Placa</th>
                    <th>Marca / Modelo</th>
                    <th>Ano</th>
                    <th>Preço</th>
                    <th>Anunciante</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carros as $c): ?>
                <tr>
                    <td>#<?= $c['id'] ?></td>
                    <td><strong><?= htmlspecialchars($c['placa']) ?></strong></td>
                    <td><?= htmlspecialchars($c['marca']) ?> <?= htmlspecialchars($c['modelo']) ?></td>
                    <td><?= $c['ano_fabricacao'] ?>/<?= $c['ano_modelo'] ?></td>
                    <td>R$ <?= number_format($c['preco'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($c['anunciante']) ?></td>
                    <td><span class="badge <?= $status_badges[$c['status']] ?>"><?= ucfirst($c['status']) ?></span></td>
                    <td>
                        <a href="<?= url('admin/carros/editar.php?id=' . $c['id']) ?>" class="btn btn-primary btn-xs">Editar</a>
                        <?php if ($c['status'] === 'ativo'): ?>
                            <a href="<?= url('admin/carros/inativar.php?id=' . $c['id']) ?>" class="btn btn-warning btn-xs">Inativar</a>
                        <?php else: ?>
                            <a href="<?= url('admin/carros/ativar.php?id=' . $c['id']) ?>" class="btn btn-success btn-xs">Ativar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
