<?php $prefix = '.'; ?>
<?php include $prefix . '/partials/head.php'; ?>
<?php include $prefix . '/partials/header.php'; ?>

<main class="min-h-[70vh] bg-gradient-to-b from-slate-50 to-white">
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid md:grid-cols-2 gap-10 items-center">
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">
          Incidencias<span class="text-indigo-600">RD</span>
        </h1>
        <p class="mt-4 text-lg text-slate-600">
          Registra, visualiza y gestiona incidencias a nivel nacional.
          Este es un <strong>DEMO</strong>.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
          <a href="pages/incidents/list.php" class="inline-flex items-center rounded-2xl px-5 py-3 text-sm font-semibold shadow hover:shadow-md bg-indigo-600 text-white">Explorar incidencias</a>
          <a href="pages/login.php" class="inline-flex items-center rounded-2xl px-5 py-3 text-sm font-semibold shadow ring-1 ring-slate-200 hover:bg-slate-50">Iniciar sesión</a>
          <a href="pages/register.php" class="inline-flex items-center rounded-2xl px-5 py-3 text-sm font-semibold shadow ring-1 ring-slate-200 hover:bg-slate-50">Crear cuenta</a>
        </div>
        <div class="mt-10 grid grid-cols-2 md:grid-cols-3 gap-4 text-sm text-slate-600">
          <div class="p-4 rounded-2xl ring-1 ring-slate-200 bg-white">Mapa interactivo</div>
          <div class="p-4 rounded-2xl ring-1 ring-slate-200 bg-white">Filtros por tipo</div>
          <div class="p-4 rounded-2xl ring-1 ring-slate-200 bg-white">Panel de usuario</div>
        </div>
      </div>
      <div>
        <div class="rounded-3xl ring-1 ring-slate-200 bg-white p-2">
          <div id="heroMap" class="h-80 w-full rounded-2xl"></div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include $prefix . '/partials/footer.php'; ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (window.L) {
      const map = L.map('heroMap', { zoomControl: false }).setView([18.7357, -70.1627], 7);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
      L.circleMarker([18.48, -69.9]).addTo(map).bindPopup('Ejemplo: Accidente');
      L.circleMarker([19.45, -70.69]).addTo(map).bindPopup('Ejemplo: Robo');
    }
  });
</script>