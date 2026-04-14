<?php 
session_start();
$_SESSION['usuario_id'] = 1;
$_SESSION['nombre'] = "Diego Admin";
header("Location: index.php");
exit();
?>