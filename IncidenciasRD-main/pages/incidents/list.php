<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Incidencias - Sistema de Reportes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
        
        #map { 
            height: 480px; 
            border-radius: 1.5rem;
            z-index: 1;
        }
        
        .incident-marker {
            transition: all 0.3s ease;
        }
        
        .incident-marker:hover {
            transform: scale(1.2);
            z-index: 1000;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .incident-item {
            transition: all 0.2s ease;
        }
        
        .incident-item:hover {
            background-color: #f8fafc !important;
            transform: translateX(5px);
        }
        
        .filter-section {
            transition: all 0.3s ease;
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
        
        /* Estilos para los clusters de marcadores */
        .marker-cluster-small {
            background-color: rgba(181, 226, 140, 0.6);
        }
        .marker-cluster-small div {
            background-color: rgba(110, 204, 57, 0.6);
        }

        .marker-cluster-medium {
            background-color: rgba(241, 211, 87, 0.6);
        }
        .marker-cluster-medium div {
            background-color: rgba(240, 194, 12, 0.6);
        }

        .marker-cluster-large {
            background-color: rgba(253, 156, 115, 0.6);
        }
        .marker-cluster-large div {
            background-color: rgba(241, 128, 23, 0.6);
        }
        
        /* Leyenda del mapa */
        .map-legend {
            padding: 10px;
            background: white;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            line-height: 1.5;
        }
        
        .map-legend h4 {
            margin: 0 0 10px;
            font-weight: bold;
        }
        
        .map-legend i {
            width: 18px;
            height: 18px;
            float: left;
            margin-right: 8px;
            opacity: 0.7;
            border-radius: 50%;
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        .leaflet-popup-content {
            margin: 15px 19px;
            min-width: 200px;
        }
        
        .incident-type-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header (simulado) -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold mr-3">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h1 class="text-xl font-semibold text-gray-900">Mapa de Incidencias</h1>
            </div>
                          <nav class="hidden md:flex space-x-8">
                  <a href="../pages/dashboard.php" class="text-gray-500 hover:text-indigo-600">Dashboard</a>
                  <a href="../index.php" class="text-indigo-600 font-medium">Mapa</a>
                  <a href="../pages/stats.php" class="text-gray-500 hover:text-indigo-600">Estadísticas</a>
                  <a href="../pages/settings.php" class="text-gray-500 hover:text-indigo-600">Configuración</a>
              </nav>

            <button class="md:hidden text-gray-500">
                <i class="fas fa-bars text-xl"></i>
            </button>  
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 fade-in">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mapa de Incidencias</h1>
                <p class="text-gray-600 mt-2">Explora las incidencias recientes en el mapa y filtra por tipo, provincia y fecha.</p>
                <p class="text-sm text-gray-400 mt-1"><i class="fas fa-info-circle mr-1"></i> Haz clic en cualquier marcador para ver detalles</p>
            </div>
            <div class="flex gap-3">
                <a href="new.php" class="rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 text-sm font-semibold flex items-center gap-2 transition-all card-hover">
                    <i class="fas fa-plus-circle"></i> Reportar incidencia
                </a>
                <button id="btn-locate" class="rounded-2xl bg-white text-gray-700 hover:bg-gray-100 ring-1 ring-gray-300 px-4 py-3 text-sm font-semibold flex items-center gap-2 transition-all card-hover" title="Centrar en mi ubicación">
                    <i class="fas fa-location-arrow"></i>
                </button>
            </div>
        </div>

        <section class="mt-6 grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 rounded-3xl ring-1 ring-slate-200 overflow-hidden bg-white shadow-sm card-hover relative">
                <div id="map"></div>
                <!-- Controles del mapa -->
                <div class="absolute top-4 right-4 z-[1000] flex flex-col gap-2">
                    <button id="btn-zoom-in" class="bg-white text-gray-700 hover:bg-gray-100 rounded-full w-10 h-10 flex items-center justify-center shadow-md" title="Acercar">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button id="btn-zoom-out" class="bg-white text-gray-700 hover:bg-gray-100 rounded-full w-10 h-10 flex items-center justify-center shadow-md" title="Alejar">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <!-- Leyenda del mapa -->
                <div class="absolute bottom-4 left-4 z-[1000] map-legend">
                    <h4>Leyenda</h4>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center">
                            <i style="background: #EF4444"></i> <span>Accidentes</span>
                        </div>
                        <div class="flex items-center">
                            <i style="background: #F59E0B"></i> <span>Robos</span>
                        </div>
                        <div class="flex items-center">
                            <i style="background: #10B981"></i> <span>Desastres</span>
                        </div>
                        <div class="flex items-center">
                            <i style="background: #3B82F6"></i> <span>Peleas</span>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-2xl ring-1 ring-slate-200 bg-white p-5 shadow-sm card-hover filter-section">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold text-lg">Filtros</h2>
                        <button id="btn-toggle-filters" class="text-gray-500 hover:text-indigo-600 md:hidden">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div id="filters-content" class="mt-4">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de incidencia</label>
                                <select id="filterType" class="w-full rounded-xl ring-1 ring-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Todos los tipos</option>
                                    <option value="Accidente">Accidente</option>
                                    <option value="Pelea">Pelea</option>
                                    <option value="Robo">Robo</option>
                                    <option value="Desastre">Desastre</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Provincia</label>
                                <select id="filterProv" class="w-full rounded-xl ring-1 ring-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Todas las provincias</option>
                                    <option>Santo Domingo</option>
                                    <option>Santiago</option>
                                    <option>La Vega</option>
                                    <option>Puerto Plata</option>
                                    <option>Distrito Nacional</option>
                                    <option>San Cristóbal</option>
                                    <option>La Altagracia</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rango de fechas</label>
                                <div class="flex gap-2">
                                    <input id="filterDateStart" type="date" class="flex-1 rounded-xl ring-1 ring-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500" />
                                    <span class="self-center text-gray-400">a</span>
                                    <input id="filterDateEnd" type="date" class="flex-1 rounded-xl ring-1 ring-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500" />
                                </div>
                            </div>
                            <div class="flex gap-2 pt-2">
                                <button id="btnFilter" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-4 py-2.5 text-sm font-semibold transition-all">
                                    Aplicar filtros
                                </button>
                                <button id="btnClear" class="bg-white text-gray-700 hover:bg-gray-100 rounded-xl ring-1 ring-slate-300 px-4 py-2.5 text-sm font-semibold transition-all" title="Limpiar filtros">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl ring-1 ring-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <h2 class="font-semibold">Incidencias <span id="incident-count" class="text-indigo-600">(4)</span></h2>
                        <div class="relative">
                            <input type="text" id="searchIncidents" placeholder="Buscar..." class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 pl-9">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-200 max-h-[500px] overflow-y-auto" id="listContainer">
                        <!-- Las incidencias se cargarán aquí -->
                    </div>
                </div>
            </aside>
        </section>
    </main>

    <!-- Footer (simulado) -->
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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Inicializar el mapa
            const map = L.map('map').setView([18.7357, -70.1627], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
                maxZoom: 19, 
                attribution: '© OpenStreetMap',
                className: 'map-tiles'
            }).addTo(map);

            // Añadir capa de satélite
            const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
            });

            // Añadir control de capas
            const baseMaps = {
                "Mapa": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
                    maxZoom: 19, 
                    attribution: '© OpenStreetMap'
                }),
                "Satélite": satelliteLayer
            };
            
            L.control.layers(baseMaps).addTo(map);

            // Datos de ejemplo
            const data = [
                { 
                    id: 1, 
                    titulo: 'Colisión múltiple en autopista', 
                    tipo: 'Accidente', 
                    prov: 'Santo Domingo', 
                    fecha: '2025-08-12', 
                    lat: 18.48, 
                    lng: -69.9, 
                    muertos: 0, 
                    heridos: 2,
                    descripcion: 'Choque entre tres vehículos en la autopista Las Américas, sentido este. Se reportan dos heridos leves.',
                    direccion: 'Autopista Las Américas, km 12'
                },
                { 
                    id: 2, 
                    titulo: 'Robo en tienda comercial', 
                    tipo: 'Robo', 
                    prov: 'Santiago', 
                    fecha: '2025-08-11', 
                    lat: 19.45, 
                    lng: -70.69, 
                    muertos: 0, 
                    heridos: 0,
                    descripcion: 'Sujetos armados sustrajeron efectivo y mercancía de una tienda en el centro comercial.',
                    direccion: 'Centro Commercial Santiago, local 205'
                },
                { 
                    id: 3, 
                    titulo: 'Deslizamiento de tierra', 
                    tipo: 'Desastre', 
                    prov: 'La Vega', 
                    fecha: '2025-08-10', 
                    lat: 19.22, 
                    lng: -70.52, 
                    muertos: 0, 
                    heridos: 1,
                    descripcion: 'Deslizamiento de tierra en zona montañosa debido a las lluvias recientes. Una vivienda afectada.',
                    direccion: 'Carretera Jarabacoa - Manabao, km 8'
                },
                { 
                    id: 4, 
                    titulo: 'Pelea callejera con heridos', 
                    tipo: 'Pelea', 
                    prov: 'Puerto Plata', 
                    fecha: '2025-08-09', 
                    lat: 19.79, 
                    lng: -70.69, 
                    muertos: 0, 
                    heridos: 1,
                    descripcion: 'Conflicto entre grupos en la calle principal. Un herido trasladado al hospital local.',
                    direccion: 'Calle Separación esq. Beller'
                },
                { 
                    id: 5, 
                    titulo: 'Accidente de tránsito', 
                    tipo: 'Accidente', 
                    prov: 'Distrito Nacional', 
                    fecha: '2025-08-08', 
                    lat: 18.47, 
                    lng: -69.93, 
                    muertos: 0, 
                    heridos: 1,
                    descripcion: 'Colisión entre motocicleta y automóvil en intersección con semáforo dañado.',
                    direccion: 'Av. John F. Kennedy esq. Churchill'
                },
                { 
                    id: 6, 
                    titulo: 'Robo a transeúnte', 
                    tipo: 'Robo', 
                    prov: 'San Cristóbal', 
                    fecha: '2025-08-07', 
                    lat: 18.42, 
                    lng: -70.11, 
                    muertos: 0, 
                    heridos: 0,
                    descripcion: 'Sujetos en motocicleta sustrajeron pertenencias a un transeúnte en la calle principal.',
                    direccion: 'Calle Padre Ayala, Zona Centro'
                }
            ];

            // Iconos personalizados para cada tipo de incidencia
            const iconColors = {
                'Accidente': '#EF4444', // Rojo
                'Robo': '#F59E0B',      // Amarillo
                'Desastre': '#10B981',  // Verde
                'Pelea': '#3B82F6'      // Azul
            };

            function createCustomIcon(tipo) {
                const color = iconColors[tipo] || '#6B7280'; // Gris por defecto
                
                return L.divIcon({
                    html: `<div style="background-color: ${color}; width: 24px; height: 24px; border: 3px solid white; border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                    className: 'incident-marker',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
            }

            const markers = L.markerClusterGroup({
                chunkedLoading: true,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true
            });

            // Función para formatear la fecha
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            // Función para calcular días transcurridos
            function daysAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffTime = Math.abs(now - date);
                return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            }

            // Función para renderizar marcadores y lista
            function render(list = data) {
                // Limpiar marcadores existentes
                markers.clearLayers();
                
                const cont = document.getElementById('listContainer');
                cont.innerHTML = '';
                
                // Actualizar contador
                document.getElementById('incident-count').textContent = `(${list.length})`;
                
                // Si no hay resultados, mostrar mensaje
                if (list.length === 0) {
                    cont.innerHTML = `
                        <div class="p-8 text-center">
                            <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No se encontraron incidencias con los filtros aplicados</p>
                        </div>
                    `;
                    return;
                }
                
                // Añadir marcadores al mapa
                list.forEach(item => {
                    const marker = L.marker([item.lat, item.lng], { 
                        icon: createCustomIcon(item.tipo)
                    });
                    
                    // Popup con información detallada
                    const popupContent = `
                        <div class="min-w-[250px]">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <h3 class="font-bold text-lg">${item.titulo}</h3>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: ${iconColors[item.tipo]}20; color: ${iconColors[item.tipo]}; border: 1px solid ${iconColors[item.tipo]}40;">${item.tipo}</span>
                            </div>
                            <div class="text-sm text-gray-600 mb-3">
                                <p class="mb-1"><i class="fas fa-map-marker-alt mr-2"></i> ${item.direccion || 'Dirección no disponible'}</p>
                                <p class="mb-1"><i class="fas fa-calendar-alt mr-2"></i> ${formatDate(item.fecha)}</p>
                                <p class="mb-1"><i class="fas fa-map-pin mr-2"></i> ${item.prov}</p>
                            </div>
                            <p class="text-sm mb-3">${item.descripcion}</p>
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>ID: #${item.id}</span>
                                <a href="view.php?id=${item.id}" class="text-indigo-600 hover:text-indigo-800 font-medium">Ver detalles →</a>
                            </div>
                        </div>
                    `;
                    
                    marker.bindPopup(popupContent, { maxWidth: 300 });
                    markers.addLayer(marker);
                    
                    // Añadir a la lista
                    const row = document.createElement('a');
                    row.href = `view.php?id=${item.id}`;
                    row.className = 'block p-4 hover:bg-slate-50 incident-item';
                    row.innerHTML = `
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <h3 class="font-semibold text-gray-800">${item.titulo}</h3>
                            <span class="px-2 py-1 rounded-full text-xs ring-1 ring-slate-200 whitespace-nowrap">ID #${item.id}</span>
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="incident-type-badge" style="background: ${iconColors[item.tipo]}20; color: ${iconColors[item.tipo]}; border: 1px solid ${iconColors[item.tipo]}40;">
                                <i class="fas ${item.tipo === 'Accidente' ? 'fa-car-crash' : item.tipo === 'Robo' ? 'fa-store-alt' : item.tipo === 'Desastre' ? 'fa-mountain' : 'fa-fist-raised'}"></i>
                                ${item.tipo}
                            </span>
                            <span class="text-xs text-gray-500">${item.prov}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">${formatDate(item.fecha)}</span>
                            <span class="text-xs text-gray-400">Hace ${daysAgo(item.fecha)} días</span>
                        </div>
                    `;
                    cont.appendChild(row);
                });
                
                // Añadir cluster al mapa
                map.addLayer(markers);
                
                // Ajustar vista del mapa para mostrar todos los marcadores
                if (list.length > 0) {
                    const group = new L.featureGroup(list.map(item => L.marker([item.lat, item.lng])));
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }

            // Aplicar filtros
            function applyFilters() {
                const tipo = document.getElementById('filterType').value;
                const prov = document.getElementById('filterProv').value;
                const dateStart = document.getElementById('filterDateStart').value;
                const dateEnd = document.getElementById('filterDateEnd').value;
                
                const filtered = data.filter(item => {
                    // Filtro por tipo
                    if (tipo && item.tipo !== tipo) return false;
                    
                    // Filtro por provincia
                    if (prov && item.prov !== prov) return false;
                    
                    // Filtro por fecha
                    if (dateStart && item.fecha < dateStart) return false;
                    if (dateEnd && item.fecha > dateEnd) return false;
                    
                    return true;
                });
                
                render(filtered);
            }

            // Búsqueda en tiempo real
            document.getElementById('searchIncidents').addEventListener('input', function() {
                const searchValue = this.value.toLowerCase();
                const items = document.querySelectorAll('.incident-item');
                
                items.forEach(item => {
                    const title = item.querySelector('h3').textContent.toLowerCase();
                    const type = item.querySelector('.incident-type-badge').textContent.toLowerCase();
                    const province = item.querySelector('.text-xs.text-gray-500').textContent.toLowerCase();
                    
                    if (title.includes(searchValue) || type.includes(searchValue) || province.includes(searchValue)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Event listeners
            document.getElementById('btnFilter').addEventListener('click', applyFilters);
            document.getElementById('btnClear').addEventListener('click', () => {
                document.getElementById('filterType').value = '';
                document.getElementById('filterProv').value = '';
                document.getElementById('filterDateStart').value = '';
                document.getElementById('filterDateEnd').value = '';
                document.getElementById('searchIncidents').value = '';
                render(data);
            });

            // Controles del mapa
            document.getElementById('btn-zoom-in').addEventListener('click', () => {
                map.zoomIn();
            });

            document.getElementById('btn-zoom-out').addEventListener('click', () => {
                map.zoomOut();
            });

            // Geolocalización
            document.getElementById('btn-locate').addEventListener('click', () => {
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            const { latitude, longitude } = position.coords;
                            map.setView([latitude, longitude], 13);
                            
                            // Añadir marcador de ubicación actual
                            L.marker([latitude, longitude], {
                                icon: L.divIcon({
                                    html: '<div style="background-color: #4f46e5; width: 24px; height: 24px; border: 3px solid white; border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
                                    className: 'current-location-marker',
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                })
                            })
                            .addTo(map)
                            .bindPopup('Tu ubicación actual')
                            .openPopup();
                        },
                        error => {
                            alert('No se pudo obtener tu ubicación. Asegúrate de haber permitido el acceso a la ubicación.');
                        }
                    );
                } else {
                    alert('Tu navegador no soporta geolocalización');
                }
            });

            // Inicializar renderizado
            render(data);

            // Establecer fecha máxima como hoy
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('filterDateStart').max = today;
            document.getElementById('filterDateEnd').max = today;
        });
    </script>
</body>
</html>
