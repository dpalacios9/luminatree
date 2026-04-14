<?php
require_once 'includes/db/db_connect.php';

$email = 'diego@electrohuila.com';
$pass = 'admin123';
$hash = password_hash($pass, PASSWORD_DEFAULT);

try {
    // 1. Limpiamos cualquier rastro del usuario para evitar conflictos
    $pdo->prepare("DELETE FROM sys_usuarios WHERE email = ?")->execute([$email]);

    // 2. Insertamos el usuario con el hash fresco
    $sql = "INSERT INTO sys_usuarios (empresa_id, rol_id, nombre, email, password_hash, estado) 
            VALUES (1, 1, 'Diego Admin', ?, ?, 1)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $hash]);

    echo "✅ Usuario restaurado correctamente.<br>";
    echo "Email: <b>$email</b><br>";
    echo "Clave: <b>$pass</b><br>";
    echo "Hash generado: <small>$hash</small><br><br>";
    echo "<a href='login.php'>Ir al Login</a>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>