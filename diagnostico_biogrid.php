<?php
/**
 * SCRIPT DE DIAGNÓSTICO DE DATOS - BIO GRID QA
 * Propósito: Verificar la visibilidad de datos ignorando los filtros del Dashboard.
 */

// 1. Forzar visualización de errores para el Ingeniero
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db/db_connect.php';
session_start();

echo "<h2>🛠️ Diagnóstico de Datos: BioGrid QA</h2>";

// --- PRUEBA 1: Conexión y Existencia de Datos Raw ---
try {
    // Usamos el nombre de tabla identificado en su volcado SQL
    $check = $conn->query("SELECT COUNT(*) as total FROM inv_elementos");
    $total = $check->fetchColumn();
    echo "✅ <b>Conexión Exitosa:</b> Se encontraron <b>$total</b> registros en la tabla <code>inv_elementos</code>.<br>";
} catch (Exception $e) {
    die("❌ <b>Error de SQL:</b> " . $e->getMessage());
}

// --- PRUEBA 2: Estado de la Sesión (El Filtro) ---
echo "<h3>🔍 Estado de su Sesión:</h3>";
if (empty($_SESSION)) {
    echo "⚠️ <b>Aviso:</b> No hay una sesión activa. El dashboard no mostrará nada si requiere <code>empresa_id</code>.<br>";
} else {
    echo "ID de Usuario en sesión: " . ($_SESSION['usuario_id'] ?? 'No definido') . "<br>";
    echo "ID de Empresa en sesión: " . ($_SESSION['empresa_id'] ?? 'No definido') . "<br>";
}

// --- PRUEBA 3: El "Cruce" de Datos ---
if (isset($_SESSION['empresa_id'])) {
    $id_empresa = $_SESSION['empresa_id'];
    $stmt = $conn->prepare("SELECT COUNT(*) FROM inv_elementos WHERE empresa_id = ?");
    $stmt->execute([$id_empresa]);
    $filtrados = $stmt->fetchColumn();

    echo "<h3>📊 Resultado del Filtro:</h3>";
    echo "Para la Empresa ID <b>$id_empresa</b>, el sistema encuentra: <b>$filtrados</b> activos.<br>";

    if ($filtrados == 0 && $total > 0) {
        echo "<p style='color:red;'>🚨 <b>¡DIAGNÓSTICO FINAL!:</b> Los datos existen, pero no pertenecen a la empresa asignada a su usuario en esta base de datos de QA.</p>";
    } else {
        echo "<p style='color:green;'>✅ El filtro de sesión es correcto. Si no ve datos, el problema es el caché del navegador o de Hostinger.</p>";
    }
}
?>