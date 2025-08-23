<?php $prefix = '..'; ?>
<?php include $prefix . '/partials/head.php'; ?>
<?php include $prefix . '/partials/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <h1 class="text-2xl font-bold">Panel de Usuario</h1>
  <p class="text-slate-600 mt-2">Bienvenido, <strong>Daniel</strong>. Aquí verás un resumen de tus reportes.</p>

  <section class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="p-5 rounded-2xl ring-1 ring-slate-200 bg-white">
      <p class="text-sm text-slate-500">Incidencias reportadas</p>
      <p class="text-3xl font-extrabold mt-1">12</p>
    </div>
    <div class="p-5 rounded-2xl ring-1 ring-slate-200 bg-white">
      <p class="text-sm text-slate-500">Pendientes de revisión</p>
      <p class="text-3xl font-extrabold mt-1">3</p>
    </div>
    <div class="p-5 rounded-2xl ring-1 ring-slate-200 bg-white">
      <p class="text-sm text-slate-500">Correcciones aceptadas</p>
      <p class="text-3xl font-extrabold mt-1">5</p>
    </div>
  </section>

  <div class="mt-8 flex items-center justify-between">
    <h2 class="text-lg font-semibold">Tus últimas incidencias</h2>
    <a href="incidents/new.php" class="rounded-2xl bg-indigo-600 text-white px-4 py-2 text-sm font-semibold">+ Nueva incidencia</a>
  </div>

  <div class="mt-4 overflow-hidden rounded-2xl ring-1 ring-slate-200">
    <table class="min-w-full divide-y divide-slate-200">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Fecha</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Título</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tipo</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Estado</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200 bg-white">
        <tr>
          <td class="px-4 py-3 text-sm">2025-08-12</td>
          <td class="px-4 py-3 text-sm">Colisión múltiple en la 27</td>
          <td class="px-4 py-3 text-sm"><span class="px-2 py-1 rounded-full text-xs bg-red-50 text-red-700 ring-1 ring-red-200">Accidente</span></td>
          <td class="px-4 py-3 text-sm">Publicado</td>
          <td class="px-4 py-3 text-sm text-right"><a class="text-indigo-600 font-semibold" href="incidents/view.php">Ver</a></td>
        </tr>
        <tr>
          <td class="px-4 py-3 text-sm">2025-08-09</td>
          <td class="px-4 py-3 text-sm">Robo en Plaza Central</td>
          <td class="px-4 py-3 text-sm"><span class="px-2 py-1 rounded-full text-xs bg-yellow-50 text-yellow-700 ring-1 ring-yellow-200">Robo</span></td>
          <td class="px-4 py-3 text-sm">En revisión</td>
          <td class="px-4 py-3 text-sm text-right"><a class="text-indigo-600 font-semibold" href="incidents/view.php">Ver</a></td>
        </tr>
      </tbody>
    </table>
  </div>
</main>
<?php include $prefix . '/partials/footer.php'; ?>
