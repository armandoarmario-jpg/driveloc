<?php
require __DIR__ . '/includes/db.php';
$db = getDB();
$title = 'Funcionários';
$page = '/funcionarios.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $db['users'][] = [
            'id' => uid(),
            'nome' => $_POST['nome'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role' => $_POST['role'] ?? 'operador',
            'status' => 'pendente',
            'criadoEm' => date('c'),
        ];
        saveDB($db);
        header('Location: /funcionarios.php?msg=Funcionário+cadastrado+-+aguardando+aprovação');
        exit;
    } elseif ($action === 'status') {
        foreach ($db['users'] as &$u) {
            if ($u['id'] === ($_POST['id'] ?? '')) {
                $u['status'] = $_POST['status'] ?? 'ativo';
            }
        }
        saveDB($db);
        header('Location: /funcionarios.php?msg=Status+atualizado');
        exit;
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="flex items-end justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-3xl font-bold tracking-tight">Funcionários</h1>
        <p class="text-slate-500 mt-1">Aprove novos cadastros e gerencie permissões.</p>
    </div>
    <a href="#" onclick="document.getElementById('modalAdd').classList.remove('hidden');return false;" class="inline-flex items-center gap-2 bg-primary text-primary-foreground px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">+ Novo cadastro</a>
</div>

<?php if (!empty($_GET['msg'])): ?>
    <div class="bg-emerald-100 text-emerald-700 border border-emerald-200 p-3 rounded-lg text-sm mb-4"><?= h($_GET['msg']) ?></div>
<?php endif; ?>

<div class="bg-white border rounded-xl divide-y">
    <?php foreach ($db['users'] as $u): ?>
        <div class="p-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 min-w-[240px]">
                <div class="w-10 h-10 rounded-full grid place-items-center <?= $u['role'] === 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-blue-50 text-blue-600' ?>">
                    <?php if ($u['role'] === 'admin'): ?>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                    <?php else: ?>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="font-medium"><?= h($u['nome']) ?></div>
                    <div class="text-xs text-slate-500"><?= h($u['email']) ?></div>
                </div>
            </div>
            <div class="text-sm capitalize"><?= h($u['role']) ?></div>
            <div class="text-xs text-slate-500">desde <?= fmtDate($u['criadoEm']) ?></div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= badgeClass($u['status']) ?>"><?= badgeLabel($u['status']) ?></span>
            <div class="flex gap-2">
                <form method="post" class="inline">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="id" value="<?= h($u['id']) ?>">
                    <?php if ($u['status'] === 'pendente'): ?>
                        <input type="hidden" name="status" value="ativo">
                        <button type="submit" class="px-3 py-1.5 bg-primary text-white rounded-md text-sm hover:bg-blue-700">Aprovar</button>
                    <?php elseif ($u['status'] === 'ativo'): ?>
                        <input type="hidden" name="status" value="bloqueado">
                        <button type="submit" class="px-3 py-1.5 border rounded-md text-sm hover:bg-slate-50">Bloquear</button>
                    <?php else: ?>
                        <input type="hidden" name="status" value="ativo">
                        <button type="submit" class="px-3 py-1.5 border rounded-md text-sm hover:bg-slate-50">Reativar</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Add -->
<div id="modalAdd" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Cadastrar funcionário</h2>
        <form method="post" class="space-y-3">
            <input type="hidden" name="action" value="add">
            <div><label class="text-sm text-slate-600">Nome</label><input name="nome" required class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div><label class="text-sm text-slate-600">E-mail</label><input name="email" type="email" required class="w-full border rounded-md px-3 py-2 text-sm mt-1"></div>
            <div>
                <label class="text-sm text-slate-600">Função</label>
                <select name="role" class="w-full border rounded-md px-3 py-2 text-sm mt-1 bg-white">
                    <option value="operador">Operador</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <p class="text-xs text-slate-500">Por segurança, o novo funcionário começará como <b>pendente</b> até aprovação do administrador.</p>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-sm hover:bg-slate-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm hover:bg-blue-700">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
