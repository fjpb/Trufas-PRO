<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>TrufaControl Pro | Gestión profesional de truficultura</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: system-ui, 'Segoe UI', 'Inter', -apple-system, sans-serif;
        }
        body {
            background: #f5efe6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .login-card {
            background: white;
            max-width: 400px;
            margin: 80px auto;
            border-radius: 48px;
            padding: 32px 28px;
            box-shadow: 0 20px 35px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card {
            background: white;
            border-radius: 32px;
            padding: 24px;
            margin-bottom: 28px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.05);
            border: 1px solid #e6d8ca;
        }
        h1, h2, h3 {
            color: #4a3422;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            font-weight: 500;
            display: block;
            margin-bottom: 6px;
            color: #6b4c34;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #dcccb8;
            border-radius: 40px;
            font-size: 1rem;
        }
        button, .btn {
            background: #7a4c2c;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }
        button:hover { background: #56341c; }
        .btn-secondary {
            background: #e8ddd0;
            color: #4a3422;
        }
        .btn-sm {
            background: #e3d5c6;
            color: #4a3422;
            padding: 6px 14px;
            font-size: 0.75rem;
            border-radius: 30px;
            border: none;
            cursor: pointer;
        }
        .grid-melgas {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .melga-card, .arbol-card {
            background: #fefbf7;
            border-radius: 28px;
            padding: 18px;
            border: 1px solid #eedfcb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
        }
        th, td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #f0e4d8;
        }
        th {
            background: #f9f2ea;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #7a4c2c;
        }
        .flex {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }
        .alert {
            background: #e3f5e3;
            padding: 12px 18px;
            border-radius: 40px;
            margin-bottom: 20px;
        }
        .chart-container {
            width: 100%;
            height: 250px;
            margin: 20px 0;
        }
        @media (max-width: 700px) {
            .grid-melgas { grid-template-columns: 1fr; }
            body { padding: 12px; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div id="app">
    <!-- Pantalla de login -->
    <div id="loginScreen" class="container">
        <div class="login-card">
            <h1>🍄 TrufaControl Pro</h1>
            <p>Control de melgas y cosecha por árbol</p>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="loginEmail" placeholder="admin@trufa.com">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" id="loginPassword" placeholder="campo123">
            </div>
            <button id="doLogin">Ingresar</button>
            <p style="margin-top:20px; font-size:0.8rem;">Demo: admin@trufa.com / campo123</p>
        </div>
    </div>

    <!-- Panel principal (oculto hasta login) -->
    <div id="mainPanel" style="display:none;">
        <div class="container">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:20px;">
                <h1>🍄 Mis Melgas Truferas</h1>
                <button id="logoutBtn" class="btn-secondary">Cerrar sesión</button>
            </div>

            <!-- Tarjetas resumen dinámicas -->
            <div class="card" style="display:flex; gap:20px; flex-wrap:wrap; justify-content:space-between;">
                <div><strong>🌳 Árboles totales</strong><br><span id="totalArboles" class="stat-number">0</span></div>
                <div><strong>🍄 Árboles productores</strong><br><span id="enProduccion" class="stat-number">0</span></div>
                <div><strong>📦 Total kg cosechados</strong><br><span id="totalKg" class="stat-number">0</span></div>
                <div><strong>🔢 Total trufas</strong><br><span id="totalTrufas" class="stat-number">0</span></div>
                <div><strong>📊 Melgas activas</strong><br><span id="totalMelgas" class="stat-number">0</span></div>
            </div>

            <!-- Formulario nueva melga -->
            <div class="card">
                <h2>➕ Nueva melga</h2>
                <div class="flex">
                    <div class="form-group" style="flex:2"><label>Nombre</label><input type="text" id="nuevaMelgaNombre" placeholder="Ej: Encinares Sur"></div>
                    <div class="form-group" style="flex:1"><label>N° árboles</label><input type="number" id="nuevaMelgaArboles" min="1" value="10"></div>
                    <div class="form-group" style="flex:1"><label>Ubicación</label><input type="text" id="nuevaMelgaUbicacion" placeholder="Opcional"></div>
                    <div class="form-group" style="align-self:flex-end"><button id="crearMelgaBtn">Crear melga</button></div>
                </div>
            </div>

            <!-- Listado de melgas -->
            <div class="card">
                <h2>🌲 Melgas registradas</h2>
                <div id="melgasContainer" class="grid-melgas"></div>
            </div>

            <!-- Detalle de melga (árboles) -->
            <div id="detalleMelga" style="display:none;">
                <div class="card">
                    <h2 id="detalleTitulo"></h2>
                    <div id="arbolesLista"></div>
                    <button id="cerrarDetalle" class="btn-secondary">Cerrar</button>
                </div>
            </div>

            <!-- Vista detallada de un árbol (historial + gráfico) -->
            <div id="detalleArbol" style="display:none;">
                <div class="card">
                    <h2 id="arbolDetalleTitulo"></h2>
                    <div class="chart-container"><canvas id="arbolChart"></canvas></div>
                    <h3>Historial de cosechas</h3>
                    <div id="cosechasArbolLista"></div>
                    <button id="cerrarDetalleArbol" class="btn-secondary">Cerrar</button>
                </div>
            </div>

            <!-- Historial global con filtros avanzados -->
            <div class="card">
                <h2>📜 Historial de cosechas</h2>
                <div class="flex" style="margin-bottom:16px;">
                    <div class="form-group" style="flex:1"><label>Melga</label><select id="filtroMelgaHistorial"></select></div>
                    <div class="form-group" style="flex:1"><label>Desde</label><input type="date" id="filtroFechaInicio"></div>
                    <div class="form-group" style="flex:1"><label>Hasta</label><input type="date" id="filtroFechaFin"></div>
                    <div class="form-group" style="flex:1"><label>Árbol inactivo (meses sin cosecha)</label><input type="number" id="filtroInactivos" placeholder="meses" value="12"></div>
                    <div class="form-group" style="align-self:flex-end"><button id="aplicarFiltros" class="btn-secondary">Filtrar</button></div>
                </div>
                <div style="overflow-x:auto;">
                    <table id="tablaHistorial">
                        <thead><tr><th>Fecha</th><th>Melga</th><th>Árbol</th><th>N° trufas</th><th>Kg</th><th>Kg/trufa</th><th>Calidad</th><th>Observaciones</th><th></th></tr></thead>
                        <tbody id="historialBody"></tbody>
                    </table>
                </div>
                <div class="flex" style="justify-content:space-between; margin-top:16px;">
                    <button id="exportarCSV" class="btn-secondary">📎 Exportar a CSV</button>
                    <button id="respaldarDatos" class="btn-secondary">💾 Respaldar (JSON)</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ---------- CONFIGURACIÓN GLOBAL ----------
    let melgas = [];
    let arboles = [];      // cache árboles para la melga actual
    let cosechasGlobal = []; // cache para historial
    let chartInstance = null;

    // Variables de estado
    let melgaIdAbierta = null;
    let arbolIdAbierto = null;

    // Helper para mostrar mensajes
    function mostrarMensaje(msg, esError = false) {
        // eliminar alert anterior
        const existing = document.querySelector('.alert');
        if(existing) existing.remove();
        const div = document.createElement('div');
        div.className = 'alert';
        div.style.background = esError ? '#f8d7da' : '#e3f5e3';
        div.style.color = esError ? '#721c24' : '#2a6b2a';
        div.innerText = msg;
        const panel = document.getElementById('mainPanel');
        if(panel) panel.insertBefore(div, panel.firstChild);
        setTimeout(() => div.remove(), 4000);
    }

    // ---------- LLAMADAS API ----------
    async function apiFetch(url, options = {}) {
        const opts = {
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            ...options
        };
        const response = await fetch(url, opts);
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.error || `Error ${response.status}`);
        }
        return response.json();
    }

    // Login
    async function login(email, password) {
        const data = await apiFetch('api/login.php', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });
        if (data.success) {
            document.getElementById('loginScreen').style.display = 'none';
            document.getElementById('mainPanel').style.display = 'block';
            cargarDatosIniciales();
        } else {
            throw new Error(data.error || 'Credenciales inválidas');
        }
    }

    async function logout() {
        await apiFetch('api/logout.php', { method: 'POST' });
        document.getElementById('loginScreen').style.display = 'block';
        document.getElementById('mainPanel').style.display = 'none';
        document.getElementById('loginEmail').value = '';
        document.getElementById('loginPassword').value = '';
    }

    // Estadísticas
    async function cargarEstadisticas() {
        const stats = await apiFetch('api/estadisticas.php');
        document.getElementById('totalArboles').innerText = stats.total_arboles || 0;
        document.getElementById('enProduccion').innerText = stats.en_produccion || 0;
        document.getElementById('totalKg').innerText = (stats.total_kg || 0).toFixed(1);
        document.getElementById('totalTrufas').innerText = stats.total_trufas || 0;
        document.getElementById('totalMelgas').innerText = stats.total_melgas || 0;
    }

    // Melgas
    async function cargarMelgas() {
        melgas = await apiFetch('api/melgas.php');
        renderMelgas();
        // Actualizar filtro de historial
        const sel = document.getElementById('filtroMelgaHistorial');
        if(sel) {
            const curr = sel.value;
            sel.innerHTML = '<option value="0">Todas las melgas</option>' +
                melgas.map(m => `<option value="${m.id}">${escapeHtml(m.nombre)}</option>`).join('');
            if(curr && curr !== '0' && melgas.some(m => m.id == curr)) sel.value = curr;
            else sel.value = '0';
        }
    }

    function renderMelgas() {
        const container = document.getElementById('melgasContainer');
        if (!melgas.length) {
            container.innerHTML = '<p>No hay melgas. Cree una usando el formulario.</p>';
            return;
        }
        container.innerHTML = melgas.map(m => `
            <div class="melga-card">
                <h3>${escapeHtml(m.nombre)}</h3>
                <p>📍 ${escapeHtml(m.ubicacion || 'Sin ubicación')}</p>
                <p>🌲 ${m.total_arboles_real || 0} árboles | 🍄 ${m.productores || 0} productores</p>
                <div class="flex">
                    <button class="btn-sm" onclick="verMelga(${m.id})">Ver árboles</button>
                    <button class="btn-sm" onclick="eliminarMelga(${m.id})">Eliminar</button>
                </div>
            </div>
        `).join('');
    }

    async function crearMelga(nombre, numArboles, ubicacion) {
        const data = await apiFetch('api/melgas.php', {
            method: 'POST',
            body: JSON.stringify({ nombre, num_arboles: numArboles, ubicacion })
        });
        if (data.success) {
            mostrarMensaje(`Melga "${nombre}" creada con ${numArboles} árboles.`);
            cargarMelgas();
            cargarEstadisticas();
        } else {
            throw new Error(data.error);
        }
    }

    async function eliminarMelga(id) {
        if (!confirm('¿Eliminar esta melga? Se perderán todos sus árboles y cosechas.')) return;
        await apiFetch(`api/melgas.php?id=${id}`, { method: 'DELETE' });
        mostrarMensaje('Melga eliminada');
        cargarMelgas();
        cargarEstadisticas();
        if (melgaIdAbierta === id) cerrarDetalle();
    }

    // Árboles de una melga
    async function verMelga(melgaId) {
        melgaIdAbierta = melgaId;
        const melga = melgas.find(m => m.id === melgaId);
        if (!melga) return;
        document.getElementById('detalleTitulo').innerHTML = `🌳 Árboles de ${escapeHtml(melga.nombre)}`;
        const arbolesData = await apiFetch(`api/arboles.php?melga_id=${melgaId}`);
        arboles = arbolesData;
        let html = `<table><thead><tr><th>Código</th><th>Produce</th><th>Inicio prod.</th><th>Estado sanitario</th><th>Última rev.</th><th>Acciones</th></tr></thead><tbody>`;
        for (const a of arboles) {
            html += `<tr>
                <td>${escapeHtml(a.codigo)}</td>
                <td><input type="checkbox" ${a.en_produccion ? 'checked' : ''} onchange="toggleProduccion(${a.id}, this.checked)"></td>
                <td><input type="date" value="${a.fecha_inicio_produccion || ''}" onchange="actualizarArbol(${a.id}, 'fecha_inicio_produccion', this.value)" style="width:120px"></td>
                <td><select onchange="actualizarArbol(${a.id}, 'estado_sanitario', this.value)">
                    <option ${a.estado_sanitario === 'Bueno' ? 'selected' : ''}>Bueno</option>
                    <option ${a.estado_sanitario === 'Regular' ? 'selected' : ''}>Regular</option>
                    <option ${a.estado_sanitario === 'Malo' ? 'selected' : ''}>Malo</option>
                </select></td>
                <td><input type="date" value="${a.ultima_revision || ''}" onchange="actualizarArbol(${a.id}, 'ultima_revision', this.value)" style="width:120px"></td>
                <td><button class="btn-sm" onclick="mostrarDetalleArbol(${a.id})">📊 Historial</button> <button class="btn-sm" onclick="abrirRegistroCosecha(${a.id})">➕ Cosecha</button></td>
            </div>`;
        }
        html += `</tbody></table>`;
        document.getElementById('arbolesLista').innerHTML = html;
        document.getElementById('detalleMelga').style.display = 'block';
        document.getElementById('detalleArbol').style.display = 'none';
        window.scrollTo({top: document.getElementById('detalleMelga').offsetTop - 20, behavior: 'smooth'});
    }

    async function toggleProduccion(arbolId, checked) {
        await actualizarArbol(arbolId, 'en_produccion', checked ? 1 : 0);
    }

    async function actualizarArbol(arbolId, campo, valor) {
        await apiFetch('api/arboles.php', {
            method: 'PUT',
            body: JSON.stringify({ arbol_id: arbolId, campo, valor })
        });
        // refrescar la vista actual si es la melga abierta
        if (melgaIdAbierta) verMelga(melgaIdAbierta);
        cargarEstadisticas();
    }

    // Detalle de árbol (historial y gráfico)
    async function mostrarDetalleArbol(arbolId) {
        arbolIdAbierto = arbolId;
        const arbol = arboles.find(a => a.id === arbolId);
        if (!arbol) return;
        const melga = melgas.find(m => m.id === melgaIdAbierta);
        document.getElementById('arbolDetalleTitulo').innerHTML = `🍄 Árbol ${escapeHtml(arbol.codigo)} - ${escapeHtml(melga?.nombre || '')}`;
        // Obtener cosechas del árbol desde la API de cosechas con filtro
        const todas = await apiFetch('api/cosechas.php');
        const cosechasArbol = todas.filter(c => c.arbol_id === arbolId).sort((a,b)=>a.fecha.localeCompare(b.fecha));
        let html = `<td><thead><tr><th>Fecha</th><th>N° trufas</th><th>Kg</th><th>Kg/trufa</th><th>Calidad</th><th>Observaciones</th><th></th></tr></thead><tbody>`;
        for (const c of cosechasArbol) {
            const kgPor = (c.kg / c.cantidad_trufas).toFixed(2);
            html += `<tr>
                <td>${c.fecha}</td>
                <td>${c.cantidad_trufas}</td>
                <td>${c.kg}</td>
                <td>${kgPor}</td>
                <td>${c.calidad || 'Estándar'}</td>
                <td>${escapeHtml(c.observaciones || '')}</td>
                <td><button class="btn-sm" onclick="editarCosecha(${c.id})">✏️</button> <button class="btn-sm" onclick="eliminarCosecha(${c.id})">🗑️</button></td>
            </div>`;
        }
        html += `</tbody></table><button class="btn-sm" onclick="abrirRegistroCosecha(${arbolId})">➕ Nueva cosecha</button>`;
        document.getElementById('cosechasArbolLista').innerHTML = html;

        // Gráfico
        const ctx = document.getElementById('arbolChart').getContext('2d');
        if (chartInstance) chartInstance.destroy();
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: cosechasArbol.map(c => c.fecha),
                datasets: [
                    { label: 'Kg', data: cosechasArbol.map(c => c.kg), backgroundColor: '#7a4c2c' },
                    { label: 'N° trufas', data: cosechasArbol.map(c => c.cantidad_trufas), backgroundColor: '#b88b5a' }
                ]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
        document.getElementById('detalleArbol').style.display = 'block';
        document.getElementById('detalleMelga').style.display = 'none';
        window.scrollTo({top: document.getElementById('detalleArbol').offsetTop - 20, behavior: 'smooth'});
    }

    function cerrarDetalle() {
        document.getElementById('detalleMelga').style.display = 'none';
        melgaIdAbierta = null;
    }
    function cerrarDetalleArbol() {
        document.getElementById('detalleArbol').style.display = 'none';
        arbolIdAbierto = null;
    }

    // CRUD cosechas
    async function registrarCosecha(arbolId, fecha, cantidadTrufas, kg, calidad, observaciones) {
        await apiFetch('api/cosechas.php', {
            method: 'POST',
            body: JSON.stringify({ arbol_id: arbolId, fecha, cantidad_trufas: cantidadTrufas, kg, calidad, observaciones })
        });
        mostrarMensaje('Cosecha registrada');
        if (arbolIdAbierto === arbolId) mostrarDetalleArbol(arbolId);
        if (melgaIdAbierta) verMelga(melgaIdAbierta);
        cargarEstadisticas();
        cargarHistorial();
    }

    async function editarCosecha(id) {
        const cosecha = (await apiFetch('api/cosechas.php')).find(c => c.id === id);
        if (!cosecha) return;
        const nuevaFecha = prompt("Nueva fecha (YYYY-MM-DD):", cosecha.fecha);
        const nuevasTrufas = parseInt(prompt("Número de trufas:", cosecha.cantidad_trufas));
        const nuevoKg = parseFloat(prompt("Kg:", cosecha.kg));
        const nuevaCalidad = prompt("Calidad (Extra, Primera, Segunda, Estándar):", cosecha.calidad);
        const nuevasObs = prompt("Observaciones:", cosecha.observaciones);
        if (nuevaFecha && !isNaN(nuevasTrufas) && !isNaN(nuevoKg)) {
            await apiFetch('api/cosechas.php', {
                method: 'PUT',
                body: JSON.stringify({ id, fecha: nuevaFecha, cantidad_trufas: nuevasTrufas, kg: nuevoKg, calidad: nuevaCalidad, observaciones: nuevasObs })
            });
            mostrarMensaje('Cosecha actualizada');
            if (arbolIdAbierto === cosecha.arbol_id) mostrarDetalleArbol(arbolIdAbierto);
            if (melgaIdAbierta) verMelga(melgaIdAbierta);
            cargarHistorial();
            cargarEstadisticas();
        }
    }

    async function eliminarCosecha(id) {
        if (!confirm('¿Eliminar esta cosecha?')) return;
        await apiFetch(`api/cosechas.php?id=${id}`, { method: 'DELETE' });
        mostrarMensaje('Cosecha eliminada');
        if (arbolIdAbierto) mostrarDetalleArbol(arbolIdAbierto);
        if (melgaIdAbierta) verMelga(melgaIdAbierta);
        cargarHistorial();
        cargarEstadisticas();
    }

    function abrirRegistroCosecha(arbolId) {
        const fecha = prompt("Fecha (YYYY-MM-DD):", new Date().toISOString().slice(0,10));
        if (!fecha) return;
        const cantidad = parseInt(prompt("Número de trufas:", ""));
        if (isNaN(cantidad) || cantidad <= 0) return alert("Cantidad inválida");
        const kg = parseFloat(prompt("Peso total (kg):", ""));
        if (isNaN(kg) || kg <= 0) return alert("Peso inválido");
        const calidad = prompt("Calidad (Extra, Primera, Segunda, Estándar):", "Estándar");
        const obs = prompt("Observaciones:", "");
        registrarCosecha(arbolId, fecha, cantidad, kg, calidad, obs);
    }

    // Historial global con filtros
    async function cargarHistorial() {
        const melgaId = document.getElementById('filtroMelgaHistorial').value;
        const fechaInicio = document.getElementById('filtroFechaInicio').value;
        const fechaFin = document.getElementById('filtroFechaFin').value;
        const mesesInactivos = document.getElementById('filtroInactivos').value;
        let url = 'api/cosechas.php?';
        if (melgaId && melgaId !== '0') url += `melga_id=${melgaId}&`;
        if (fechaInicio) url += `fecha_inicio=${fechaInicio}&`;
        if (fechaFin) url += `fecha_fin=${fechaFin}&`;
        if (mesesInactivos) url += `meses_inactivos=${mesesInactivos}`;
        cosechasGlobal = await apiFetch(url);
        renderizarHistorial();
    }

    function renderizarHistorial() {
        const tbody = document.getElementById('historialBody');
        if (!cosechasGlobal.length) {
            tbody.innerHTML = '<tr><td colspan="9">No hay registros de cosecha</td></tr>';
            return;
        }
        tbody.innerHTML = cosechasGlobal.map(c => {
            const kgPor = (c.kg / c.cantidad_trufas).toFixed(2);
            return `<tr>
                <td>${c.fecha}</td>
                <td>${escapeHtml(c.melga_nombre)}</td>
                <td>${escapeHtml(c.arbol_codigo)}</td>
                <td>${c.cantidad_trufas}</td>
                <td>${c.kg}</td>
                <td>${kgPor}</td>
                <td>${c.calidad || 'Estándar'}</td>
                <td>${escapeHtml(c.observaciones || '')}</td>
                <td><button class="btn-sm" onclick="editarCosecha(${c.id})">✏️</button> <button class="btn-sm" onclick="eliminarCosecha(${c.id})">🗑️</button></td>
            </div>`;
        }).join('');
    }

    // Exportar CSV
    function exportarCSV() {
        window.location.href = 'api/exportar.php';
    }

    // Respaldo (solo exporta cosechas en JSON)
    async function respaldarJSON() {
        const data = await apiFetch('api/cosechas.php');
        const blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `trufa_backup_${new Date().toISOString().slice(0,10)}.json`;
        a.click();
    }

    // Inicialización completa
    async function cargarDatosIniciales() {
        await cargarEstadisticas();
        await cargarMelgas();
        await cargarHistorial();
    }

    // Eventos y funciones globales
    window.verMelga = verMelga;
    window.eliminarMelga = eliminarMelga;
    window.toggleProduccion = toggleProduccion;
    window.actualizarArbol = actualizarArbol;
    window.mostrarDetalleArbol = mostrarDetalleArbol;
    window.cerrarDetalleArbol = cerrarDetalleArbol;
    window.editarCosecha = editarCosecha;
    window.eliminarCosecha = eliminarCosecha;
    window.abrirRegistroCosecha = abrirRegistroCosecha;

    document.getElementById('doLogin').addEventListener('click', async () => {
        const email = document.getElementById('loginEmail').value.trim();
        const pass = document.getElementById('loginPassword').value;
        try {
            await login(email, pass);
        } catch (err) {
            alert('Error: ' + err.message);
        }
    });
    document.getElementById('logoutBtn').addEventListener('click', () => logout());
    document.getElementById('crearMelgaBtn').addEventListener('click', async () => {
        const nombre = document.getElementById('nuevaMelgaNombre').value.trim();
        const num = parseInt(document.getElementById('nuevaMelgaArboles').value);
        const ubic = document.getElementById('nuevaMelgaUbicacion').value.trim();
        if (!nombre || num < 1) return alert('Nombre y número válidos');
        try {
            await crearMelga(nombre, num, ubic);
            document.getElementById('nuevaMelgaNombre').value = '';
            document.getElementById('nuevaMelgaUbicacion').value = '';
        } catch (err) {
            alert(err.message);
        }
    });
    document.getElementById('cerrarDetalle').addEventListener('click', cerrarDetalle);
    document.getElementById('cerrarDetalleArbol').addEventListener('click', cerrarDetalleArbol);
    document.getElementById('aplicarFiltros').addEventListener('click', () => cargarHistorial());
    document.getElementById('exportarCSV').addEventListener('click', exportarCSV);
    document.getElementById('respaldarDatos').addEventListener('click', respaldarJSON);

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, m => m === '&' ? '&amp;' : m === '<' ? '&lt;' : '&gt;');
    }
</script>
</body>
</html>