<?php
echo "<h1>¡El servidor PHP funciona!</h1>";
echo "<p>Ruta actual en el servidor: " . __DIR__ . "</p>";
echo "<p>Versión de PHP: " . phpversion() . "</p>";
echo "<hr>";
echo "<h2>Prueba de Laravel Index:</h2>";
if (file_exists(__DIR__ . '/index.php')) {
    echo "<p>✅ El archivo index.php EXISTE en esta carpeta.</p>";
} else {
    echo "<p>❌ El archivo index.php NO EXISTE en esta carpeta.</p>";
}
