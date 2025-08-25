<?php
session_start();
$prefix = '../..';
require_once __DIR__ . '/../../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$inc = null;
$save_ok = false;
$errors = [];

if ($id > 0) {
  $stmt = $conn->prepare("SELECT id, titulo, descripcion, tipo, provincia, municipio, barrio, latitud, longitud, muertos, heridos, perdida_estimada, link_redes, foto, fecha FROM incidencias WHERE id=? LIMIT 1");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $res = $stmt->get_result();
  $inc = $res->fetch_assoc();
  $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $inc) {
  $c_muertos = isset($_POST['c_muertos']) && $_POST['c_muertos'] !== '' ? (int)$_POST['c_muertos'] : null;
  $c_heridos = isset($_POST['c_heridos']) && $_POST['c_heridos'] !== '' ? (int)$_POST['c_heridos'] : null;
  $c_provincia = isset($_POST['c_provincia']) && trim($_POST['c_provincia']) !== '' ? trim($_POST['c_provincia']) : null;
  $c_perdida = isset($_POST['c_perdida']) && $_POST['c_perdida'] !== '' ? (float)$_POST['c_perdida'] : null;
  if ($c_muertos === null && $c_heridos === null && $c_provincia === null && $c_perdida === null) $errors[] = 'Indica al menos un campo a corregir.';
  if (!$errors) {
    $usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
    $stmt = $conn->prepare("INSERT INTO correcciones (incidencia_id, muertos, heridos, provincia, perdida_estimada, usuario_id, creada_en) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param(
      "iiisdi",
      $id,
      $c_muertos,
      $c_heridos,
      $c_provincia,
      $c_perdida,
      $usuario_id
    );
    $stmt->execute();
    $stmt->close();
    $save_ok = true;
  }
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
      <?php if ($save_ok): ?>
        <div class="rounded-2xl ring-1 ring-emerald-200 bg-emerald-50 text-emerald-700 p-4 text-sm">Corrección enviada correctamente.</div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <div class="rounded-2xl ring-1 ring-red-200 bg-red-50 text-red-700 p-4 text-sm">
          <ul class="list-disc pl-5">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

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

      <div class="rounded-2xl ring-1 ring-slate-200 bg-white p-4">
        <h2 class="font-semibold mb-2">Correcciones</h2>
        <form class="space-y-3" method="post">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs text-slate-500">Muertos</label>
              <input name="c_muertos" type="number" min="0" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= isset($_POST['c_muertos']) ? htmlspecialchars($_POST['c_muertos']) : '' ?>" />
            </div>
            <div>
              <label class="block text-xs text-slate-500">Heridos</label>
              <input name="c_heridos" type="number" min="0" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= isset($_POST['c_heridos']) ? htmlspecialchars($_POST['c_heridos']) : '' ?>" />
            </div>
          </div>
          <div>
            <label class="block text-xs text-slate-500">Provincia</label>
            <input name="c_provincia" type="text" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= isset($_POST['c_provincia']) ? htmlspecialchars($_POST['c_provincia']) : '' ?>" />
          </div>
          <div>
            <label class="block text-xs text-slate-500">Pérdida estimada</label>
            <input name="c_perdida" type="number" min="0" step="0.01" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= isset($_POST['c_perdida']) ? htmlspecialchars($_POST['c_perdida']) : '' ?>" />
          </div>
          <button type="submit" class="w-full rounded-2xl bg-indigo-600 text-white px-4 py-2 text-sm font-semibold">Enviar corrección</button>
        </form>
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
