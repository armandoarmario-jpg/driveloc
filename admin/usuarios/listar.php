<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/auth.php';

admin_required();

$usuarios = db()->query("SELECT * FROM usuario ORDER BY created_at DESC")->fetchAll();

$status_badges = ['ativo' => 'badge-ativo', 'inativo' => 'badge-inativo'];
$tipo_badges = ['admin' => 'badge-admin', 'comum' => 'badge-comum'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Admin</title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
    <header>
        <a href="/index.php" class="logo">DriveLoc</a>
        <nav>
            <a href="/index.php">Catálogo</a>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['user_nome']) ?></span>
                <a href="/logout.php">Sair</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Gerenciar Usuários</h1>

        <div class="admin-menu">
            <a href="/admin/dashboard.php">Dashboard</a>
            <a href="/admin/carros/listar.php">Carros</a>
            <a href="/admin/usuarios/listar.php" class="active">Usuários</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>#<?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['telefone']) ?></td>
                    <td><span class="badge <?= $tipo_badges[$u['tipo']] ?>"><?= ucfirst($u['tipo']) ?></span></td>
                    <td><span class="badge <?= $status_badges[$u['status']] ?>"><?= ucfirst($u['status']) ?></span></td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <a href="/admin/usuarios/editar.php?id=<?= $u['id'] ?>" class="btn btn-primary btn-xs">Editar</a>
                        <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                            <?php if ($u['status'] === 'ativo'): ?>
                                <a href="/admin/usuarios/inativar.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-xs">Inativar</a>
                            <?php else: ?>
                                <a href="/admin/usuarios/ativar.php?id=<?= $u['id'] ?>" class="btn btn-success btn-xs">Ativar</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
