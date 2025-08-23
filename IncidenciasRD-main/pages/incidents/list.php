<?php $prefix = '../..'; ?>
<?php include $prefix . '/partials/head.php'; ?>
<?php include $prefix . '/partials/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Incidencias</h1>
      <p class="text-slate-600 mt-2">Explora las incidencias recientes en el mapa y filtra por tipo, provincia y fecha.</p>
    </div>
    <a href="new.php" class="rounded-2xl bg-indigo-600 text-white px-4 py-2 text-sm font-semibold">+ Reportar incidencia</a>
  </div>

  <section class="mt-6 grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 rounded-3xl ring-1 ring-slate-200 overflow-hidden bg-white">
      <div id="map" class="h-[480px] w-full"></div>
    </div>

    <aside class="space-y-4">
      <div class="rounded-2xl ring-1 ring-slate-200 bg-white p-4">
        <h2 class="font-semibold">Filtros</h2>
        <div class="mt-4 grid grid-cols-1 gap-3">
          <select id="filterType" class="rounded-xl ring-1 ring-slate-300 px-3 py-2">
            <option value="">Todos los tipos</option>
            <option>Accidente</option>
            <option>Pelea</option>
            <option>Robo</option>
            <option>Desastre</option>
          </select>
          <select id="filterProv" class="rounded-xl ring-1 ring-slate-300 px-3 py-2">
            <option value="">Todas las provincias</option>
            <option>Santo Domingo</option>
            <option>Santiago</option>
            <option>La Vega</option>
            <option>Puerto Plata</option>
          </select>
          <input id="filterDate" type="date" class="rounded-xl ring-1 ring-slate-300 px-3 py-2" />
          <button id="btnClear" class="rounded-xl ring-1 ring-slate-300 px-3 py-2 hover:bg-slate-50 text-sm">Limpiar filtros</button>
        </div>
      </div>

      <div class="rounded-2xl ring-1 ring-slate-200 bg-white divide-y" id="listContainer">
      </div>
    </aside>
  </section>
</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (!window.L) return;
    const map = L.map('map').setView([18.7357, -70.1627], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);

    const data = [
      { id:1, titulo:'Colisión múltiple', tipo:'Accidente', prov:'Santo Domingo', fecha:'2025-08-12', lat:18.48, lng:-69.9, muertos:0, heridos:2 },
      { id:2, titulo:'Robo en tienda', tipo:'Robo', prov:'Santiago', fecha:'2025-08-11', lat:19.45, lng:-70.69, muertos:0, heridos:0 },
      { id:3, titulo:'Deslizamiento menor', tipo:'Desastre', prov:'La Vega', fecha:'2025-08-10', lat:19.22, lng:-70.52, muertos:0, heridos:1 },
      { id:4, titulo:'Pelea callejera', tipo:'Pelea', prov:'Puerto Plata', fecha:'2025-08-09', lat:19.79, lng:-70.69, muertos:0, heridos:1 },
    ];

    const markers = [];

    function render(list = data) {
      markers.forEach(m => map.removeLayer(m));
      markers.length = 0;

      const cont = document.getElementById('listContainer');
      cont.innerHTML = '';

      list.forEach(item => {
        const m = L.marker([item.lat, item.lng]).addTo(map).bindPopup(`<b>${item.titulo}</b><br>${item.tipo} • ${item.prov}<br>${item.fecha}`);
        markers.push(m);

        const row = document.createElement('a');
        row.href = `view.php?id=${item.id}`;
        row.className = 'block p-4 hover:bg-slate-50';
        row.innerHTML = `
          <div class="flex items-start justify-between gap-3">
            <div>
              <h3 class="font-semibold">${item.titulo}</h3>
              <p class="text-sm text-slate-600">${item.tipo} • ${item.prov} • ${item.fecha}</p>
            </div>
            <span class="px-2 py-1 rounded-full text-xs ring-1 ring-slate-200">ID #${item.id}</span>
          </div>`;
        cont.appendChild(row);
      });

      if (list.length) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.2));
      }
    }

    function applyFilters() {
      const t = document.getElementById('filterType').value;
      const p = document.getElementById('filterProv').value;
      const d = document.getElementById('filterDate').value;
      const filtered = data.filter(x => (!t || x.tipo===t) && (!p || x.prov===p) && (!d || x.fecha===d));
      render(filtered);
    }

    document.getElementById('filterType').addEventListener('change', applyFilters);
    document.getElementById('filterProv').addEventListener('change', applyFilters);
    document.getElementById('filterDate').addEventListener('change', applyFilters);
    document.getElementById('btnClear').addEventListener('click', () => {
      document.getElementById('filterType').value = '';
      document.getElementById('filterProv').value = '';
      document.getElementById('filterDate').value = '';
      render(data);
    });

    render(data);
  });
</script>

<?php include $prefix . '/partials/footer.php'; ?>