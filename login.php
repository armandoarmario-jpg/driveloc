<?php
require_once __DIR__ . '/app/config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        $stmt = db()->prepare("SELECT id, nome, tipo, senha, status FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            if ($user['status'] === 'ativo') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nome'] = $user['nome'];
                $_SESSION['user_tipo'] = $user['tipo'];
                header('Location: /index.php');
                exit;
            } else {
                $erro = 'Usuário inativo. Contate o administrador.';
            }
        } else {
            $erro = 'Email ou senha inválidos.';
        }
    } else {
        $erro = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DriveLoc</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header>
        <a href="/index.php" class="logo">DriveLoc</a>
        <nav>
            <a href="/index.php">Catálogo</a>
            <a href="/login.php">Entrar</a>
            <a href="/cadastro.php">Cadastrar</a>
        </nav>
    </header>

    <div class="auth-form">
        <h1>Entrar</h1>
        <?php if ($erro): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
        <div class="auth-links">
            <a href="/cadastro.php">Criar conta</a> &bull;
            <a href="/admin/index.php">Área do Admin</a>
        </div>
    </div>
</body>
</html>