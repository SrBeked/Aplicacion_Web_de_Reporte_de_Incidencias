<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mapa de Incidencias</title>
  <link rel="icon" href="<?= $prefix ?>/assets/img/logo.svg?2">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { theme: { extend: { borderRadius: { '2xl': '1rem' }}} };</script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script defer src="<?= $prefix ?>/assets/js/app.js"></script>
</head>
<body class="bg-white text-slate-800 selection:bg-indigo-600 selection:text-white">
