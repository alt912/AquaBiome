<?php
echo "<h1>Diagnostic PHP</h1>";
echo "Version PHP : " . phpversion() . "<br>";
echo "Interface SAPI : " . php_sapi_name() . "<br>";
echo "Dossier courant : " . __DIR__ . "<br>";

if (version_compare(phpversion(), '8.4.0', '<')) {
    echo "<p style='color:red'>⚠️ ERREUR : Ta version de PHP est trop ancienne pour Symfony 8 (besoin de 8.4+).</p>";
} else {
    echo "<p style='color:green'>✅ Version PHP OK.</p>";
}

echo "<h2>Extensions requises :</h2>";
$exts = ['ctype', 'iconv', 'pdo_mysql', 'intl', 'mbstring'];
foreach ($exts as $ext) {
    echo "$ext : " . (extension_loaded($ext) ? "✅ OK" : "❌ MANQUANTE") . "<br>";
}
