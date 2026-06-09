<?php
require __DIR__ . '/includes/db.php';
$db = getDB();
$title = 'Dashboard';
$page = '/';

$disp = count(array_filter($db['vehicles'], fn($v) => $v['status'] === 'disponivel'));
$alug = count(array_filter($db['vehicles'], fn($v) => $v['status'] === 'alugado'));
$manut = count(array_filter($db['vehicles'], fn($v) => $v['status'] === 'manutencao'));
$ativas = count(array_filter($db['rentals'], fn($r) => $r['status'] === 'ativa'));
$faturamento = array_sum(array_map(fn($r) => $r['total'] ?? 0, array_filter($db['rentals'], fn($r) => $r['status'] === 'encerrada')));

$brandData = [];
foreach ($db['vehicles'] as $v) {
    $brandData[$v['marca']] = ($brandData[$v['marca']] ?? 0) + 1;
}
$brandJson = json_encode($brandData);

require __DIR__ . '/includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
    <p class="text-slate-500 mt-1">Visão geral da operação em tempo real.</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white border rounded-xl p-5">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-500">Frota total</span>
            <div class="w-9 h-9 rounded-lg grid place-items-center bg-blue-50 text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H20.625a1.125 1.125 0 0 1-1.125-1.125V14.25"/></svg>
            </div>
        </div>
        <div class="mt-3 text-3xl font-bold tracking-tight"><?= count($db['vehicles']) ?></div>
        <div class="text-xs text-slate-500 mt-1"><?= $disp ?> disponíveis</div>
    </div>
    <div class="bg-white border rounded-xl p-5">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-500">Alugados</span>
            <div class="w-9 h-9 rounded-lg grid place-items-center bg-blue-50 text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg></div>
        </div>
        <div class="mt-3 text-3xl font-bold tracking-tight"><?= $alug ?></div>
        <div class="text-xs text-slate-500 mt-1">Em rodagem</div>
    </div>
    <div class="bg-white border rounded-xl p-5">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-500">Manutenção</span>
            <div class="w-9 h-9 rounded-lg grid place-items-center bg-amber-50 text-amber-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.33c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.42.33c-.512.403-1.18.653-1.878.653H7.313c-.56 0-1.085.223-1.478.62l-1.332 1.334a6.237 6.237 0 0 0 8.816 0l.005-.004Z"/></svg></div>
        </div>
        <div class="mt-3 text-3xl font-bold tracking-tight"><?= $manut ?></div>
        <div class="text-xs text-slate-500 mt-1">Indisponíveis</div>
    </div>
    <div class="bg-white border rounded-xl p-5">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-500">Faturamento</span>
            <div class="w-9 h-9 rounded-lg grid place-items-center bg-emerald-50 text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.307a11.95 11.95 0 0 1 5.814-5.519l2.74-1.22m0 0-5.94-2.28m5.94 2.28-2.28 5.941"/></svg></div>
        </div>
        <div class="mt-3 text-3xl font-bold tracking-tight"><?= fmtBRL($faturamento) ?></div>
        <div class="text-xs text-slate-500 mt-1"><?= $ativas ?> contratos ativos</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-4 mt-6">
    <div class="bg-white border rounded-xl p-5">
        <h3 class="font-semibold mb-4">Status da frota</h3>
        <div class="h-64" id="pieChart"></div>
    </div>
    <div class="bg-white border rounded-xl p-5">
        <h3 class="font-semibold mb-4">Veículos por marca</h3>
        <div class="h-64" id="barChart"></div>
    </div>
</div>

<div class="bg-white border rounded-xl p-5 mt-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            Contratos recentes
        </h3>
        <span class="text-xs text-slate-500"><?= count($db['rentals']) ?> totais</span>
    </div>
    <?php if (count($db['rentals']) === 0): ?>
        <p class="text-sm text-slate-500 py-6 text-center">Nenhum contrato registrado ainda. Abra um na aba <b>Locações</b>.</p>
    <?php else: ?>
        <div class="divide-y">
            <?php foreach (array_slice($db['rentals'], 0, 5) as $r):
                $v = array_values(array_filter($db['vehicles'], fn($x) => $x['id'] === $r['vehicleId']))[0] ?? null; ?>
                <div class="py-3 flex items-center justify-between text-sm">
                    <div>
                        <div class="font-medium"><?= h($r['cliente']) ?></div>
                        <div class="text-xs text-slate-500"><?= $v ? h($v['marca'] . ' ' . $v['modelo'] . ' • ' . $v['placa']) : 'Veículo removido' ?></div>
                    </div>
                    <div class="text-right">
                        <div class="font-medium"><?= $r['total'] ? fmtBRL($r['total']) : fmtBRL($r['diaria']) . '/dia' ?></div>
                        <div class="text-xs text-slate-500 capitalize"><?= h($r['status']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
        labels: ['Disponíveis','Alugados','Manutenção'],
        datasets: [{
            data: [<?= $disp ?>, <?= $alug ?>, <?= $manut ?>],
            backgroundColor: ['#10b981','#3b82f6','#f59e0b']
        }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
});

new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($brandData)) ?>,
        datasets: [{
            label: 'Qtd',
            data: <?= json_encode(array_values($brandData)) ?>,
            backgroundColor: '#2563eb',
            borderRadius: 6
        }]
    },
    options: { responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true,ticks:{stepSize:1}}} }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
