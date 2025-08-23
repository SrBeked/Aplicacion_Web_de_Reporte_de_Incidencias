<?php $prefix = '..'; ?>
<?php include $prefix . '/partials/head.php'; ?>
<?php include $prefix . '/partials/header.php'; ?>

<main class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <h1 class="text-2xl font-bold">Iniciar sesión</h1>
  <p class="text-slate-600 mt-2">Accede a tu cuenta para reportar y gestionar incidencias.</p>
  <form id="loginForm" class="mt-8 space-y-4 bg-white p-6 rounded-2xl ring-1 ring-slate-200">
    <div>
      <label class="block text-sm font-medium">Correo</label>
      <input type="email" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600" placeholder="tucorreo@ejemplo.com" />
    </div>
    <div>
      <label class="block text-sm font-medium">Contraseña</label>
      <input type="password" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600" placeholder="••••••••" />
    </div>
    <button type="submit" class="w-full rounded-2xl bg-indigo-600 text-white font-semibold px-4 py-2 hover:shadow">Entrar</button>
  </form>
  <p class="mt-4 text-sm">¿No tienes cuenta? <a href="register.php" class="text-indigo-600 font-semibold">Regístrate</a></p>
</main>

<?php include $prefix . '/partials/footer.php'; ?>