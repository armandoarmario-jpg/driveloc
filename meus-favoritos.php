<?php
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/auth.php';

login_required();

$usuario_id = $_SESSION['user_id'];

$stmt = db()->prepare("SELECT c.*, u.nome AS anunciante FROM carro c JOIN favorito f ON f.carro_id = c.id JOIN usuario u ON c.usuario_id = u.id WHERE f.usuario_id = ? ORDER BY f.created_at DESC");
$stmt->execute([$usuario_id]);
$carros = $stmt->fetchAll();

$combustiveis = ['gasolina' => 'Gasolina', 'etanol' => 'Etanol', 'flex' => 'Flex', 'diesel' => 'Diesel', 'eletrico' => 'Elétrico', 'hibrido' => 'Híbrido'];
$cambios = ['manual' => 'Manual', 'automatico' => 'Automático', 'semi-automatico' => 'Semi-Automático', 'cvt' => 'CVT'];
$carrocerias = ['sedan' => 'Sedã', 'hatch' => 'Hatch', 'suv' => 'SUV', 'pickup' => 'Pickup', 'coupe' => 'Coupé', 'conversivel' => 'Conversível', 'minivan' => 'Minivan', 'perua' => 'Perua'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Favoritos - DriveLoc</title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
    <header>
        <a href="<?= url('index.php') ?>" class="logo">DriveLoc</a>
        <nav>
            <a href="<?= url('index.php') ?>">Catálogo</a>
            <a href="<?= url('meus-favoritos.php') ?>">Meus Favoritos</a>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['user_nome']) ?></span>
                <?php if ($_SESSION['user_tipo'] === 'admin'): ?>
                    <a href="<?= url('admin/dashboard.php') ?>">Painel Admin</a>
                <?php endif; ?>
                <a href="<?= url('logout.php') ?>">Sair</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Meus Favoritos</h1>

        <?php if (empty($carros)): ?>
            <p style="text-align:center;color:#888;padding:40px 0;font-size:1.1em;">Você ainda não favoritou nenhum carro.</p>
        <?php else: ?>
            <div class="car-grid">
                <?php foreach ($carros as $c): ?>
                    <div class="car-card">
                        <div class="placa"><?= htmlspecialchars($c['placa']) ?></div>
                        <div class="marca-modelo"><?= htmlspecialchars($c['marca']) ?> <?= htmlspecialchars($c['modelo']) ?></div>
                        <div class="ano-cor"><?= $c['ano_fabricacao'] ?> / <?= $c['ano_modelo'] ?> &mdash; <?= htmlspecialchars($c['cor']) ?></div>
                        <div class="detalhes">
                            <span>⚡ <?= $combustiveis[$c['combustivel']] ?></span>
                            <span>⚙ <?= $cambios[$c['cambio']] ?></span>
                            <span>📏 <?= number_format($c['quilometragem'], 0, ',', '.') ?> km</span>
                            <span>🚪 <?= $c['portas'] ?> portas</span>
                            <span>🚗 <?= $carrocerias[$c['carroceria']] ?></span>
                        </div>
                        <div class="cidade">📍 <?= htmlspecialchars($c['cidade']) ?>/<?= $c['estado'] ?></div>
                        <div class="preco">R$ <?= number_format($c['preco'], 2, ',', '.') ?></div>
                        <div class="actions">
                            <form method="POST" action="<?= url('favoritar.php') ?>">
                                <input type="hidden" name="carro_id" value="<?= $c['id'] ?>">
                                <button type="submit" class="btn-favoritar favoritado">❤️ Remover dos Favoritos</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>