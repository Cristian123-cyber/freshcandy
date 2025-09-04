<?php
// Segundo test simple de variables de entorno
// Accede a: http://localhost/test_env2.php

echo "<h1>Test de Variables de Entorno #2</h1>";
echo "<p>Verificando configuración desde archivo .env</p>";

echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>";
echo "<tr style='background-color: #f0f0f0;'><th>Variable</th><th>Valor</th><th>Status</th></tr>";

// Lista de variables a verificar
$envVars = [
    'DB_HOST' => 'Host de BD',
    'DB_NAME' => 'Nombre BD',
    'DB_USER' => 'Usuario BD',
    'DB_PASSWORD' => 'Password BD',
    'APP_ENV' => 'Ambiente',
    'TZ' => 'Zona Horaria'
];

$todoOk = true;

foreach ($envVars as $var => $description) {
    $value = getenv($var);
    $hasValue = $value !== false;
    
    if (!$hasValue) {
        $todoOk = false;
    }
    
    // Ocultar password por seguridad
    $displayValue = $hasValue ? 
        ($var === 'DB_PASSWORD' ? '***' : $value) : 
        '<span style="color: red;">NO DEFINIDA</span>';
    
    $status = $hasValue ? 
        '<span style="color: green;">✓ OK</span>' : 
        '<span style="color: red;">✗ FALTA</span>';
    
    echo "<tr>";
    echo "<td><strong>$var</strong><br><small>$description</small></td>";
    echo "<td>$displayValue</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}

echo "</table>";

// Resumen
echo "<h2>Resumen:</h2>";
if ($todoOk) {
    echo "<p style='color: green; font-weight: bold;'>✅ Todas las variables están configuradas</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Faltan algunas variables</p>";
}

// Info adicional
echo "<h3>Info Adicional:</h3>";
echo "<ul>";
echo "<li><strong>Función getenv disponible:</strong> " . (function_exists('getenv') ? 'SÍ' : 'NO') . "</li>";
echo "<li><strong>PHP Version:</strong> " . phpversion() . "</li>";
echo "<li><strong>Fecha/Hora actual:</strong> " . date('Y-m-d H:i:s') . "</li>";
echo "</ul>";

// Test rápido de conexión
echo "<h3>Test de Conexión Rápido:</h3>";
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

if ($host && $user && $dbname) {
    $conexion = @mysqli_connect($host, $user, $pass, $dbname);
    if ($conexion) {
        echo "<p style='color: green;'>✅ Conexión a MySQL exitosa</p>";
        mysqli_close($conexion);
    } else {
        echo "<p style='color: red;'>❌ Error de conexión: " . mysqli_connect_error() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ No se puede probar conexión - faltan datos</p>";
}

echo "<br><br>";
echo "<em style='color: red;'>🗑️ Recuerda eliminar este archivo después de probar</em>";
?>