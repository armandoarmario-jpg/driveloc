<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/auth.php';

admin_required();

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM carro WHERE id = ?");
$stmt->execute([$id]);
$carro = $stmt->fetch();

if (!$carro) {
    header('Location: ' . url('admin/carros/listar.php'));
    exit;
}

$erro = '';
$sucesso = '';

$usuarios = db()->query("SELECT id, nome FROM usuario WHERE status = 'ativo' ORDER BY nome")->fetchAll();

$combustiveis = ['gasolina' => 'Gasolina', 'etanol' => 'Etanol', 'flex' => 'Flex', 'diesel' => 'Diesel', 'eletrico' => 'Elétrico', 'hibrido' => 'Híbrido'];
$cambios = ['manual' => 'Manual', 'automatico' => 'Automático', 'semi-automatico' => 'Semi-Automático', 'cvt' => 'CVT'];
$carrocerias = ['sedan' => 'Sedã', 'hatch' => 'Hatch', 'suv' => 'SUV', 'pickup' => 'Pickup', 'coupe' => 'Coupé', 'conversivel' => 'Conversível', 'minivan' => 'Minivan', 'perua' => 'Perua'];
$estados = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

$dados = $carro;
$dados['image_path'] = $carro['image_path'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = strtoupper(trim($_POST['placa'] ?? ''));
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $ano_fabricacao = (int) ($_POST['ano_fabricacao'] ?? 0);
    $ano_modelo = (int) ($_POST['ano_modelo'] ?? 0);
    $cor = trim($_POST['cor'] ?? '');
    $combustivel = $_POST['combustivel'] ?? '';
    $quilometragem = (int) ($_POST['quilometragem'] ?? 0);
    $cambio = $_POST['cambio'] ?? '';
    $portas = (int) ($_POST['portas'] ?? 0);
    $carroceria = $_POST['carroceria'] ?? '';
    $preco = str_replace(',', '.', $_POST['preco'] ?? '0');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = $_POST['estado'] ?? '';
    $usuario_id = (int) ($_POST['usuario_id'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $imagem_path = $carro['image_path'];

    if (!$placa || !$marca || !$modelo || !$ano_fabricacao || !$preco || !$cidade || !$estado || !$usuario_id) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        if (!empty($_FILES['imagem']['name'])) {
            if ($_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
                $erro = 'Erro ao enviar a imagem.';
            } else {
                $fileType = mime_content_type($_FILES['imagem']['tmp_name']);
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($fileType, $allowedTypes, true)) {
                    $erro = 'Formato de imagem inválido. Use JPG, PNG ou GIF.';
                } elseif ($_FILES['imagem']['size'] > 2 * 1024 * 1024) {
                    $erro = 'A imagem deve ter no máximo 2MB.';
                } else {
                    $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
                    $filename = uniqid('car_', true) . '.' . $ext;
                    $destination = upload_path('cars/' . $filename);
                    if (!is_dir(dirname($destination))) {
                        mkdir(dirname($destination), 0755, true);
                    }
                    if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $destination)) {
                        $erro = 'Falha ao salvar a imagem.';
                    } else {
                        if (!empty($carro['image_path'])) {
                            $oldPath = upload_path(ltrim($carro['image_path'], '/'));
                            if (file_exists($oldPath)) {
                                @unlink($oldPath);
                            }
                        }
                        $imagem_path = 'uploads/cars/' . $filename;
                    }
                }
            }
        }

        if (!$erro) {
            $stmt = db()->prepare("SELECT id FROM carro WHERE placa = ? AND id != ?");
            $stmt->execute([$placa, $id]);
            if ($stmt->fetch()) {
                $erro = 'Esta placa já está cadastrada em outro veículo.';
            } else {
                $stmt = db()->prepare("UPDATE carro SET placa=?, marca=?, modelo=?, ano_fabricacao=?, ano_modelo=?, cor=?, combustivel=?, quilometragem=?, cambio=?, portas=?, carroceria=?, preco=?, descricao=?, image_path=?, cidade=?, estado=?, usuario_id=? WHERE id=?");
                $stmt->execute([$placa, $marca, $modelo, $ano_fabricacao, $ano_modelo, $cor, $combustivel, $quilometragem, $cambio, $portas, $carroceria, $preco, $descricao, $imagem_path, $cidade, $estado, $usuario_id, $id]);
                $sucesso = 'Carro atualizado com sucesso!';
                $dados = compact('placa','marca','modelo','ano_fabricacao','ano_modelo','cor','combustivel','quilometragem','cambio','portas','carroceria','preco','descricao','cidade','estado','usuario_id');
                $dados['image_path'] = $imagem_path;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Carro - Admin</title>
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
        <h1 class="page-title">Editar Carro - <?= htmlspecialchars($carro['placa']) ?></h1>

        <div class="admin-menu">
            <a href="<?= url('admin/dashboard.php') ?>">Dashboard</a>
            <a href="<?= url('admin/carros/listar.php') ?>">Carros</a>
            <a href="<?= url('admin/usuarios/listar.php') ?>">Usuários</a>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <div style="background:#fff;border-radius:8px;padding:25px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
            <form method="POST" enctype="multipart/form-data">
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:15px;">
                    <div class="form-group">
                        <label for="placa">Placa *</label>
                        <input type="text" name="placa" id="placa" value="<?= htmlspecialchars($dados['placa']) ?>" style="text-transform:uppercase;font-family:'Courier New',monospace;font-weight:700;" maxlength="10" required>
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca *</label>
                        <input type="text" name="marca" id="marca" value="<?= htmlspecialchars($dados['marca']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="modelo">Modelo *</label>
                        <input type="text" name="modelo" id="modelo" value="<?= htmlspecialchars($dados['modelo']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ano_fabricacao">Ano Fabricação *</label>
                        <input type="number" name="ano_fabricacao" id="ano_fabricacao" value="<?= htmlspecialchars($dados['ano_fabricacao']) ?>" min="1900" max="2099" required>
                    </div>
                    <div class="form-group">
                        <label for="ano_modelo">Ano Modelo</label>
                        <input type="number" name="ano_modelo" id="ano_modelo" value="<?= htmlspecialchars($dados['ano_modelo']) ?>" min="1900" max="2099">
                    </div>
                    <div class="form-group">
                        <label for="cor">Cor</label>
                        <input type="text" name="cor" id="cor" value="<?= htmlspecialchars($dados['cor']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="combustivel">Combustível *</label>
                        <select name="combustivel" id="combustivel" required>
                            <?php foreach ($combustiveis as $k => $v): ?>
                                <option value="<?= $k ?>" <?= $dados['combustivel'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cambio">Câmbio *</label>
                        <select name="cambio" id="cambio" required>
                            <?php foreach ($cambios as $k => $v): ?>
                                <option value="<?= $k ?>" <?= $dados['cambio'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quilometragem">Quilometragem</label>
                        <input type="number" name="quilometragem" id="quilometragem" value="<?= htmlspecialchars($dados['quilometragem']) ?>" min="0">
                    </div>
                    <div class="form-group">
                        <label for="portas">Portas *</label>
                        <select name="portas" id="portas" required>
                            <?php for ($i = 2; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= $dados['portas'] == $i ? 'selected' : '' ?>><?= $i ?> portas</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="carroceria">Carroceria *</label>
                        <select name="carroceria" id="carroceria" required>
                            <?php foreach ($carrocerias as $k => $v): ?>
                                <option value="<?= $k ?>" <?= $dados['carroceria'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="preco">Preço (R$) *</label>
                        <input type="text" name="preco" id="preco" value="<?= htmlspecialchars(number_format($dados['preco'], 2, ',', '.')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="cidade">Cidade *</label>
                        <input type="text" name="cidade" id="cidade" value="<?= htmlspecialchars($dados['cidade']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado *</label>
                        <select name="estado" id="estado" required>
                            <?php foreach ($estados as $uf): ?>
                                <option value="<?= $uf ?>" <?= $dados['estado'] === $uf ? 'selected' : '' ?>><?= $uf ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usuario_id">Anunciante *</label>
                        <select name="usuario_id" id="usuario_id" required>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= $dados['usuario_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea name="descricao" id="descricao"><?= htmlspecialchars($dados['descricao']) ?></textarea>
                </div>
                <?php if (!empty($dados['image_path'])): ?>
                    <div class="form-group">
                        <label>Imagem atual</label>
                        <img src="<?= url($dados['image_path']) ?>" alt="Imagem do carro" class="car-image-preview">
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="imagem">Atualizar imagem</label>
                    <input type="file" name="imagem" id="imagem" accept="image/jpeg,image/png,image/gif">
                    <small>Somente JPG, PNG ou GIF. Máx. 2MB.</small>
                </div>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="<?= url('admin/carros/listar.php') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>
