<?php
require __DIR__ . '/includes/db.php';
$db = getDB();
$title = 'Veículos';
$page = '/veiculos.php';

// CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $db['vehicles'][] = [
            'id' => uid(),
            'placa' => strtoupper($_POST['placa'] ?? ''),
            'marca' => $_POST['marca'] ?? '',
            'modelo' => $_POST['modelo'] ?? '',
            'ano' => (int)($_POST['ano'] ?? date('Y')),
            'cor' => $_POST['cor'] ?? '',
            'diaria' => (float)($_POST['diaria'] ?? 0),
            'status' => $_POST['status'] ?? 'disponivel',
        ];
        saveDB($db);
        header('Location: /veiculos.php?msg=Veículo+cadastrado');
        exit;
    } elseif ($action === 'edit') {
        foreach ($db['vehicles'] as &$v) {
            if ($v['id'] === ($_POST['id'] ?? '')) {
                $v['placa'] = strtoupper($_POST['placa'] ?? '');
                $v['marca'] = $_POST['marca'] ?? '';
                $v['modelo'] = $_POST['modelo'] ?? '';
                $v['ano'] = (int)($_POST['ano'] ?? 0);
                $v['cor'] = $_POST['cor'] ?? '';
                $v['diaria'] = (float)($_POST['diaria'] ?? 0);
                $v['status'] = $_POST['status'] ?? 'disponivel';
            }
        }
        saveDB($db);
        header('Location: /veiculos.php?msg=Veículo+atualizado');
        exit;
    } elseif ($action === 'delete') {
        $db['vehicles'] = array_values(array_filter($db['vehicles'], fn($v) => $v['id'] !== ($_POST['id'] ?? '')));
        saveDB($db);
        header('Location: /veiculos.php?msg=Veículo+removido');
        exit;
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="flex items-end justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-3xl font-bold tracking-tight">Veículos</h1>
        <p class="text-slate-500 mt-1">Gerencie a frota completa da locadora.</p>
    </div>
    <a href="#" onclick="document.getElementById('modalAdd').classList.remove('hidden');return false;" class="inline-flex items-center gap-2 bg-primary text-primary-foreground px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">+ Novo veículo</a>
</div>

<?php if (!empty($_GET['msg'])): ?>
    <div class="bg-emerald-100 text-emerald-700 border border-emerald-200 p-3 rounded-lg text-sm mb-4"><?= h($_GET['msg']) ?></div>
<?php endif; ?>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($db['vehicles'] as $v): ?>
        <div class="bg-white border rounded-xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-600 grid place-items-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H20.625a1.125 1.125 0 0 1-1.125-1.125V14.25"/></svg>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= badgeClass($v['status']) ?>"><?= badgeLabel($v['status']) ?></span>
            </div>
            <h3 class="font-semibold text-lg leading-tight"><?= h($v['marca']) ?> <?= h($v['modelo']) ?></h3>
            <p class="text-sm text-slate-500"><?= $v['ano'] ?> • <?= h($v['cor']) ?> • <span class="font-mono"><?= h($v['placa']) ?></span></p>
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm"><span class="text-slate-500">Diária</span><div class="font-bold text-blue-600"><?= fmtBRL($v['diaria']) ?></div></div>
                <div class="flex gap-1">
                    <button onclick="openEdit(<?= htmlspecialchars(json_encode($v), ENT_QUOTES) ?>)" class="p-2 hover:bg-slate-100 rounded-md"><svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg></button>
                    <form method="post" class="inline" onsubmit="return confirm('Excluir <?= h($v['marca']) ?> <?= h($v['modelo']) ?>?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= h($v['id']) ?>">
                        <button type="submit" class="p-2 hover:bg-slate-100 rounded-md"><svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg></button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Add -->
<div id="modalAdd" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Cadastrar veículo</h2>
        <form method="post" class="grid grid-cols-2 gap-3">
            <input type="hidden" name="action" value="add">
            <div class="col-span-2"><label class="text-sm text-slate-600">Placa</label><input name="placa" required class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">Marca</label><input name="marca" required class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">Modelo</label><input name="modelo" required class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">Ano</label><input name="ano" type="number" value="<?= date('Y') ?>" class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">Cor</label><input name="cor" class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">Diária (R$)</label><input name="diaria" type="number" step="0.01" value="150" class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div>
                <label class="text-sm text-slate-600">Status</label>
                <select name="status" class="w-full border rounded-md px-3 py-2 text-sm mt-1 bg-white">
                    <option value="disponivel">Disponível</option>
                    <option value="alugado">Alugado</option>
                    <option value="manutencao">Manutenção</option>
                </select>
            </div>
            <div class="col-span-2 flex justify-end gap-2 mt-2">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-sm hover:bg-slate-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm hover:bg-blue-700">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Editar veículo</h2>
        <form method="post" class="grid grid-cols-2 gap-3">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <div class="col-span-2"><label class="text-sm text-slate-600">Placa</label><input name="placa" id="editPlaca" required class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">Marca</label><input name="marca" id="editMarca" required class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">Modelo</label><input name="modelo" id="editModelo" required class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">Ano</label><input name="ano" id="editAno" type="number" class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">Cor</label><input name="cor" id="editCor" class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">Diária (R$)</label><input name="diaria" id="editDiaria" type="number" step="0.01" class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div>
                <label class="text-sm text-slate-600">Status</label>
                <select name="status" id="editStatus" class="w-full border rounded-md px-3 py-2 text-sm mt-1 bg-white">
                    <option value="disponivel">Disponível</option>
                    <option value="alugado">Alugado</option>
                    <option value="manutencao">Manutenção</option>
                </select>
            </div>
            <div class="col-span-2 flex justify-end gap-2 mt-2">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-sm hover:bg-slate-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm hover:bg-blue-700">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(v) {
    document.getElementById('editId').value = v.id;
    document.getElementById('editPlaca').value = v.placa;
    document.getElementById('editMarca').value = v.marca;
    document.getElementById('editModelo').value = v.modelo;
    document.getElementById('editAno').value = v.ano;
    document.getElementById('editCor').value = v.cor;
    document.getElementById('editDiaria').value = v.diaria;
    document.getElementById('editStatus').value = v.status;
    document.getElementById('modalEdit').classList.remove('hidden');
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
