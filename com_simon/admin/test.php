<?php
/**
 * Diagnostic test file for SIMON component
 * Access via: /administrator/components/com_simon/admin/test.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>SIMON Component Diagnostic Test</h1>";
echo "<p>PHP is working!</p>";

// Find Joomla root - go up from administrator/components/com_simon/admin/
// We're at: /var/www/html/administrator/components/com_simon/admin/test.php
// Need to go up 5 levels to get to /var/www/html
$currentFile = __FILE__;
$joomlaRoot = dirname(dirname(dirname(dirname(dirname($currentFile)))));
echo "<p>Current file: " . $currentFile . "</p>";
echo "<p>Joomla root path (calculated): " . $joomlaRoot . "</p>";

// Verify by checking for configuration.php
$configFile = $joomlaRoot . '/configuration.php';
if (!file_exists($configFile)) {
    // Try one level up (in case we're already at root)
    $joomlaRoot = dirname($joomlaRoot);
    $configFile = $joomlaRoot . '/configuration.php';
    echo "<p>Trying alternative path: " . $joomlaRoot . "</p>";
}

if (file_exists($configFile)) {
    echo "<p style='color:green;'>✓ Found Joomla configuration.php at: " . $configFile . "</p>";
} else {
    echo "<p style='color:red;'>✗ Joomla configuration.php not found. Trying to locate...</p>";
    // Try common locations
    $possibleRoots = [
        '/var/www/html',
        dirname(dirname(dirname(dirname(__FILE__)))),
        dirname(dirname(dirname(dirname(dirname(__FILE__))))),
    ];
    foreach ($possibleRoots as $possibleRoot) {
        if (file_exists($possibleRoot . '/configuration.php')) {
            $joomlaRoot = $possibleRoot;
            echo "<p style='color:green;'>✓ Found Joomla at: " . $joomlaRoot . "</p>";
            break;
        }
    }
}

// Load Joomla bootstrap
echo "<p>Attempting to load Joomla...</p>";

// Set JPATH_BASE if not already set
if (!defined('JPATH_BASE')) {
    define('JPATH_BASE', $joomlaRoot);
    echo "<p>✓ JPATH_BASE defined: " . JPATH_BASE . "</p>";
}

$bootstrapFile = $joomlaRoot . '/includes/app.php';
echo "<p>Checking for bootstrap file: " . $bootstrapFile . "</p>";

if (file_exists($bootstrapFile)) {
    echo "<p>✓ Bootstrap file found, loading...</p>";
    echo "<p>Flushing output before require...</p>";
    flush();
    ob_flush();
    
    // Set error handler to catch fatal errors
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        echo "<p style='color:red;'>✗ PHP Error ($errno): $errstr in $errfile on line $errline</p>";
        return true;
    });
    
    try {
        echo "<p>About to require bootstrap file...</p>";
        flush();
        require_once $bootstrapFile;
        echo "<p style='color:green;'>✓ Joomla bootstrap loaded successfully!</p>";
        $bootstrapLoaded = true;
    } catch (Exception $e) {
        echo "<p style='color:red;'>✗ Exception loading bootstrap: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        $bootstrapFile = null;
        $bootstrapLoaded = false;
    } catch (Error $e) {
        echo "<p style='color:red;'>✗ Fatal error loading bootstrap: " . $e->getMessage() . "</p>";
        echo "<p>Error file: " . $e->getFile() . " on line " . $e->getLine() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        $bootstrapFile = null;
        $bootstrapLoaded = false;
    } catch (Throwable $e) {
        echo "<p style='color:red;'>✗ Throwable error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        $bootstrapFile = null;
        $bootstrapLoaded = false;
    }
    
    restore_error_handler();
    
    if (!isset($bootstrapLoaded)) {
        echo "<p style='color:red;'>✗ Bootstrap loading failed silently. Checking if classes are available...</p>";
        $bootstrapLoaded = false;
    }
} else {
    echo "<p>Bootstrap file not found, trying alternative method...</p>";
    // Try alternative bootstrap
    $definesFile = $joomlaRoot . '/includes/defines.php';
    echo "<p>Checking for defines file: " . $definesFile . "</p>";
    if (file_exists($definesFile)) {
        echo "<p>✓ Defines file found, loading...</p>";
        try {
            require_once $definesFile;
            echo "<p>✓ Defines loaded</p>";
            if (defined('JPATH_LIBRARIES')) {
                $libBootstrap = JPATH_LIBRARIES . '/bootstrap.php';
                echo "<p>Checking for library bootstrap: " . $libBootstrap . "</p>";
                if (file_exists($libBootstrap)) {
                    require_once $libBootstrap;
                    echo "<p style='color:green;'>✓ Joomla libraries loaded!</p>";
                } else {
                    echo "<p style='color:red;'>✗ Library bootstrap not found at: " . $libBootstrap . "</p>";
                    $bootstrapFile = null;
                }
            } else {
                echo "<p style='color:red;'>✗ JPATH_LIBRARIES not defined after loading defines.php</p>";
                $bootstrapFile = null;
            }
        } catch (Exception $e) {
            echo "<p style='color:red;'>✗ Exception: " . $e->getMessage() . "</p>";
            $bootstrapFile = null;
        } catch (Error $e) {
            echo "<p style='color:red;'>✗ Fatal error: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            $bootstrapFile = null;
        }
    } else {
        echo "<p style='color:red;'>✗ Joomla bootstrap file not found at: " . $bootstrapFile . "</p>";
        echo "<p style='color:red;'>✗ Defines file not found at: " . $definesFile . "</p>";
        $bootstrapFile = null;
    }
}

if (isset($bootstrapLoaded) && $bootstrapLoaded) {
    echo "<p>Bootstrap loaded, continuing with Joomla checks...</p>";
}

// Check if Joomla loaded
$joomlaAvailable = false;
if (defined('JPATH_LIBRARIES') || class_exists('Joomla\\CMS\\Factory') || (isset($bootstrapLoaded) && $bootstrapLoaded)) {
    $joomlaAvailable = true;
    echo "<p style='color:green;'>✓ Joomla framework is available</p>";
}

if ($joomlaAvailable) {
    try {
        // Initialize Joomla application
        if (!class_exists('Joomla\\CMS\\Factory')) {
            if (defined('JPATH_LIBRARIES')) {
                $autoload = JPATH_LIBRARIES . '/vendor/autoload.php';
                if (file_exists($autoload)) {
                    require_once $autoload;
                }
            }
        }
        
        if (class_exists('Joomla\\CMS\\Factory')) {
            $app = \Joomla\CMS\Factory::getApplication('Administrator');
            echo "<p style='color:green;'>✓ Joomla application loaded!</p>";
            if (defined('JVERSION')) {
                echo "<p>Joomla Version: " . JVERSION . "</p>";
            }
            
            // Check if entry point exists
            $entryPoint = __DIR__ . '/simon.php';
            echo "<p>Entry point file exists: " . (file_exists($entryPoint) ? "YES ✓" : "NO ✗") . "</p>";
            if (file_exists($entryPoint)) {
                echo "<p>Entry point path: " . $entryPoint . "</p>";
                echo "<p>Entry point readable: " . (is_readable($entryPoint) ? "YES" : "NO") . "</p>";
            }
            
            // Check if service provider exists
            $serviceProvider = __DIR__ . '/services/provider.php';
            echo "<p>Service provider exists: " . (file_exists($serviceProvider) ? "YES ✓" : "NO ✗") . "</p>";
            
            // Check if Extension class exists
            $extensionClass = __DIR__ . '/src/Extension/SimonComponent.php';
            echo "<p>Extension class exists: " . (file_exists($extensionClass) ? "YES ✓" : "NO ✗") . "</p>";
            
            // Check if DisplayController exists
            $controller = __DIR__ . '/src/Controller/DisplayController.php';
            echo "<p>DisplayController exists: " . (file_exists($controller) ? "YES ✓" : "NO ✗") . "</p>";
            
            // Check if Dashboard view exists
            $dashboardView = __DIR__ . '/src/View/Dashboard/HtmlView.php';
            echo "<p>Dashboard view exists: " . (file_exists($dashboardView) ? "YES ✓" : "NO ✗") . "</p>";
            
            // Check if Dashboard template exists
            $dashboardTemplate = __DIR__ . '/tmpl/dashboard/default.php';
            echo "<p>Dashboard template exists: " . (file_exists($dashboardTemplate) ? "YES ✓" : "NO ✗") . "</p>";
            
            // Check if component is registered
            $db = \Joomla\CMS\Factory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('element') . ' = ' . $db->quote('com_simon'))
                ->where($db->quoteName('type') . ' = ' . $db->quote('component'));
            $db->setQuery($query);
            $component = $db->loadObject();
            
            if ($component) {
                echo "<p><strong style='color:green;'>Component is registered: YES ✓</strong></p>";
                echo "<p>Component enabled: " . ($component->enabled ? "<strong style='color:green;'>YES ✓</strong>" : "<strong style='color:red;'>NO ✗ - NEEDS TO BE ENABLED</strong>") . "</p>";
                $manifest = json_decode($component->manifest_cache);
                if ($manifest) {
                    echo "<p>Component version: " . ($manifest->version ?? "Unknown") . "</p>";
                }
                echo "<p>Component ID: " . $component->extension_id . "</p>";
            } else {
                echo "<p><strong style='color:red;'>Component is registered: NO ✗</strong></p>";
                echo "<p><strong>ACTION NEEDED: Go to Extensions → Manage → Discover and click Discover</strong></p>";
            }
        } else {
            echo "<p style='color:orange;'>⚠ Joomla Factory class not available, but framework constants are defined</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } catch (Error $e) {
        echo "<p style='color:red;'><strong>Fatal Error:</strong> " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        $joomlaAvailable = false;
    }
} else {
    echo "<p style='color:orange;'>⚠ Could not fully load Joomla framework. Will show file structure only...</p>";
    $joomlaAvailable = false;
}

// Always show file structure, even if Joomla didn't load
if (!$joomlaAvailable) {
    echo "<hr>";
    echo "<h2>⚠ Joomla Not Fully Loaded - Showing File Structure Only</h2>";
    echo "<p>This means we can't check the database, but we can verify files exist.</p>";
}

echo "<hr>";
echo "<h2>File Structure Check</h2>";
echo "<p>Checking component directory structure...</p>";

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

foreach ($filesToCheck as $file => $description) {
    $fullPath = $baseDir . '/' . $file;
    $exists = file_exists($fullPath);
    $status = $exists ? "✓ EXISTS" : "✗ MISSING";
    $color = $exists ? "green" : "red";
    echo "<p style='color:$color;'>$description ($file): <strong>$status</strong></p>";
    if (!$exists) {
        echo "<p style='margin-left:20px; color:orange;'>Expected at: $fullPath</p>";
    }
}

echo "<hr>";
echo "<h2>Next Steps</h2>";
echo "<ul>";
echo "<li>If component is NOT registered: Go to <strong>Extensions → Manage → Discover</strong> and click <strong>Discover</strong></li>";
echo "<li>If component is registered but disabled: Go to <strong>Extensions → Manage → Extensions</strong>, find SIMON, and enable it</li>";
echo "<li>After any changes: <strong>System → Clear Cache</strong></li>";
echo "<li>If files are missing: Verify the component was copied correctly to the Joomla installation</li>";
echo "</ul>";
