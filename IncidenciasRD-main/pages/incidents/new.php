<?php $prefix = '../..'; ?>
<?php include $prefix . '/partials/head.php'; ?>
<?php include $prefix . '/partials/header.php'; ?>

<main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <h1 class="text-2xl font-bold">Reportar nueva incidencia</h1>
  <p class="text-slate-600 mt-2">Este formulario es solo de demostración; no guarda información.</p>

  <form id="incidentForm" class="mt-8 space-y-6 bg-white p-6 rounded-2xl ring-1 ring-slate-200">
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Fecha</label>
        <input type="date" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm font-medium">Tipo</label>
        <select required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2">
          <option value="">Selecciona</option>
          <option>Accidente</option>
          <option>Pelea</option>
          <option>Robo</option>
          <option>Desastre</option>
        </select>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">Título</label>
      <input type="text" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" placeholder="Ej. Colisión en la 27 de Febrero" />
    </div>

    <div>
      <label class="block text-sm font-medium">Descripción</label>
      <textarea required rows="4" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" placeholder="Describe lo ocurrido..."></textarea>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Provincia</label>
        <input type="text" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm font-medium">Municipio</label>
        <input type="text" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm font-medium">Barrio</label>
        <input type="text" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Coordenadas (lat, lng)</label>
        <div class="mt-1 grid grid-cols-2 gap-3">
          <input type="number" step="any" class="w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" placeholder="18.48" />
          <input type="number" step="any" class="w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" placeholder="-69.9" />
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium">Muertos</label>
          <input type="number" min="0" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-medium">Heridos</label>
          <input type="number" min="0" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
        </div>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Pérdida estimada (RD$)</label>
        <input type="number" min="0" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm font-medium">Link a redes sociales</label>
        <input type="url" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2" placeholder="https://..." />
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">Foto del hecho</label>
      <input type="file" accept="image/*" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2 bg-white" />
    </div>

    <div class="flex items-center justify-end gap-3">
      <a href="list.php" class="rounded-xl ring-1 ring-slate-300 px-4 py-2 text-sm">Cancelar</a>
      <button type="submit" class="rounded-2xl bg-indigo-600 text-white px-4 py-2 text-sm font-semibold">Publicar</button>
    </div>
  </form>
</main>

<script>
  document.getElementById('incidentForm').addEventListener('submit', function(e){
    e.preventDefault();
    alert('Prototipo: la incidencia no se guarda.');
  });
</script>

<?php include $prefix . '/partials/footer.php'; ?>