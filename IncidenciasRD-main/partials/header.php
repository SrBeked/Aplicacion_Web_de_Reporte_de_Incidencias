<header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
  <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
    <a href="<?= $prefix ?>/index.php" class="flex items-center gap-2 font-extrabold text-lg">
      <img src="https://cdn-icons-png.freepik.com/256/10266/10266552.png?semt=ais_white_label" alt="logo" class="w-7 h-7">
      <span>Incidencias<span class="text-indigo-600">RD</span></span>
    </a>
    <button id="menuBtn" class="md:hidden p-2 rounded-xl ring-1 ring-slate-200" aria-label="Abrir menú">☰</button>
    <ul id="menu" class="hidden md:flex items-center gap-6 text-sm font-semibold">
      <li><a class="hover:text-indigo-600" href="<?= $prefix ?>/index.php">Inicio</a></li>
      <li><a class="hover:text-indigo-600" href="<?= $prefix ?>/pages/incidents/list.php">Incidencias</a></li>
      <li><a class="hover:text-indigo-600" href="<?= $prefix ?>/pages/dashboard.php">Panel</a></li>
      <li><a class="hover:text-indigo-600" href="<?= $prefix ?>/pages/login.php">Entrar</a></li>
      <li><a class="hover:text-indigo-600" href="<?= $prefix ?>/pages/register.php">Registro</a></li>
    </ul>
  </nav>
  <div id="mobileMenu" class="md:hidden hidden border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 py-3 space-y-2 text-sm font-semibold">
      <a class="block" href="<?= $prefix ?>/index.php">Inicio</a>
      <a class="block" href="<?= $prefix ?>/pages/incidents/list.php">Incidencias</a>
      <a class="block" href="<?= $prefix ?>/pages/dashboard.php">Panel</a>
      <a class="block" href="<?= $prefix ?>/pages/login.php">Entrar</a>
      <a class="block" href="<?= $prefix ?>/pages/register.php">Registro</a>
    </div>
  </div>
</header>