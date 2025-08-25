<?php
session_start();
$prefix = '../..';
require_once __DIR__ . '/../../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$inc = null;

if ($id > 0) {
  $stmt = $conn->prepare("SELECT id, titulo, descripcion, tipo, provincia, municipio, barrio, latitud, longitud, muertos, heridos, perdida_estimada, link_redes, foto, fecha FROM incidencias WHERE id=? LIMIT 1");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $res = $stmt->get_result();
  $inc = $res->fetch_assoc();
  $stmt->close();
}

include $prefix . '/partials/head.php';
include $prefix . '/partials/header.php';

if (!$inc): ?>
<main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <a href="list.php" class="text-sm text-indigo-600 font-semibold">← Volver a la lista</a>
  <div class="mt-6 rounded-2xl ring-1 ring-slate-200 bg-white p-6">
    <h1 class="text-xl font-bold text-slate-900">Incidencia no encontrada</h1>
    <p class="text-slate-600 mt-2">El registro solicitado no existe o fue eliminado.</p>
  </div>
</main>
<?php include $prefix . '/partials/footer.php'; exit; endif; ?>

<main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <a href="list.php" class="text-sm text-indigo-600 font-semibold">← Volver a la lista</a>

  <header class="mt-3 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
    <div>
      <h1 class="text-2xl font-bold"><?= htmlspecialchars($inc['titulo']) ?></h1>
      <p class="text-slate-600"><?= htmlspecialchars($inc['tipo']) ?> • <?= htmlspecialchars($inc['municipio'] ? $inc['municipio'] . ', ' . $inc['provincia'] : $inc['provincia']) ?> • <?= date('Y-m-d H:i', strtotime($inc['fecha'])) ?></p>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-2 py-1 rounded-full text-xs bg-green-50 text-green-700 ring-1 ring-green-200">Publicado</span>
      <a href="new.php" class="rounded-xl ring-1 ring-slate-300 px-3 py-1.5 text-sm">Reportar similar</a>
    </div>
  </header>

  <section class="mt-6 grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 rounded-3xl ring-1 ring-slate-200 overflow-hidden bg-white">
      <div id="viewMap" class="h-[420px] w-full"></div>
      <?php if (!empty($inc['descripcion'])): ?>
        <div class="p-4 border-t border-slate-200">
          <h2 class="font-semibold mb-2">Descripción</h2>
          <p class="text-sm text-slate-700 whitespace-pre-line"><?= nl2br(htmlspecialchars($inc['descripcion'])) ?></p>
        </div>
      <?php endif; ?>
      <?php if (!empty($inc['link_redes'])): ?>
        <div class="p-4 border-t border-slate-200">
          <a class="text-sm text-indigo-600 font-semibold" href="<?= htmlspecialchars($inc['link_redes']) ?>" target="_blank" rel="noopener">Ver referencia externa</a>
        </div>
      <?php endif; ?>
      <?php if (!empty($inc['foto'])): ?>
        <div class="p-4 border-t border-slate-200">
          <img src="<?= htmlspecialchars($inc['foto']) ?>" alt="Evidencia" class="rounded-xl max-h-96 object-cover">
        </div>
      <?php endif; ?>
    </div>

    <aside class="space-y-4">
      <div class="rounded-2xl ring-1 ring-slate-200 bg-white p-4">
        <h2 class="font-semibold mb-2">Detalles</h2>
        <dl class="text-sm grid grid-cols-3 gap-2">
          <dt class="text-slate-500">Muertos</dt><dd class="col-span-2"><?= (int)$inc['muertos'] ?></dd>
          <dt class="text-slate-500">Heridos</dt><dd class="col-span-2"><?= (int)$inc['heridos'] ?></dd>
          <dt class="text-slate-500">Pérdida</dt><dd class="col-span-2"><?= $inc['perdida_estimada'] !== null ? 'RD$ ' . number_format((float)$inc['perdida_estimada'], 2, '.', ',') : 'N/D' ?></dd>
          <dt class="text-slate-500">Coordenadas</dt><dd class="col-span-2"><?= $inc['latitud'] && $inc['longitud'] ? htmlspecialchars($inc['latitud'] . ', ' . $inc['longitud']) : 'N/D' ?></dd>
          <dt class="text-slate-500">Ubicación</dt><dd class="col-span-2"><?= htmlspecialchars(trim(($inc['barrio'] ? $inc['barrio'] . ', ' : '') . ($inc['municipio'] ? $inc['municipio'] . ', ' : '') . $inc['provincia'], ', ')) ?></dd>
        </dl>
      </div>
    </aside>
  </section>
</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (!window.L) return;
    const lat = <?= $inc['latitud'] !== null ? json_encode((float)$inc['latitud']) : 'null' ?>;
    const lng = <?= $inc['longitud'] !== null ? json_encode((float)$inc['longitud']) : 'null' ?>;
    const center = lat !== null && lng !== null ? [lat, lng] : [18.7357, -70.1627];
    const z = lat !== null && lng !== null ? 13 : 7;
    const map = L.map('viewMap').setView(center, z);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
    if (lat !== null && lng !== null) {
      L.marker([lat, lng]).addTo(map).bindPopup(<?= json_encode($inc['titulo']) ?>);
    }
  });
</script>

<?php include $prefix . '/partials/footer.php'; ?>
