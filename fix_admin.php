<?php
require_once 'includes/db/db_connect.php';

$email = 'diego@electrohuila.com';
$password_plana = 'admin123'; // La clave que tú quieras usar
$hash_seguro = password_hash($password_plana, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE sys_usuarios SET password_hash = ? WHERE email = ?");
    $stmt->execute([$hash_seguro, $email]);
    echo "¡Listo! El usuario $email ahora tiene la clave '$password_plana' cifrada correctamente.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>