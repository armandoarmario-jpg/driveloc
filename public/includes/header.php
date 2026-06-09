<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'DriveLoc') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#2563eb', foreground: '#fff' },
                        sidebar: { DEFAULT: '#0f172a', foreground: '#f8fafc', border: '#1e293b' },
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body class="bg-slate-50 text-slate-900">
<div class="flex min-h-screen">
    <aside class="hidden md:flex w-64 flex-col bg-sidebar text-sidebar-foreground border-r border-sidebar-border">
        <div class="px-6 py-6 flex items-center gap-3 border-b border-sidebar-border">
            <div class="w-9 h-9 rounded-lg bg-primary grid place-items-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m-3-3h-7.5M15.75 5.25v7.5m0-7.5h-7.5m7.5 0L9 15.75M6.75 15.75l-3 3m3-3L6.75 9.75M3 18.75v-7.5"/></svg>
            </div>
            <div>
                <div class="font-bold text-lg leading-none">DriveLoc</div>
                <div class="text-xs opacity-60 mt-1">Locação de veículos</div>
            </div>
        </div>
        <nav class="p-3 flex flex-col gap-1 flex-1">
            <?php
            $nav = [
                ['href' => '/', 'label' => 'Dashboard'],
                ['href' => '/veiculos.php', 'label' => 'Veículos'],
                ['href' => '/locacoes.php', 'label' => 'Locações'],
                ['href' => '/funcionarios.php', 'label' => 'Funcionários'],
            ];
            foreach ($nav as $n) {
                $active = ($page ?? '') === $n['href'];
                $cls = $active ? 'bg-primary text-primary-foreground' : 'hover:bg-white/5';
                echo '<a href="' . h($n['href']) . '" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm transition-colors ' . $cls . '">' . h($n['label']) . '</a>';
            }
            ?>
        </nav>
        <div class="p-4 border-t border-sidebar-border text-xs opacity-60">v1.0 • Demo Acadêmico</div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="md:hidden flex items-center gap-3 px-4 py-3 border-b bg-white">
            <div class="w-8 h-8 rounded-md bg-primary grid place-items-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m-3-3h-7.5M15.75 5.25v7.5m0-7.5h-7.5m7.5 0L9 15.75M6.75 15.75l-3 3m3-3L6.75 9.75M3 18.75v-7.5"/></svg>
            </div>
            <span class="font-bold">DriveLoc</span>
        </header>
        <nav class="md:hidden flex gap-1 px-2 py-2 border-b bg-white overflow-x-auto">
            <?php foreach ($nav as $n) {
                $active = ($page ?? '') === $n['href'];
                $cls = $active ? 'bg-primary text-white' : 'bg-slate-100';
                echo '<a href="' . h($n['href']) . '" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm whitespace-nowrap ' . $cls . '">' . h($n['label']) . '</a>';
            } ?>
        </nav>
        <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto">
