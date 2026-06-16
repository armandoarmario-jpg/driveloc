<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/auth.php';

admin_required();

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM usuario WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: /admin/usuarios/listar.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';

    if (!$nome || !$email || !$tipo) {
        $erro = 'Preencha nome, email e tipo.';
    } else {
        $stmt = db()->prepare("SELECT id FROM usuario WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            $erro = 'Este email já está em uso por outro usuário.';
        } else {
            if ($nova_senha) {
                if (strlen($nova_senha) < 6) {
                    $erro = 'A nova senha deve ter no mínimo 6 caracteres.';
                } else {
                    $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    $stmt = db()->prepare("UPDATE usuario SET nome=?, email=?, telefone=?, tipo=?, senha=? WHERE id=?");
                    $stmt->execute([$nome, $email, $telefone, $tipo, $hash, $id]);
                    $sucesso = 'Usuário atualizado com sucesso!';
                }
            } else {
                $stmt = db()->prepare("UPDATE usuario SET nome=?, email=?, telefone=?, tipo=? WHERE id=?");
                $stmt->execute([$nome, $email, $telefone, $tipo, $id]);
                $sucesso = 'Usuário atualizado com sucesso!';
            }
            $user = db()->prepare("SELECT * FROM usuario WHERE id = ?");
            $user->execute([$id]);
            $user = $user->fetch();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - Admin</title>
    <link rel="stylesheet" href="/assets/style.css">
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
        <h1 class="page-title">Editar Usuário - <?= htmlspecialchars($user['nome']) ?></h1>

        <div class="admin-menu">
            <a href="/admin/dashboard.php">Dashboard</a>
            <a href="/admin/carros/listar.php">Carros</a>
            <a href="/admin/usuarios/listar.php">Usuários</a>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <div style="background:#fff;border-radius:8px;padding:25px;box-shadow:0 2px 8px rgba(0,0,0,0.08);max-width:600px;">
            <form method="POST">
                <div class="form-group">
                    <label for="nome">Nome *</label>
                    <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" name="telefone" id="telefone" value="<?= htmlspecialchars($user['telefone']) ?>">
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo *</label>
                    <select name="tipo" id="tipo" required>
                        <option value="comum" <?= $user['tipo'] === 'comum' ? 'selected' : '' ?>>Comum</option>
                        <option value="admin" <?= $user['tipo'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nova_senha">Nova senha (deixe em branco para manter)</label>
                    <input type="password" name="nova_senha" id="nova_senha" minlength="6">
                </div>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="/admin/usuarios/listar.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>
