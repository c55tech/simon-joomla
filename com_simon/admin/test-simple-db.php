<?php
/**
 * Simple diagnostic that checks files and database without full Joomla bootstrap
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>SIMON Component Diagnostic (Simple)</h1>";
echo "<p>PHP is working!</p>";

// Find Joomla root
$joomlaRoot = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
echo "<p>Joomla root: " . $joomlaRoot . "</p>";

// Check configuration file to get database credentials
$configFile = $joomlaRoot . '/configuration.php';
if (file_exists($configFile)) {
    echo "<p style='color:green;'>✓ Configuration file found</p>";
    
    // Read configuration
    $configContent = file_get_contents($configFile);
    
    // Extract database settings (simple regex - not perfect but works)
    preg_match("/public\s+\$host\s*=\s*['\"]([^'\"]+)['\"]/", $configContent, $hostMatch);
    preg_match("/public\s+\$user\s*=\s*['\"]([^'\"]+)['\"]/", $configContent, $userMatch);
    preg_match("/public\s+\$password\s*=\s*['\"]([^'\"]+)['\"]/", $configContent, $passMatch);
    preg_match("/public\s+\$db\s*=\s*['\"]([^'\"]+)['\"]/", $configContent, $dbMatch);
    preg_match("/public\s+\$dbprefix\s*=\s*['\"]([^'\"]+)['\"]/", $configContent, $prefixMatch);
    
    $dbHost = $hostMatch[1] ?? 'localhost';
    $dbUser = $userMatch[1] ?? '';
    $dbPass = $passMatch[1] ?? '';
    $dbName = $dbMatch[1] ?? '';
    $dbPrefix = $prefixMatch[1] ?? 'jos_';
    
    echo "<p>Database: " . $dbName . " on " . $dbHost . "</p>";
    
    // Connect to database directly
    try {
        $db = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        
        if ($db->connect_error) {
            echo "<p style='color:red;'>✗ Database connection failed: " . $db->connect_error . "</p>";
        } else {
            echo "<p style='color:green;'>✓ Database connected</p>";
            
            // Check if component is registered
            $tableName = $dbPrefix . 'extensions';
            $query = "SELECT * FROM `" . $db->real_escape_string($tableName) . "` 
                      WHERE `element` = 'com_simon' AND `type` = 'component'";
            
            $result = $db->query($query);
            
            if ($result && $result->num_rows > 0) {
                $component = $result->fetch_assoc();
                echo "<p><strong style='color:green;'>✓ Component is registered in database</strong></p>";
                echo "<p>Component ID: " . $component['extension_id'] . "</p>";
                echo "<p>Component enabled: " . ($component['enabled'] ? "<strong style='color:green;'>YES ✓</strong>" : "<strong style='color:red;'>NO ✗ - NEEDS TO BE ENABLED</strong>") . "</p>";
                
                $manifest = json_decode($component['manifest_cache'], true);
                if ($manifest) {
                    echo "<p>Component version: " . ($manifest['version'] ?? "Unknown") . "</p>";
                }
            } else {
                echo "<p><strong style='color:red;'>✗ Component is NOT registered in database</strong></p>";
                echo "<p><strong>ACTION NEEDED: Go to Extensions → Manage → Discover and click Discover</strong></p>";
            }
            
            $db->close();
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>✗ Database error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red;'>✗ Configuration file not found</p>";
}

echo "<hr>";
echo "<h2>File Structure Check</h2>";

$baseDir = dirname(__DIR__);
echo "<p>Component base directory: " . $baseDir . "</p>";

$filesToCheck = [
    'simon.xml' => 'Manifest file',
    'admin/simon.php' => 'Entry point',
    'admin/services/provider.php' => 'Service provider',
    'admin/src/Extension/SimonComponent.php' => 'Extension class',
    'admin/src/Controller/DisplayController.php' => 'Display controller',
    'admin/src/View/Dashboard/HtmlView.php' => 'Dashboard view',
    'admin/tmpl/dashboard/default.php' => 'Dashboard template',
];

$allFilesExist = true;
foreach ($filesToCheck as $file => $description) {
    $fullPath = $baseDir . '/' . $file;
    $exists = file_exists($fullPath);
    $status = $exists ? "✓ EXISTS" : "✗ MISSING";
    $color = $exists ? "green" : "red";
    echo "<p style='color:$color;'>$description ($file): <strong>$status</strong></p>";
    if (!$exists) {
        echo "<p style='margin-left:20px; color:orange;'>Expected at: $fullPath</p>";
        $allFilesExist = false;
    }
}

echo "<hr>";
echo "<h2>Summary</h2>";

if ($allFilesExist) {
    echo "<p style='color:green;'>✓ All required files exist</p>";
} else {
    echo "<p style='color:red;'>✗ Some files are missing</p>";
}

echo "<hr>";
echo "<h2>Next Steps</h2>";
echo "<ul>";
echo "<li>If component is NOT registered: Go to <strong>Extensions → Manage → Discover</strong> and click <strong>Discover</strong></li>";
echo "<li>If component is registered but disabled: Go to <strong>Extensions → Manage → Extensions</strong>, find SIMON, and enable it</li>";
echo "<li>After any changes: <strong>System → Clear Cache</strong></li>";
echo "<li>If files are missing: Verify the component was copied correctly</li>";
echo "</ul>";

