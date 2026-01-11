<?php
/**
 * Quick check to see if component is installed
 * Access: /administrator/components/com_simon/admin/check-installation.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>SIMON Component Installation Check</h1>";

// Get Joomla root
$joomlaRoot = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$configFile = $joomlaRoot . '/configuration.php';

if (!file_exists($configFile)) {
    die("<p style='color:red;'>Joomla configuration not found!</p>");
}

// Read config
$configContent = file_get_contents($configFile);
preg_match("/public\s+\$host\s*=\s*['\"]([^'\"]+)['\"]/", $configContent, $hostMatch);
preg_match("/public\s+\$user\s*=\s*['\"]([^'\"]+)['\"]/", $configContent, $userMatch);
preg_match("/public\s+\$password\s*=\s*['\"]([^'\"]+)['\"]/", $configContent, $passMatch);
preg_match("/public\s+\$db\s*=\s*['\"]([^'\"]+)['\"]/", $configContent, $dbMatch);
preg_match("/public\s+\$dbprefix\s*=\s*['\"]([^'\"]+)['\"]/", $configContent, $prefixMatch);

$dbHost = $hostMatch[1] ?? 'db'; // DDEV uses 'db' as hostname
$dbUser = $userMatch[1] ?? 'db';
$dbPass = $passMatch[1] ?? 'db';
$dbName = $dbMatch[1] ?? 'db';
$dbPrefix = $prefixMatch[1] ?? 'jos_';

echo "<p>Database config from Joomla:</p>";
echo "<ul>";
echo "<li>Host: $dbHost</li>";
echo "<li>User: $dbUser</li>";
echo "<li>Database: $dbName</li>";
echo "<li>Prefix: $dbPrefix</li>";
echo "</ul>";

echo "<p>Attempting database connection...</p>";

$db = null;
$connectionError = null;

// Try multiple connection methods
$hostsToTry = [$dbHost, 'db', 'localhost', '127.0.0.1'];

foreach ($hostsToTry as $tryHost) {
    try {
        echo "<p>Trying host: <code>$tryHost</code>...</p>";
        // Force TCP connection by using 127.0.0.1 or hostname, not localhost
        $actualHost = ($tryHost === 'localhost') ? '127.0.0.1' : $tryHost;
        
        // Use mysqli with explicit port if needed
        $db = @new mysqli($actualHost, $dbUser, $dbPass, $dbName);
        
        if ($db->connect_error) {
            $connectionError = $db->connect_error;
            echo "<p style='color:orange;'>Failed: $connectionError</p>";
            if ($db) {
                $db->close();
            }
            continue;
        } else {
            echo "<p style='color:green;'>✓ Connected successfully using host: $tryHost</p>";
            break;
        }
    } catch (Exception $e) {
        $connectionError = $e->getMessage();
        echo "<p style='color:orange;'>Exception: $connectionError</p>";
        if ($db) {
            $db->close();
            $db = null;
        }
        continue;
    }
}

if (!$db || $db->connect_error) {
    echo "<hr>";
    echo "<h2 style='color:red;'>Database Connection Failed</h2>";
    echo "<p>Could not connect to database. This means we can't check if the component is installed.</p>";
    echo "<p>However, you can still check manually:</p>";
    echo "<ol>";
    echo "<li>Go to: <strong>Extensions → Manage → Extensions</strong></li>";
    echo "<li>Filter by <strong>Component</strong></li>";
    echo "<li>Search for <strong>simon</strong></li>";
    echo "<li>If it's not there, go to <strong>Extensions → Manage → Discover</strong> and click <strong>Discover</strong></li>";
    echo "</ol>";
    echo "<hr>";
    echo "<h2>File Check (can still verify)</h2>";
    $entryPoint = __DIR__ . '/simon.php';
    echo "<p>Entry point exists: " . (file_exists($entryPoint) ? "<strong style='color:green;'>YES ✓</strong>" : "<strong style='color:red;'>NO ✗</strong>") . "</p>";
    
    $serviceProvider = __DIR__ . '/services/provider.php';
    echo "<p>Service provider exists: " . (file_exists($serviceProvider) ? "<strong style='color:green;'>YES ✓</strong>" : "<strong style='color:red;'>NO ✗</strong>") . "</p>";
    
    $manifest = dirname(__DIR__) . '/simon.xml';
    echo "<p>Manifest exists: " . (file_exists($manifest) ? "<strong style='color:green;'>YES ✓</strong>" : "<strong style='color:red;'>NO ✗</strong>") . "</p>";
    
    die();
}

// If we get here, database connection succeeded
echo "<p style='color:green;'>✓ Database connected</p>";

try {
    // First, find the correct extensions table name
    $extensionsTable = null;
    
    // Look for the main extensions table (not action_logs_extensions or other variations)
    $tablesQuery = "SHOW TABLES";
    $tablesResult = $db->query($tablesQuery);
    if ($tablesResult) {
        while ($row = $tablesResult->fetch_array()) {
            $tableName = $row[0];
            // Look for table that ends with just 'extensions' (not something_extensions)
            if (preg_match('/^(.+)_extensions$/', $tableName, $matches)) {
                // Check if this table has the 'element' column (main extensions table)
                $checkColQuery = "SHOW COLUMNS FROM `" . $db->real_escape_string($tableName) . "` LIKE 'element'";
                $checkColResult = $db->query($checkColQuery);
                if ($checkColResult && $checkColResult->num_rows > 0) {
                    $extensionsTable = $tableName;
                    $dbPrefix = $matches[1] . '_';
                    echo "<p>Found main extensions table: <code>$extensionsTable</code></p>";
                    echo "<p>Detected table prefix: <code>$dbPrefix</code></p>";
                    break;
                }
            }
        }
    }
    
    // Fallback: try common table names
    if (!$extensionsTable) {
        $possibleTables = [
            $dbPrefix . 'extensions',
            'ci5o8_extensions',  // Based on the table list we saw
        ];
        
        foreach ($possibleTables as $table) {
            $checkQuery = "SHOW TABLES LIKE '" . $db->real_escape_string($table) . "'";
            $checkResult = $db->query($checkQuery);
            if ($checkResult && $checkResult->num_rows > 0) {
                // Verify it has the element column
                $checkColQuery = "SHOW COLUMNS FROM `" . $db->real_escape_string($table) . "` LIKE 'element'";
                $checkColResult = $db->query($checkColQuery);
                if ($checkColResult && $checkColResult->num_rows > 0) {
                    $extensionsTable = $table;
                    echo "<p>Using extensions table: <code>$extensionsTable</code></p>";
                    break;
                }
            }
        }
    }
    
    if (!$extensionsTable) {
        throw new Exception("Could not find the main extensions table with 'element' column.");
    }
    
    // Check if component exists
    $query = "SELECT * FROM `" . $db->real_escape_string($extensionsTable) . "` 
              WHERE `element` = 'com_simon' AND `type` = 'component'";
    
    $result = $db->query($query);
    
    if ($result && $result->num_rows > 0) {
        $component = $result->fetch_assoc();
        echo "<p style='color:green; font-size:18px;'><strong>✓ Component IS registered in database</strong></p>";
        echo "<p>Extension ID: " . $component['extension_id'] . "</p>";
        echo "<p>Name: " . htmlspecialchars($component['name']) . "</p>";
        echo "<p>Enabled: " . ($component['enabled'] == 1 ? "<strong style='color:green;'>YES ✓</strong>" : "<strong style='color:red;'>NO ✗</strong>") . "</p>";
        
        if ($component['enabled'] == 0) {
            echo "<hr>";
            echo "<h2 style='color:red;'>⚠ ACTION REQUIRED</h2>";
            echo "<p><strong>The component is installed but DISABLED.</strong></p>";
            echo "<p>To enable it:</p>";
            echo "<ol>";
            echo "<li>Go to: <strong>Extensions → Manage → Extensions</strong></li>";
            echo "<li>Filter by <strong>Component</strong></li>";
            echo "<li>Find <strong>SIMON</strong></li>";
            echo "<li>Click the toggle to <strong>Enable</strong> it</li>";
            echo "<li>Clear cache: <strong>System → Clear Cache</strong></li>";
            echo "</ol>";
        } else {
            echo "<hr>";
            echo "<h2 style='color:orange;'>Component is enabled but still showing 404?</h2>";
            echo "<p>Try these steps:</p>";
            echo "<ol>";
            echo "<li><strong>Clear all caches:</strong> System → Clear Cache</li>";
            echo "<li><strong>Check entry point:</strong> Verify <code>admin/simon.php</code> exists and is readable</li>";
            echo "<li><strong>Check service provider:</strong> Verify <code>admin/services/provider.php</code> exists</li>";
            echo "<li><strong>Enable debug mode:</strong> System → Global Configuration → System → Debug System: Yes</li>";
            echo "<li><strong>Check error logs:</strong> System → Information → Log Files</li>";
            echo "</ol>";
        }
        
        $manifest = json_decode($component['manifest_cache'], true);
        if ($manifest) {
            echo "<p>Version: " . ($manifest['version'] ?? "Unknown") . "</p>";
        }
    } else {
        echo "<p style='color:red; font-size:18px;'><strong>✗ Component is NOT registered in database</strong></p>";
        echo "<hr>";
        echo "<h2>Installation Required</h2>";
        echo "<p>The component files exist, but Joomla doesn't know about it yet.</p>";
        echo "<p><strong>Steps to install:</strong></p>";
        echo "<ol>";
        echo "<li>Go to: <strong>Extensions → Manage → Discover</strong></li>";
        echo "<li>Click the <strong>Discover</strong> button (top toolbar)</li>";
        echo "<li>Wait for the scan to complete</li>";
        echo "<li>Look for <strong>SIMON</strong> in the list</li>";
        echo "<li>Check the box next to it</li>";
        echo "<li>Click <strong>Install</strong> (top toolbar)</li>";
        echo "<li>After installation, verify it's enabled in <strong>Extensions → Manage → Extensions</strong></li>";
        echo "<li>Clear cache: <strong>System → Clear Cache</strong></li>";
        echo "</ol>";
    }
    
    $db->close();
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>File Check</h2>";
$entryPoint = __DIR__ . '/simon.php';
echo "<p>Entry point exists: " . (file_exists($entryPoint) ? "<strong style='color:green;'>YES ✓</strong>" : "<strong style='color:red;'>NO ✗</strong>") . "</p>";

$serviceProvider = __DIR__ . '/services/provider.php';
echo "<p>Service provider exists: " . (file_exists($serviceProvider) ? "<strong style='color:green;'>YES ✓</strong>" : "<strong style='color:red;'>NO ✗</strong>") . "</p>";

