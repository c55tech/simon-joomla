<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

try {
	// Get the application
	$app = Factory::getApplication();

	// Define the component name
	$component = 'com_simon';

	// Execute the task
	$controller = BaseController::getInstance($component);
	$controller->execute($app->input->get('task'));
	$controller->redirect();
} catch (Exception $e) {
	// If debug mode is enabled, show the error
	if (JDEBUG) {
		echo '<h1>Component Error</h1>';
		echo '<p>' . $e->getMessage() . '</p>';
		echo '<pre>' . $e->getTraceAsString() . '</pre>';
	} else {
		$app = Factory::getApplication();
		$app->enqueueMessage('Component error occurred. Please enable debug mode to see details.', 'error');
		$app->redirect('index.php');
	}
}

