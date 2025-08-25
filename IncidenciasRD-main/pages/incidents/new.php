<?php
session_start();
$prefix = '../..';
require_once __DIR__ . '/../../db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fecha = $_POST['fecha'] ?? '';
  $tipo = trim($_POST['tipo'] ?? '');
  $titulo = trim($_POST['titulo'] ?? '');
  $descripcion = trim($_POST['descripcion'] ?? '');
  $provincia = trim($_POST['provincia'] ?? '');
  $municipio = trim($_POST['municipio'] ?? '');
  $barrio = trim($_POST['barrio'] ?? '');
  $lat = $_POST['lat'] ?? '';
  $lng = $_POST['lng'] ?? '';
  $muertos = $_POST['muertos'] ?? '0';
  $heridos = $_POST['heridos'] ?? '0';
  $perdida = $_POST['perdida'] ?? '0';
  $link = trim($_POST['link'] ?? '');
  $fotoPath = null;

  if ($fecha === '') $errors[] = 'La fecha es requerida.';
  if ($tipo === '') $errors[] = 'El tipo es requerido.';
  if ($titulo === '') $errors[] = 'El título es requerido.';
  if ($provincia === '') $errors[] = 'La provincia es requerida.';
  if ($lat !== '' && !is_numeric($lat)) $errors[] = 'Latitud inválida.';
  if ($lng !== '' && !is_numeric($lng)) $errors[] = 'Longitud inválida.';
  if ($muertos !== '' && !ctype_digit((string)$muertos)) $errors[] = 'Muertos inválido.';
  if ($heridos !== '' && !ctype_digit((string)$heridos)) $errors[] = 'Heridos inválido.';
  if ($perdida !== '' && !is_numeric($perdida)) $errors[] = 'Pérdida inválida.';
  if ($link !== '' && !filter_var($link, FILTER_VALIDATE_URL)) $errors[] = 'Link inválido.';

  if (!empty($_FILES['foto']['name'])) {
    $dir = $prefix . '/uploads/incidencias';
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $tmp = $_FILES['foto']['tmp_name'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $ext = $mime === 'image/jpeg' ? 'jpg' : ($mime === 'image/png' ? 'png' : ($mime === 'image/webp' ? 'webp' : ''));
    if ($ext === '') {
      $errors[] = 'Formato de imagen no permitido.';
    } else {
      $name = 'inc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
      $destFs = rtrim($dir, '/') . '/' . $name;
      if (!move_uploaded_file($tmp, $destFs)) {
        $errors[] = 'No se pudo guardar la imagen.';
      } else {
        $fotoPath = '/uploads/incidencias/' . $name;
      }
    }
  }

  if (!$errors) {
    $sql = "INSERT INTO incidencias (titulo, descripcion, tipo, provincia, municipio, barrio, latitud, longitud, muertos, heridos, perdida_estimada, link_redes, foto, fecha, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $municipioParam = $municipio !== '' ? $municipio : null;
    $barrioParam = $barrio !== '' ? $barrio : null;
    $latParam = $lat !== '' ? (float)$lat : null;
    $lngParam = $lng !== '' ? (float)$lng : null;
    $muertosParam = $muertos !== '' ? (int)$muertos : 0;
    $heridosParam = $heridos !== '' ? (int)$heridos : 0;
    $perdidaParam = $perdida !== '' ? (float)$perdida : 0;
    $linkParam = $link !== '' ? $link : null;
    $fotoParam = $fotoPath !== null ? $fotoPath : null;
    $fechaParam = $fecha . ' ' . date('H:i:s');
    $usuarioParam = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;

    $stmt->bind_param(
      "ssssssddiidsssi",
      $titulo,
      $descripcion,
      $tipo,
      $provincia,
      $municipioParam,
      $barrioParam,
      $latParam,
      $lngParam,
      $muertosParam,
      $heridosParam,
      $perdidaParam,
      $linkParam,
      $fotoParam,
      $fechaParam,
      $usuarioParam
    );

    $stmt->execute();
    $id = (int)$conn->insert_id;
    header('Location: view.php?id=' . $id);
    exit;
  }
}
?>
<?php include $prefix . '/partials/head.php'; ?>
<?php include $prefix . '/partials/header.php'; ?>

<main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <h1 class="text-2xl font-bold">Reportar nueva incidencia</h1>
  <p class="text-slate-600 mt-2">Completa la información para registrar una incidencia.</p>

  <?php if (!empty($errors)): ?>
    <div class="mt-6 rounded-2xl bg-red-50 text-red-700 ring-1 ring-red-200 p-4">
      <ul class="list-disc pl-5 space-y-1">
        <?php foreach ($errors as $er): ?>
          <li><?= htmlspecialchars($er) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form id="incidentForm" class="mt-8 space-y-6 bg-white p-6 rounded-2xl ring-1 ring-slate-200" method="post" enctype="multipart/form-data" novalidate>
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Fecha</label>
        <input name="fecha" type="date" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>" />
      </div>
      <div>
        <label class="block text-sm font-medium">Tipo</label>
        <select name="tipo" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2">
          <option value="">Selecciona</option>
          <?php
            $tipos = ['Accidente','Pelea','Robo','Desastre','Incendio','Inundación','Sismo'];
            $sel = $_POST['tipo'] ?? '';
            foreach ($tipos as $t) {
              $s = $sel === $t ? 'selected' : '';
              echo "<option $s>" . htmlspecialchars($t) . "</option>";
            }
          ?>
        </select>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">Título</label>
      <input name="titulo" type="text" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" placeholder="Ej. Colisión en la 27 de Febrero" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" />
    </div>

    <div>
      <label class="block text-sm font-medium">Descripción</label>
      <textarea name="descripcion" rows="4" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" placeholder="Describe lo ocurrido..."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Provincia</label>
        <input name="provincia" type="text" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= htmlspecialchars($_POST['provincia'] ?? '') ?>" />
      </div>
      <div>
        <label class="block text-sm font-medium">Municipio</label>
        <input name="municipio" type="text" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= htmlspecialchars($_POST['municipio'] ?? '') ?>" />
      </div>
      <div>
        <label class="block text-sm font-medium">Barrio</label>
        <input name="barrio" type="text" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= htmlspecialchars($_POST['barrio'] ?? '') ?>" />
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Coordenadas (lat, lng)</label>
        <div class="mt-1 grid grid-cols-2 gap-3">
          <input name="lat" type="number" step="any" class="w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" placeholder="18.48" value="<?= htmlspecialchars($_POST['lat'] ?? '') ?>" />
          <input name="lng" type="number" step="any" class="w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" placeholder="-69.9" value="<?= htmlspecialchars($_POST['lng'] ?? '') ?>" />
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium">Muertos</label>
          <input name="muertos" type="number" min="0" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= htmlspecialchars($_POST['muertos'] ?? '0') ?>" />
        </div>
        <div>
          <label class="block text-sm font-medium">Heridos</label>
          <input name="heridos" type="number" min="0" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= htmlspecialchars($_POST['heridos'] ?? '0') ?>" />
        </div>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Pérdida estimada (RD$)</label>
        <input name="perdida" type="number" min="0" step="0.01" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" value="<?= htmlspecialchars($_POST['perdida'] ?? '0') ?>" />
      </div>
      <div>
        <label class="block text-sm font-medium">Link a redes sociales</label>
        <input name="link" type="url" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" placeholder="https://..." value="<?= htmlspecialchars($_POST['link'] ?? '') ?>" />
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">Foto del hecho</label>
      <input name="foto" type="file" accept="image/*" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2 bg-white" />
    </div>

    <div class="flex items-center justify-end gap-3">
      <a href="list.php" class="rounded-xl ring-1 ring-slate-300 px-4 py-2 text-sm">Cancelar</a>
      <button type="submit" class="rounded-2xl bg-indigo-600 text-white px-4 py-2 text-sm font-semibold">Publicar</button>
    </div>
  </form>
</main>

<?php include $prefix . '/partials/footer.php'; ?>
