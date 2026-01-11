<?php
// Ultra-simple test - no Joomla dependencies
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple File Test</h1>";
echo "<p>If you see this, PHP is working and files are accessible!</p>";
echo "<p>Current file: " . __FILE__ . "</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";

// List files in current directory
echo "<h2>Files in admin directory:</h2>";
echo "<ul>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $path = __DIR__ . '/' . $file;
        $type = is_dir($path) ? '[DIR]' : '[FILE]';
        echo "<li>$type $file</li>";
    }
}
echo "</ul>";

// Check for simon.php
$simonPhp = __DIR__ . '/simon.php';
echo "<h2>Entry Point Check:</h2>";
if (file_exists($simonPhp)) {
    echo "<p style='color:green;'>✓ simon.php EXISTS at: $simonPhp</p>";
    echo "<p>File size: " . filesize($simonPhp) . " bytes</p>";
    echo "<p>File readable: " . (is_readable($simonPhp) ? "YES" : "NO") . "</p>";
} else {
    echo "<p style='color:red;'>✗ simon.php NOT FOUND at: $simonPhp</p>";
}

// Check component root
$componentRoot = dirname(__DIR__);
echo "<h2>Component Root:</h2>";
echo "<p>Component root: $componentRoot</p>";

$manifest = $componentRoot . '/simon.xml';
if (file_exists($manifest)) {
    echo "<p style='color:green;'>✓ simon.xml EXISTS</p>";
} else {
    echo "<p style='color:red;'>✗ simon.xml NOT FOUND</p>";
}

