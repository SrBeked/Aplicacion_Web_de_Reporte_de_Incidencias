<?php
$prefix = '.';
require_once __DIR__ . '/db.php';

$stats24 = ['total'=>0];
$stats24_by_type = [];
$statsMonth = 0;
$latestIncidents = [];

if (isset($conn) && $conn instanceof mysqli) {
  $q1 = $conn->query("SELECT COUNT(*) AS total FROM incidencias WHERE fecha >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
  if ($q1 && $r = $q1->fetch_assoc()) $stats24['total'] = (int)$r['total'];

  $q2 = $conn->query("SELECT tipo, COUNT(*) AS c FROM incidencias WHERE fecha >= DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY tipo ORDER BY c DESC");
  if ($q2) {
    while ($r = $q2->fetch_assoc()) {
      $stats24_by_type[] = ['tipo'=>$r['tipo'], 'c'=>(int)$r['c']];
    }
  }

  $q3 = $conn->query("SELECT COUNT(*) AS c FROM incidencias WHERE YEAR(fecha)=YEAR(CURDATE()) AND MONTH(fecha)=MONTH(CURDATE())");
  if ($q3 && $r = $q3->fetch_assoc()) $statsMonth = (int)$r['c'];

  $q4 = $conn->query("SELECT id, titulo, tipo, latitud, longitud, provincia, municipio, heridos, muertos, fecha FROM incidencias WHERE latitud IS NOT NULL AND longitud IS NOT NULL ORDER BY fecha DESC LIMIT 6");
  if ($q4) {
    while ($r = $q4->fetch_assoc()) {
      $latestIncidents[] = [
        'id'=>(int)$r['id'],
        'titulo'=>$r['titulo'],
        'tipo'=>$r['tipo'],
        'latitud'=>$r['latitud'],
        'longitud'=>$r['longitud'],
        'provincia'=>$r['provincia'],
        'municipio'=>$r['municipio'],
        'heridos'=>(int)$r['heridos'],
        'muertos'=>(int)$r['muertos'],
        'fecha'=>$r['fecha'],
      ];
    }
  }
}
?>
<?php include $prefix . '/partials/head.php'; ?>
<?php include $prefix . '/partials/header.php'; ?>

<main class="min-h-[70vh] bg-gradient-to-b from-slate-50 to-white">
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid md:grid-cols-2 gap-10 items-center">
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">
          Mapa de Incidencias
        </h1>
        <p class="mt-4 text-lg text-slate-600">
          Plataforma para registrar, visualizar y gestionar incidencias reportadas en República Dominicana con datos en tiempo cercano al evento.
        </p>
        <div class="mt-6 flex flex-wrap items-center gap-3">
          <span class="inline-flex items-center rounded-2xl px-3 py-1 text-sm font-semibold bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100">Últimas 24h: <span class="ml-1 font-bold"><?php echo (int)$stats24['total']; ?></span></span>
          <span class="inline-flex items-center rounded-2xl px-3 py-1 text-sm font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">Este mes: <span class="ml-1 font-bold"><?php echo (int)$statsMonth; ?></span></span>
          <?php if (!empty($stats24_by_type)): ?>
            <?php foreach ($stats24_by_type as $t): ?>
              <span class="inline-flex items-center rounded-2xl px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700 ring-1 ring-slate-200"><?php echo htmlspecialchars($t['tipo']); ?>: <span class="ml-1 font-bold"><?php echo (int)$t['c']; ?></span></span>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <div class="mt-8 flex flex-wrap gap-3">
          <a href="pages/incidents/list.php" class="inline-flex items-center rounded-2xl px-5 py-3 text-sm font-semibold shadow hover:shadow-md bg-indigo-600 text-white">Explorar incidencias</a>
          <a href="pages/incidents/new.php" class="inline-flex items-center rounded-2xl px-5 py-3 text-sm font-semibold shadow hover:shadow-md bg-emerald-600 text-white">Reportar incidencia</a>
          <a href="pages/login.php" class="inline-flex items-center rounded-2xl px-5 py-3 text-sm font-semibold shadow ring-1 ring-slate-200 hover:bg-slate-50">Iniciar sesión</a>
          <a href="pages/register.php" class="inline-flex items-center rounded-2xl px-5 py-3 text-sm font-semibold shadow ring-1 ring-slate-200 hover:bg-slate-50">Crear cuenta</a>
        </div>
      </div>
      <div>
        <div class="rounded-3xl ring-1 ring-slate-200 bg-white p-2">
          <div id="heroMap" class="h-80 w-full rounded-2xl"></div>
        </div>
        <div class="mt-4 text-xs text-slate-500">Fuente de mapas © OpenStreetMap</div>
      </div>
    </div>

    <div class="mt-16">
      <h2 class="text-xl font-bold text-slate-900">Incidencias recientes</h2>
      <div class="mt-4 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if (!empty($latestIncidents)): ?>
          <?php foreach (array_slice($latestIncidents,0,6) as $i): ?>
            <a href="pages/incidents/view.php?id=<?php echo (int)$i['id']; ?>" class="block rounded-2xl ring-1 ring-slate-200 bg-white p-4 hover:shadow">
              <div class="flex items-start justify-between">
                <span class="text-xs uppercase tracking-wide px-2 py-1 rounded-full bg-slate-100 text-slate-700"><?php echo htmlspecialchars($i['tipo']); ?></span>
                <span class="text-xs text-slate-500"><?php echo date('d/m/Y H:i', strtotime($i['fecha'])); ?></span>
              </div>
              <h3 class="mt-2 text-sm font-semibold text-slate-900"><?php echo htmlspecialchars($i['titulo']); ?></h3>
              <p class="mt-1 text-xs text-slate-600"><?php echo htmlspecialchars(($i['municipio'] ?: '') . ($i['municipio'] && $i['provincia'] ? ', ' : '') . ($i['provincia'] ?: '')); ?></p>
              <div class="mt-2 flex items-center gap-3 text-xs text-slate-600">
                <span>Heridos: <b><?php echo (int)$i['heridos']; ?></b></span>
                <span>Muertos: <b><?php echo (int)$i['muertos']; ?></b></span>
              </div>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-span-full rounded-2xl ring-1 ring-slate-200 bg-white p-6 text-slate-600">No hay incidencias registradas aún.</div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<?php include $prefix . '/partials/footer.php'; ?>
<script>
  window.incidents = <?php echo json_encode($latestIncidents ?: [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  document.addEventListener('DOMContentLoaded', () => {
    if (window.L) {
      const center = [18.7357, -70.1627];
      const map = L.map('heroMap', { zoomControl: false }).setView(center, 7);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
      if (Array.isArray(window.incidents) && window.incidents.length > 0) {
        const markers = [];
        window.incidents.forEach(i => {
          if (!i.latitud || !i.longitud) return;
          const m = L.circleMarker([parseFloat(i.latitud), parseFloat(i.longitud)]).addTo(map);
          const title = i.titulo ? i.titulo : '';
          const where = [i.municipio || '', i.provincia || ''].filter(Boolean).join(', ');
          const stats = `Heridos: ${parseInt(i.heridos||0)} · Muertos: ${parseInt(i.muertos||0)}`;
          const when = i.fecha ? new Date(i.fecha).toLocaleString() : '';
          m.bindPopup(`<b>${title}</b><br>${i.tipo || ''}<br>${where}<br>${stats}<br>${when}`);
          markers.push(m);
        });
        if (markers.length > 0) {
          const group = L.featureGroup(markers);
          map.fitBounds(group.getBounds().pad(0.2));
        }
      }
    }
  });
</script>
