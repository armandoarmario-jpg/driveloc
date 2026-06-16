<?php
require_once __DIR__ . '/app/config.php';

$erro = '';
$sucesso = '';
$nome = $email = $telefone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha_confirm = $_POST['senha_confirm'] ?? '';

    if (!$nome || !$email || !$senha) {
        $erro = 'Preencha nome, email e senha.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } elseif ($senha !== $senha_confirm) {
        $erro = 'As senhas não conferem.';
    } else {
        $stmt = db()->prepare("SELECT id FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erro = 'Este email já está cadastrado.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = db()->prepare("INSERT INTO usuario (nome, email, telefone, senha, tipo, status) VALUES (?, ?, ?, ?, 'comum', 'ativo')");
            $stmt->execute([$nome, $email, $telefone, $hash]);
            $sucesso = 'Conta criada com sucesso! Faça login.';
            $nome = $email = $telefone = '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - DriveLoc</title>
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
        <h1>Criar Conta</h1>
        <?php if ($erro): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome completo</label>
                <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($nome) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" name="telefone" id="telefone" value="<?= htmlspecialchars($telefone) ?>" placeholder="(11) 91234-5678">
            </div>
            <div class="form-group">
                <label for="senha">Senha (mín. 6 caracteres)</label>
                <input type="password" name="senha" id="senha" minlength="6" required>
            </div>
            <div class="form-group">
                <label for="senha_confirm">Confirmar senha</label>
                <input type="password" name="senha_confirm" id="senha_confirm" minlength="6" required>
            </div>
            <button type="submit" class="btn btn-success">Cadastrar</button>
        </form>
        <div class="auth-links">
            Já tem conta? <a href="/login.php">Faça login</a>
        </div>
    </div>
</body>
</html>