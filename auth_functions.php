<?php
/**
 * PROYECTO: BioGrid - Gestión de Activos
 * AUTOR: Diego Palacios
 * FECHA: 31/03/2026
 * DESCRIPCIÓN: Controlador central de autenticación (Login/Logout).
 * Gestiona el acceso por email/usuario y destruye la sesión de forma segura.
 */

// 1. Carga de conexión y sesión
require_once 'includes/db/db_connect.php'; 
session_start();

$action = $_GET['action'] ?? '';

// --- BLOQUE 1: INICIO DE SESIÓN (LOGIN) ---
if ($action == 'login') {
    $user_in = $_POST['usuario'] ?? '';
    $pass_in = $_POST['password'] ?? '';

    // Buscamos al usuario (Email o Usuario) y traemos su Rol y Empresa
    $stmt = $pdo->prepare("SELECT u.*, r.nombre_rol, e.nombre as empresa_nombre 
                           FROM sys_usuarios u
                           JOIN sys_roles r ON u.rol_id = r.id
                           JOIN sys_empresas e ON u.empresa_id = e.id
                           WHERE (u.email = ? OR u.usuario = ?) AND u.estado = 1");
    $stmt->execute([$user_in, $user_in]);
    $user = $stmt->fetch();

    // Verificación: Soporta texto plano (para pruebas) y Password Hash (producción)
    if ($user && ($pass_in === $user['password_hash'] || password_verify($pass_in, $user['password_hash']))) {
        $_SESSION['usuario_id']   = $user['id'];
        $_SESSION['nombre']       = $user['nombre'];
        $_SESSION['rol_id']       = $user['rol_id'];
        $_SESSION['rol_nombre']   = $user['nombre_rol'];
        $_SESSION['empresa_id']   = $user['empresa_id'];
        $_SESSION['empresa_nom']  = $user['empresa_nombre'];
        
        header("Location: index.php");
        exit();
    } else {
        header("Location: login.php?error=1");
        exit();
    }
}

// --- BLOQUE 2: CIERRE DE SESIÓN (LOGOUT) ---
if ($action == 'logout') {
    // Limpiar el array de sesión
    $_SESSION = array();

    // Destruir la cookie de sesión en el navegador
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destruir la sesión en el servidor
    session_destroy();

    // Redirigir al login
    header("Location: login.php?status=logged_out");
    exit();
}