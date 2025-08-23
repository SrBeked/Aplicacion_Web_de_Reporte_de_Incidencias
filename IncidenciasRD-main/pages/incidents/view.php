<?php $prefix = '../..'; ?>
<?php include $prefix . '/partials/head.php'; ?>
<?php include $prefix . '/partials/header.php'; ?>

<main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <a href="list.php" class="text-sm text-indigo-600 font-semibold">← Volver a la lista</a>

  <header class="mt-3 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
    <div>
      <h1 class="text-2xl font-bold">Colisión múltiple en la 27</h1>
      <p class="text-slate-600">Accidente • Santo Domingo • 2025-08-12</p>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-2 py-1 rounded-full text-xs bg-green-50 text-green-700 ring-1 ring-green-200">Publicado</span>
      <a href="new.php" class="rounded-xl ring-1 ring-slate-300 px-3 py-1.5 text-sm">Reportar similar</a>
    </div>
  </header>

  <section class="mt-6 grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 rounded-3xl ring-1 ring-slate-200 overflow-hidden bg-white">
      <div id="viewMap" class="h-[420px] w-full"></div>
    </div>
    <aside class="space-y-4">
      <div class="rounded-2xl ring-1 ring-slate-200 bg-white p-4">
        <h2 class="font-semibold mb-2">Detalles</h2>
        <dl class="text-sm grid grid-cols-3 gap-2">
          <dt class="text-slate-500">Muertos</dt><dd class="col-span-2">0</dd>
          <dt class="text-slate-500">Heridos</dt><dd class="col-span-2">2</dd>
          <dt class="text-slate-500">Pérdida</dt><dd class="col-span-2">RD$ 150,000</dd>
          <dt class="text-slate-500">Coordenadas</dt><dd class="col-span-2">18.48, -69.9</dd>
        </dl>
      </div>

      <div class="rounded-2xl ring-1 ring-slate-200 bg-white p-4">
        <h2 class="font-semibold mb-2">Correcciones</h2>
        <form class="space-y-3">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs text-slate-500">Muertos</label>
              <input type="number" min="0" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
            </div>
            <div>
              <label class="block text-xs text-slate-500">Heridos</label>
              <input type="number" min="0" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
            </div>
          </div>
          <div>
            <label class="block text-xs text-slate-500">Provincia</label>
            <input type="text" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
          </div>
          <div>
            <label class="block text-xs text-slate-500">Pérdida estimada</label>
            <input type="number" min="0" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
          </div>
          <button type="button" onclick="alert('Prototipo: corrección enviada')" class="w-full rounded-2xl bg-indigo-600 text-white px-4 py-2 text-sm font-semibold">Enviar corrección</button>
        </form>
      </div>
    </aside>
  </section>
</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (!window.L) return;
    const map = L.map('viewMap').setView([18.48, -69.9], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
    L.marker([18.48, -69.9]).addTo(map).bindPopup('Colisión múltiple en la 27');
  });
</script>

<?php include $prefix . '/partials/footer.php'; ?>