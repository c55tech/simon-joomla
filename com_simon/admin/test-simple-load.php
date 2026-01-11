<?php
/**
 * Simple test to check component loading without full bootstrap
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>Simple Component Load Test</h1>";
echo "<p>Testing step by step...</p>";

// Step 1: Check Joomla root
$joomlaRoot = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
echo "<p>Step 1: Joomla root = $joomlaRoot</p>";

// Step 2: Check if we can define JPATH_BASE
if (!defined('JPATH_BASE')) {
    define('JPATH_BASE', $joomlaRoot);
    echo "<p>Step 2: ✓ JPATH_BASE defined</p>";
} else {
    echo "<p>Step 2: ✓ JPATH_BASE already defined</p>";
}

// Step 3: Try to load defines
$definesFile = $joomlaRoot . '/includes/defines.php';
echo "<p>Step 3: Loading defines from: $definesFile</p>";

if (file_exists($definesFile)) {
    try {
        require_once $definesFile;
        echo "<p style='color:green;'>✓ Defines loaded</p>";
        
        if (defined('JPATH_LIBRARIES')) {
            echo "<p>JPATH_LIBRARIES = " . JPATH_LIBRARIES . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>✗ Exception loading defines: " . $e->getMessage() . "</p>";
        die();
    } catch (Error $e) {
        echo "<p style='color:red;'>✗ Fatal error loading defines: " . $e->getMessage() . "</p>";
        echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
        die();
    }
} else {
    echo "<p style='color:red;'>✗ Defines file not found!</p>";
    die();
}

// Step 4: Try to load bootstrap
$bootstrapFile = JPATH_LIBRARIES . '/bootstrap.php';
echo "<p>Step 4: Loading bootstrap from: $bootstrapFile</p>";

if (file_exists($bootstrapFile)) {
    try {
        require_once $bootstrapFile;
        echo "<p style='color:green;'>✓ Bootstrap loaded</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>✗ Exception loading bootstrap: " . $e->getMessage() . "</p>";
        die();
    } catch (Error $e) {
        echo "<p style='color:red;'>✗ Fatal error loading bootstrap: " . $e->getMessage() . "</p>";
        echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
        die();
    }
} else {
    echo "<p style='color:red;'>✗ Bootstrap file not found!</p>";
    die();
}

// Step 5: Try to load autoloader
$autoloadFile = JPATH_LIBRARIES . '/vendor/autoload.php';
echo "<p>Step 5: Loading autoloader from: $autoloadFile</p>";

if (file_exists($autoloadFile)) {
    try {
        require_once $autoloadFile;
        echo "<p style='color:green;'>✓ Autoloader loaded</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>✗ Exception loading autoloader: " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p style='color:red;'>✗ Fatal error loading autoloader: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠ Autoloader not found (may not be needed)</p>";
}

// Step 6: Try to get Factory
echo "<p>Step 6: Checking if Factory class exists...</p>";

if (class_exists('Joomla\\CMS\\Factory')) {
    echo "<p style='color:green;'>✓ Factory class exists</p>";
    
    try {
        $app = \Joomla\CMS\Factory::getApplication('Administrator');
        echo "<p style='color:green;'>✓ Application created</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>✗ Exception creating application: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } catch (Error $e) {
        echo "<p style='color:red;'>✗ Fatal error creating application: " . $e->getMessage() . "</p>";
        echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<p style='color:red;'>✗ Factory class not found</p>";
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>If you see errors above, that's where the component loading is failing.</p>";
echo "<p>If all steps show ✓, then the issue is likely in the component's service provider or entry point.</p>";

