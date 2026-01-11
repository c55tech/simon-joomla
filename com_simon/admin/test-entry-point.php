<?php
/**
 * Test the component entry point directly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing Component Entry Point</h1>";

// Get Joomla root
$joomlaRoot = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
define('JPATH_BASE', $joomlaRoot);

// Load Joomla
require_once $joomlaRoot . '/includes/app.php';

echo "<p style='color:green;'>✓ Joomla loaded</p>";

// Get application
$app = \Joomla\CMS\Factory::getApplication('Administrator');
echo "<p style='color:green;'>✓ Application loaded</p>";

// Check if we can get the controller
try {
    $component = 'com_simon';
    echo "<p>Attempting to get controller for: $component</p>";
    
    $controller = \Joomla\CMS\MVC\Controller\BaseController::getInstance($component);
    echo "<p style='color:green;'>✓ Controller instance created</p>";
    echo "<p>Controller class: " . get_class($controller) . "</p>";
    
    // Try to get the default view
    $reflection = new ReflectionClass($controller);
    if ($reflection->hasProperty('default_view')) {
        $prop = $reflection->getProperty('default_view');
        $prop->setAccessible(true);
        $defaultView = $prop->getValue($controller);
        echo "<p>Default view: $defaultView</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Error getting controller: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<p style='color:red;'>✗ Fatal error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Check service provider
$serviceProvider = __DIR__ . '/services/provider.php';
if (file_exists($serviceProvider)) {
    echo "<p style='color:green;'>✓ Service provider file exists</p>";
    
    // Try to load it
    try {
        $provider = require $serviceProvider;
        echo "<p style='color:green;'>✓ Service provider loaded</p>";
        echo "<p>Provider type: " . get_class($provider) . "</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>✗ Error loading service provider: " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p style='color:red;'>✗ Fatal error loading service provider: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}

// Check Extension class
$extensionClass = __DIR__ . '/src/Extension/SimonComponent.php';
if (file_exists($extensionClass)) {
    echo "<p style='color:green;'>✓ Extension class file exists</p>";
    
    // Check if class can be loaded
    if (class_exists('Joomla\\Component\\Simon\\Administrator\\Extension\\SimonComponent')) {
        echo "<p style='color:green;'>✓ Extension class is available</p>";
    } else {
        echo "<p style='color:orange;'>⚠ Extension class not autoloaded (may need namespace registration)</p>";
    }
}

echo "<hr>";
echo "<h2>Recommendations</h2>";
echo "<ol>";
echo "<li><strong>Clear ALL caches:</strong> System → Clear Cache (select 'All' and delete)</li>";
echo "<li><strong>Check Joomla error logs</strong> for any component loading errors</li>";
echo "<li><strong>Enable debug mode</strong> to see detailed error messages</li>";
echo "<li><strong>Try accessing directly:</strong> <code>index.php?option=com_simon&view=dashboard</code></li>";
echo "</ol>";

