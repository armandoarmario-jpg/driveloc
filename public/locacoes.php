<?php
require __DIR__ . '/includes/db.php';
$db = getDB();
$title = 'Locações';
$page = '/locacoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'open') {
        $vehicleId = $_POST['vehicleId'] ?? '';
        $v = array_values(array_filter($db['vehicles'], fn($x) => $x['id'] === $vehicleId))[0] ?? null;
        if ($v) {
            $db['rentals'][] = [
                'id' => uid(),
                'vehicleId' => $vehicleId,
                'cliente' => $_POST['cliente'] ?? '',
                'cpf' => $_POST['cpf'] ?? '',
                'inicio' => date('c', strtotime($_POST['inicio'] ?? 'today')),
                'previsao' => date('c', strtotime($_POST['previsao'] ?? '+3 days')),
                'fim' => null,
                'diaria' => $v['diaria'],
                'total' => null,
                'status' => 'ativa',
            ];
            foreach ($db['vehicles'] as &$ve) {
                if ($ve['id'] === $vehicleId) $ve['status'] = 'alugado';
            }
            saveDB($db);
        }
        header('Location: /locacoes.php?tab=ativas&msg=Contrato+aberto');
        exit;
    } elseif ($action === 'close') {
        foreach ($db['rentals'] as &$r) {
            if ($r['id'] === ($_POST['id'] ?? '')) {
                $r['status'] = 'encerrada';
                $r['fim'] = date('c');
                $dias = max(1, ceil((time() - strtotime($r['inicio'])) / 86400));
                $r['total'] = $dias * $r['diaria'];
                foreach ($db['vehicles'] as &$ve) {
                    if ($ve['id'] === $r['vehicleId']) $ve['status'] = 'disponivel';
                }
            }
        }
        saveDB($db);
        header('Location: /locacoes.php?tab=encerradas&msg=Contrato+encerrado');
        exit;
    }
}

$tab = $_GET['tab'] ?? 'ativas';
$disponiveis = array_filter($db['vehicles'], fn($v) => $v['status'] === 'disponivel');
$ativas = array_filter($db['rentals'], fn($r) => $r['status'] === 'ativa');
$encerradas = array_filter($db['rentals'], fn($r) => $r['status'] === 'encerrada');

require __DIR__ . '/includes/header.php';
?>

<div class="flex items-end justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-3xl font-bold tracking-tight">Locações</h1>
        <p class="text-slate-500 mt-1">Abra e encerre contratos de aluguel.</p>
    </div>
    <a href="#" onclick="document.getElementById('modalOpen').classList.remove('hidden');return false;" class="inline-flex items-center gap-2 bg-primary text-primary-foreground px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">+ Nova locação</a>
</div>

<?php if (empty($disponiveis) && count($db['vehicles']) > 0): ?>
    <div class="bg-amber-50 border border-amber-200 text-amber-800 p-3 rounded-lg text-sm mb-4">Não há veículos disponíveis para locação no momento.</div>
<?php endif; ?>

<?php if (!empty($_GET['msg'])): ?>
    <div class="bg-emerald-100 text-emerald-700 border border-emerald-200 p-3 rounded-lg text-sm mb-4"><?= h($_GET['msg']) ?></div>
<?php endif; ?>

<div class="bg-white border rounded-xl overflow-hidden">
    <div class="flex border-b">
        <a href="?tab=ativas" class="px-4 py-2.5 text-sm font-medium <?= $tab==='ativas'?'border-b-2 border-primary text-primary':'text-slate-500 hover:text-slate-700' ?>">Ativas (<?= count($ativas) ?>)</a>
        <a href="?tab=encerradas" class="px-4 py-2.5 text-sm font-medium <?= $tab==='encerradas'?'border-b-2 border-primary text-primary':'text-slate-500 hover:text-slate-700' ?>">Encerradas (<?= count($encerradas) ?>)</a>
    </div>
    <div class="divide-y">
        <?php
        $list = $tab === 'ativas' ? $ativas : $encerradas;
        if (empty($list)):
            echo '<div class="p-12 text-center text-slate-500 text-sm">Nenhum contrato aqui.</div>';
        else:
            foreach ($list as $r):
                $v = array_values(array_filter($db['vehicles'], fn($x) => $x['id'] === $r['vehicleId']))[0] ?? null;
        ?>
            <div class="p-5 flex flex-wrap items-center justify-between gap-4">
                <div class="min-w-[200px]">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold"><?= h($r['cliente']) ?></span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= badgeClass($r['status']) ?>"><?= badgeLabel($r['status']) ?></span>
                    </div>
                    <div class="text-sm text-slate-500">CPF <?= h($r['cpf']) ?></div>
                </div>
                <div class="text-sm">
                    <div class="font-medium"><?= $v ? h($v['marca'] . ' ' . $v['modelo']) : '—' ?></div>
                    <div class="text-xs text-slate-500 font-mono"><?= $v ? h($v['placa']) : '' ?></div>
                </div>
                <div class="text-sm">
                    <div class="text-slate-500 text-xs">Período</div>
                    <div><?= fmtDate($r['inicio']) ?> → <?= $r['fim'] ? fmtDate($r['fim']) : fmtDate($r['previsao']) ?></div>
                </div>
                <div class="text-sm text-right">
                    <div class="text-slate-500 text-xs"><?= $r['total'] ? 'Total' : 'Diária' ?></div>
                    <div class="font-bold text-blue-600"><?= $r['total'] ? fmtBRL($r['total']) : fmtBRL($r['diaria']) ?></div>
                </div>
                <?php if ($tab === 'ativas'): ?>
                    <form method="post" class="inline">
                        <input type="hidden" name="action" value="close">
                        <input type="hidden" name="id" value="<?= h($r['id']) ?>">
                        <button type="submit" class="px-3 py-1.5 border rounded-md text-sm hover:bg-slate-50">Encerrar</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<!-- Modal Open -->
<div id="modalOpen" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Abrir contrato</h2>
        <form method="post" class="space-y-3">
            <input type="hidden" name="action" value="open">
            <div>
                <label class="text-sm text-slate-600">Veículo</label>
                <select name="vehicleId" required class="w-full border rounded-md px-3 py-2 text-sm mt-1 bg-white">
                    <option value="">Selecione um veículo</option>
                    <?php foreach ($disponiveis as $v): ?>
                        <option value="<?= h($v['id']) ?>"><?= h($v['marca'] . ' ' . $v['modelo']) ?> • <?= h($v['placa']) ?> • <?= fmtBRL($v['diaria']) ?>/dia</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label class="text-sm text-slate-600">Cliente</label><input name="cliente" required class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">CPF</label><input name="cpf" required class="w-full border rounded-md px-3 py-2 text-sm mt-1" placeholder="000.000.000-00"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-sm text-slate-600">Início</label><input name="inicio" type="date" value="<?= date('Y-m-d') ?>" class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
                <div><label class="text-sm text-slate-600">Previsão devolução</label><input name="previsao" type="date" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalOpen').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-sm hover:bg-slate-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm hover:bg-blue-700">Abrir contrato</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
