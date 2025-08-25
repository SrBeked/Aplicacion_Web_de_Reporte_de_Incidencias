<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Sistema de Incidencias</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
        }
        
        .incident-row {
            transition: background-color 0.2s;
        }
        
        .incident-row:hover {
            background-color: #f9fafb !important;
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold mr-3">D</div>
                <h1 class="text-xl font-semibold text-gray-900">Sistema de Incidencias</h1>
            </div>
            <nav class="hidden md:flex space-x-8" id="desktop-menu">
                <a href="#" class="text-gray-500 hover:text-indigo-600">Incidencias</a>
                <a href="#" class="text-gray-500 hover:text-indigo-600">Estadísticas</a>
                <a href="#" class="text-gray-500 hover:text-indigo-600">Configuración</a>
            </nav>
            <button class="md:hidden text-gray-500" id="mobile-toggle">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
        <nav class="hidden px-4 pb-4 md:hidden bg-white space-y-2" id="mobile-menu">
            <a href="#" class="block text-gray-500 hover:text-indigo-600">Incidencias</a>
            <a href="#" class="block text-gray-500 hover:text-indigo-600">Estadísticas</a>
            <a href="#" class="block text-gray-500 hover:text-indigo-600">Configuración</a>
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 fade-in">
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Panel de Usuario</h1>
                <p class="text-gray-600 mt-2 text-sm">Bienvenido, <strong id="username">Daniel</strong>. Aquí puedes ver el resumen de tus incidencias.</p>
                <p class="text-gray-400 text-xs mt-1" id="last-login">Último acceso: 26 ago 2023, 15:42</p>
            </div>
            <a href="incidents/new.php" class="mt-4 sm:mt-0 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow transition-all card-hover">
                <i class="fas fa-plus-circle"></i>
                Nueva Incidencia
            </a>
        </div>

        <!-- Filtros -->
        <div class="bg-white p-4 rounded-2xl shadow-sm mb-6 flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <h2 class="text-lg font-semibold text-gray-800">Filtrar incidencias</h2>
            <div class="flex flex-wrap gap-3">
                <select class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                    <option selected>Todos los estados</option>
                    <option>Pendientes</option>
                    <option>En revisión</option>
                    <option>Resueltos</option>
                    <option>Cerrados</option>
                </select>
                
                <select class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                    <option selected>Todos los tipos</option>
                    <option>Accidente</option>
                    <option>Robo</option>
                    <option>Infraestructura</option>
                    <option>Servicios</option>
                </select>
                
                <button class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 text-sm font-medium px-4 rounded-lg transition-colors">
                    <i class="fas fa-filter mr-1"></i> Aplicar
                </button>
            </div>
        </div>

        <!-- Tarjetas de resumen -->
        <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-10">
            <div class="p-5 rounded-2xl ring-1 ring-slate-200 bg-white shadow-sm card-hover">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-500">Incidencias reportadas</p>
                        <p class="text-4xl font-extrabold text-indigo-600 mt-2">12</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-full">
                        <i class="fas fa-flag text-indigo-600"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3"><i class="fas fa-arrow-up text-green-500 mr-1"></i> 2 más que el mes anterior</p>
            </div>
            
            <div class="p-5 rounded-2xl ring-1 ring-slate-200 bg-white shadow-sm card-hover">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-500">Pendientes de revisión</p>
                        <p class="text-4xl font-extrabold text-yellow-500 mt-2">3</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3"><i class="fas fa-arrow-down text-red-500 mr-1"></i> 1 menos que la semana pasada</p>
            </div>
            
            <div class="p-5 rounded-2xl ring-1 ring-slate-200 bg-white shadow-sm card-hover">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-500">Correcciones aceptadas</p>
                        <p class="text-4xl font-extrabold text-green-600 mt-2">5</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3"><i class="fas fa-arrow-up text-green-500 mr-1"></i> 3 más que el mes anterior</p>
            </div>
        </section>

        <!-- Tabla de incidencias -->
        <div class="mt-10 bg-white shadow-md rounded-2xl overflow-hidden ring-1 ring-slate-200">
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between bg-slate-50 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-2 sm:mb-0">Tus últimas incidencias</h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Buscar incidencia..." class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 pl-9">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <a href="incidents/new.php" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-semibold transition-all">
                        <i class="fas fa-plus"></i> Nueva
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer">
                                <span>Fecha</span> <i class="fas fa-sort text-gray-400 ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white" id="incidents-table">
                        <tr class="incident-row">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>2025-08-12</div>
                                <div class="text-xs text-gray-400">Hace 2 días</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-800 font-medium">Colisión múltiple en la 27</div>
                                <div class="text-xs text-gray-500">Calle 27 con Carrera 15</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="status-badge bg-red-100 text-red-700"><i class="fas fa-car-crash mr-1"></i> Accidente</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="status-badge bg-green-100 text-green-700"><i class="fas fa-check-circle mr-1"></i> Publicado</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex justify-end space-x-3">
                                    <a class="text-indigo-600 hover:text-indigo-800 transition-all" href="incidents/view.php" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="text-yellow-600 hover:text-yellow-800 transition-all" href="#" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a class="text-red-600 hover:text-red-800 transition-all" href="#" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- ... otras filas ... -->
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Mostrando <span class="font-semibold">3</span> de <span class="font-semibold">12</span> incidencias
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded-md">1</button>
                    <button class="px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">2</button>
                    <button class="px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">3</button>
                    <button class="px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white mt-12 py-8 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="text-center md:text-left">
                    <p class="text-sm text-gray-500">
                        &copy; 2023 Sistema de Reporte de Incidencias. Todos los derechos reservados.
                    </p>
                </div>
                <div class="mt-4 flex justify-center md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-gray-500 mx-3">
                        <i class="fab fa-facebook text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-500 mx-3">
                        <i class="fab fa-twitter text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-500 mx-3">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simular carga de datos
            setTimeout(() => {
                document.getElementById('last-login').textContent = 'Último acceso: ' + new Date().toLocaleString();
            }, 1000);

            // Toggle menú móvil
            const toggle = document.getElementById('mobile-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            toggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });

            // Funcionalidad de ordenamiento de tabla (solo ícono)
            const headers = document.querySelectorAll('th');
            headers.forEach(header => {
                header.addEventListener('click', () => {
                    const icon = header.querySelector('i');
                    if (icon) {
                        if (icon.classList.contains('fa-sort')) {
                            icon.classList.replace('fa-sort', 'fa-sort-up');
                        } else if (icon.classList.contains('fa-sort-up')) {
                            icon.classList.replace('fa-sort-up', 'fa-sort-down');
                        } else {
                            icon.classList.replace('fa-sort-down', 'fa-sort-up');
                        }
                    }
                });
            });

            // Funcionalidad de búsqueda
            const searchInput = document.querySelector('input[type="text"]');
            searchInput.addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const rows = document.querySelectorAll('.incident-row');
                
                rows.forEach(row => {
                    const title = row.querySelector('.text-gray-800').textContent.toLowerCase();
                    const description = row.querySelector('.text-gray-500').textContent.toLowerCase();
                    
                    if (title.includes(searchValue) || description.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Efectos de hover para las tarjetas
            const cards = document.querySelectorAll('.card-hover');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.classList.add('shadow-md');
                });
                
                card.addEventListener('mouseleave', () => {
                    card.classList.remove('shadow-md');
                });
            });
        });
    </script>
</body>
</html>
