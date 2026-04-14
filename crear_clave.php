<?php
require_once 'includes/db/db_connect.php';

// Generamos el hash exacto que tu servidor entiende para 'admin123'
$nueva_clave = password_hash('admin123', PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE sys_usuarios SET password_hash = ? WHERE email = 'diego@electrohuila.com'");
    $stmt->execute([$nueva_clave]);
    echo "¡Éxito! La clave ha sido actualizada con el hash: " . $nueva_clave;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>