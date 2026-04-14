<?php
require_once 'includes/db/db_connect.php';
session_start();

// Si quieres probar 10,000 de una vez, cambia este número (pero ve de 1000 en 1000)
$cantidad = 1000; 
$empresa_id = $_SESSION['empresa_id'] ?? 1;
$municipio_id = 41001; // Neiva
$usuario_id = $_SESSION['usuario_id'] ?? 1;

try {
    $pdo->beginTransaction();
    $sql = "INSERT INTO inv_elementos (empresa_id, municipio_id, tipo_elemento, posicion_gps, detalles_tecnicos, creado_por) 
            VALUES (?, ?, ?, ST_GeomFromText(?, 4326), ?, ?)";
    $stmt = $pdo->prepare($sql);

    for ($i = 0; $i < $cantidad; $i++) {
        // Coordenadas aproximadas de Neiva (Lat: 2.92, Lng: -75.28)
        // Generamos una variación pequeña para dispersar los puntos
        $lat = 2.9273 + (mt_rand(-1500, 1500) / 100000);
        $lng = -75.2819 + (mt_rand(-1500, 1500) / 100000);
        
        $tipo = ($i % 2 == 0) ? 'LUMINARIA' : 'ARBOL';
        $detalle = ($tipo == 'LUMINARIA') ? "Sodio " . mt_rand(50, 150) . "W - Test" : "Especie Nativa - Test " . $i;
        
        $wkt = "POINT($lat $lng)";
        $stmt->execute([$empresa_id, $municipio_id, $tipo, $wkt, $detalle, $usuario_id]);
    }
    
    $pdo->commit();
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
    echo "<h2>✅ ¡Carga de Prueba Exitosa!</h2>";
    echo "<p>Se han insertado <strong>$cantidad</strong> puntos nuevos en Neiva.</p>";
    echo "<a href='index.php?mod=mapa&f_muni=41001' style='background:#27ae60; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Ver en el Mapa</a>";
    echo "</div>";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "❌ Error en la carga masiva: " . $e->getMessage();
}