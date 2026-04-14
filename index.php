<?php
/**
 * PROYECTO: BioGrid - Gestión de Activos
 * DESCRIPCIÓN: index.php Full - Botón de salida restaurado y CSS blindado.
 */
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit(); }
require_once 'includes/db/db_connect.php';

$rol_id = $_SESSION['rol_id'] ?? 3; 
$modulo = $_GET['mod'] ?? 'mapa';

$rutas = [
    'mapa'           => ['archivo' => 'modules/inventario/mapa.php',           'titulo' => 'Visor Geográfico',   'icono' => 'fa-map-marked-alt', 'min_rol' => 3],
    'censo_off'      => ['archivo' => 'modules/inventario/nuevo_offline.php',  'titulo' => 'Censo Offline',      'icono' => 'fa-wifi',            'min_rol' => 3],
    'censo'          => ['archivo' => 'modules/inventario/nuevo.php',          'titulo' => 'Realizar Censo',     'icono' => 'fa-camera',          'min_rol' => 2],
    'importar'       => ['archivo' => 'modules/inventario/importar.php',       'titulo' => 'Importación Masiva', 'icono' => 'fa-file-import',    'min_rol' => 2],
    'reportes'       => ['archivo' => 'modules/inventario/reportes.php',       'titulo' => 'Reportes de Vatios', 'icono' => 'fa-chart-pie',      'min_rol' => 3],
    'm_lumi'         => ['archivo' => 'modules/maestros/lumi_lista.php',       'titulo' => 'Maestro Luminarias', 'icono' => 'fa-lightbulb',      'min_rol' => 1],
    'm_arbol'        => ['archivo' => 'modules/maestros/arbol_lista.php',      'titulo' => 'Maestro Árboles',    'icono' => 'fa-tree',           'min_rol' => 1],
    'usuarios'       => ['archivo' => 'modules/usuarios/listar_usuarios.php', 'titulo' => 'Gestión de Usuarios', 'icono' => 'fa-users',          'min_rol' => 1],
    'deptos'         => ['archivo' => 'modules/config/departamentos.php',      'titulo' => 'Departamentos',       'icono' => 'fa-globe-americas', 'min_rol' => 1],
    'munis'          => ['archivo' => 'modules/config/municipios.php',         'titulo' => 'Municipios',          'icono' => 'fa-map-signs',      'min_rol' => 1]
];

if (!isset($rutas[$modulo]) || $rol_id > $rutas[$modulo]['min_rol']) { $modulo = 'mapa'; }
$config_actual = $rutas[$modulo];
$archivo_carga = $config_actual['archivo'];

function is_active($mod_actual, $target) { return ($mod_actual === $target) ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>BioGrid | LuminaTree</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
    :root {
        --primary: #10b981;
        --dark: #0f172a;
        --sidebar-bg: #1e293b;
        --sidebar-width: 240px;
        --topbar-height: 50px;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #f1f5f9;
        margin: 0;
        overflow: hidden;
    }

    .dashboard-wrapper {
        display: flex;
        width: 100vw;
        height: 100vh;
    }

    /* Sidebar como contenedor flex para empujar el botón al fondo */
    .sidebar {
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        color: white;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        z-index: 100;
        transition: 0.3s;
    }

    .dashboard-wrapper.collapsed .sidebar {
        margin-left: -240px;
    }

    .sidebar-header {
        padding: 15px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .brand-name {
        font-weight: 800;
        font-size: 1.2rem;
        color: white;
        text-decoration: none;
    }

    .brand-name span {
        color: var(--primary);
    }

    /* El menú crece para ocupar el espacio y empujar lo demás */
    .menu {
        padding: 10px 0;
        flex-grow: 1;
        overflow-y: auto;
    }

    .menu-label {
        padding: 15px 20px 5px;
        font-size: 0.65rem;
        color: #64748b;
        font-weight: 800;
        text-transform: uppercase;
    }

    .menu a {
        display: flex;
        align-items: center;
        padding: 10px 22px;
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.85rem;
        gap: 12px;
    }

    .menu a.active {
        background: var(--primary);
        color: white;
        font-weight: 600;
        border-left: 4px solid white;
    }

    /* Estilos del botón de salida */
    .btn-logout-box {
        padding: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .btn-logout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px;
        border-radius: 10px;
        color: #f87171;
        text-decoration: none;
        background: rgba(239, 68, 68, 0.1);
        font-size: 0.85rem;
        font-weight: 800;
        transition: 0.2s;
    }

    .btn-logout:hover {
        background: rgba(239, 68, 68, 0.2);
        transform: scale(0.98);
    }

    .content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .topbar {
        background: white;
        height: var(--topbar-height);
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
    }

    .module-container {
        padding: 15px;
        flex-grow: 1;
        overflow-y: auto;
        background: #f8fafc;
    }
    </style>
</head>

<body>
    <div class="dashboard-wrapper" id="mainWrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="brand-name">BioGrid<span>.</span></div>
            </div>
            <nav class="menu">
                <p class="menu-label">Inventario</p>
                <a href="?mod=mapa" class="<?= is_active($modulo, 'mapa') ?>"><i class="fa fa-map-marked-alt"></i>
                    <span>Visor Geográfico</span></a>
                <a href="?mod=censo_off" class="<?= is_active($modulo, 'censo_off') ?>"><i class="fa fa-wifi"></i>
                    <span>Censo Offline</span></a>
                <?php if ($rol_id <= 2): ?>
                <a href="?mod=censo" class="<?= is_active($modulo, 'censo') ?>"><i class="fa fa-camera"></i>
                    <span>Realizar Censo</span></a>
                <a href="?mod=importar" class="<?= is_active($modulo, 'importar') ?>"><i class="fa fa-file-import"></i>
                    <span>Importación Masiva</span></a>
                <?php endif; ?>

                <p class="menu-label">Análisis</p>
                <a href="?mod=reportes" class="<?= is_active($modulo, 'reportes') ?>"><i class="fa fa-chart-pie"></i>
                    <span>Reportes</span></a>

                <?php if ($rol_id == 1): ?>
                <p class="menu-label">Maestros</p>
                <a href="?mod=m_lumi" class="<?= is_active($modulo, 'm_lumi') ?>"><i class="fa fa-lightbulb"></i>
                    <span>Luminarias</span></a>
                <a href="?mod=m_arbol" class="<?= is_active($modulo, 'm_arbol') ?>"><i class="fa fa-tree"></i>
                    <span>Árboles</span></a>

                <p class="menu-label">Configuración</p>
                <a href="?mod=usuarios" class="<?= is_active($modulo, 'usuarios') ?>"><i class="fa fa-users"></i>
                    <span>Usuarios</span></a>
                <a href="?mod=deptos" class="<?= is_active($modulo, 'deptos') ?>"><i class="fa fa-globe-americas"></i>
                    <span>Departamentos</span></a>
                <a href="?mod=munis" class="<?= is_active($modulo, 'munis') ?>"><i class="fa fa-map-signs"></i>
                    <span>Municipios</span></a>
                <?php endif; ?>
            </nav>

            <div class="btn-logout-box">
                <a href="auth_functions.php?action=logout" class="btn-logout">
                    <i class="fa fa-power-off"></i> <span>Cerrar Sesión</span>
                </a>
            </div>
        </aside>

        <main class="content">
            <header class="topbar">
                <button onclick="toggleSidebar()"
                    style="border:none; background:#f1f5f9; padding:8px; border-radius:6px; cursor:pointer;"><i
                        class="fa fa-bars"></i></button>
                <div class="breadcrumb" style="font-size:0.85rem; color:#64748b;">BioGrid /
                    <strong><?= $config_actual['titulo'] ?></strong>
                </div>
            </header>
            <section class="module-container">
                <?php include $archivo_carga; ?>
            </section>
        </main>
    </div>

    <script>
    function toggleSidebar() {
        document.getElementById('mainWrapper').classList.toggle('collapsed');
        setTimeout(() => {
            if (window.mapOff) window.mapOff.invalidateSize();
        }, 400);
    }
    </script>
</body>

</html>