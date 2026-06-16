<?php
require_once __DIR__ . '/app/config.php';

$marcas = db()->query("SELECT DISTINCT marca FROM carro WHERE status = 'ativo' ORDER BY marca")->fetchAll(PDO::FETCH_COLUMN);

$where = ["c.status = 'ativo'"];
$params = [];

if (!empty($_GET['marca'])) {
    $where[] = 'c.marca = ?';
    $params[] = $_GET['marca'];
}
if (!empty($_GET['modelo'])) {
    $where[] = 'c.modelo LIKE ?';
    $params[] = '%' . $_GET['modelo'] . '%';
}
if (!empty($_GET['preco_min'])) {
    $where[] = 'c.preco >= ?';
    $params[] = $_GET['preco_min'];
}
if (!empty($_GET['preco_max'])) {
    $where[] = 'c.preco <= ?';
    $params[] = $_GET['preco_max'];
}
if (!empty($_GET['combustivel'])) {
    $where[] = 'c.combustivel = ?';
    $params[] = $_GET['combustivel'];
}
if (!empty($_GET['cambio'])) {
    $where[] = 'c.cambio = ?';
    $params[] = $_GET['cambio'];
}

$sql = "SELECT c.*, u.nome AS anunciante FROM carro c JOIN usuario u ON c.usuario_id = u.id WHERE " . implode(' AND ', $where) . " ORDER BY c.created_at DESC";
$carros = db()->prepare($sql);
$carros->execute($params);
$carros = $carros->fetchAll();

$favoritos = [];
if (isset($_SESSION['user_id'])) {
    $stmt = db()->prepare("SELECT carro_id FROM favorito WHERE usuario_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $favoritos = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$combustiveis = ['gasolina' => 'Gasolina', 'etanol' => 'Etanol', 'flex' => 'Flex', 'diesel' => 'Diesel', 'eletrico' => 'Elétrico', 'hibrido' => 'Híbrido'];
$cambios = ['manual' => 'Manual', 'automatico' => 'Automático', 'semi-automatico' => 'Semi-Automático', 'cvt' => 'CVT'];
$carrocerias = ['sedan' => 'Sedã', 'hatch' => 'Hatch', 'suv' => 'SUV', 'pickup' => 'Pickup', 'coupe' => 'Coupé', 'conversivel' => 'Conversível', 'minivan' => 'Minivan', 'perua' => 'Perua'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - DriveLoc</title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
    <header>
        <a href="<?= url('index.php') ?>" class="logo">DriveLoc</a>
        <nav>
            <a href="<?= url('index.php') ?>">Catálogo</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= url('meus-favoritos.php') ?>">Meus Favoritos</a>
                <div class="user-info">
                    <span><?= htmlspecialchars($_SESSION['user_nome']) ?></span>
                    <?php if ($_SESSION['user_tipo'] === 'admin'): ?>
                        <a href="<?= url('admin/dashboard.php') ?>">Painel Admin</a>
                    <?php endif; ?>
                    <a href="<?= url('logout.php') ?>">Sair</a>
                </div>
            <?php else: ?>
                <a href="<?= url('login.php') ?>">Entrar</a>
                <a href="<?= url('cadastro.php') ?>">Cadastrar</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Catálogo de Carros</h1>

        <div class="filters">
            <form method="GET">
                <div class="form-group">
                    <label for="marca">Marca</label>
                    <select name="marca" id="marca">
                        <option value="">Todas</option>
                        <?php foreach ($marcas as $m): ?>
                            <option value="<?= htmlspecialchars($m) ?>" <?= ($_GET['marca'] ?? '') === $m ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modelo">Modelo</label>
                    <input type="text" name="modelo" id="modelo" value="<?= htmlspecialchars($_GET['modelo'] ?? '') ?>" placeholder="Buscar modelo">
                </div>
                <div class="form-group">
                    <label for="preco_min">Preço mín.</label>
                    <input type="number" name="preco_min" id="preco_min" step="0.01" value="<?= htmlspecialchars($_GET['preco_min'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="preco_max">Preço máx.</label>
                    <input type="number" name="preco_max" id="preco_max" step="0.01" value="<?= htmlspecialchars($_GET['preco_max'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="combustivel">Combustível</label>
                    <select name="combustivel" id="combustivel">
                        <option value="">Todos</option>
                        <?php foreach ($combustiveis as $k => $v): ?>
                            <option value="<?= $k ?>" <?= ($_GET['combustivel'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cambio">Câmbio</label>
                    <select name="cambio" id="cambio">
                        <option value="">Todos</option>
                        <?php foreach ($cambios as $k => $v): ?>
                            <option value="<?= $k ?>" <?= ($_GET['cambio'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="<?= url('index.php') ?>" class="btn btn-secondary">Limpar</a>
                </div>
            </form>
        </div>

        <?php if (empty($carros)): ?>
            <p style="text-align:center;color:#888;padding:40px 0;font-size:1.1em;">Nenhum carro encontrado.</p>
        <?php else: ?>
            <div class="car-grid">
                <?php foreach ($carros as $c): ?>
                    <div class="car-card">
                        <?php if (!empty($c['image_path'])): ?>
                            <img src="<?= url($c['image_path']) ?>" alt="<?= htmlspecialchars($c['marca'] . ' ' . $c['modelo']) ?>" class="car-image">
                        <?php endif; ?>
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
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="POST" action="<?= url('favoritar.php') ?>">
                                    <input type="hidden" name="carro_id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn-favoritar <?= in_array($c['id'], $favoritos) ? 'favoritado' : '' ?>">
                                        <?= in_array($c['id'], $favoritos) ? '❤️ Favoritado' : '🤍 Favoritar' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>