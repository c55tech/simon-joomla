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

// This is an administrator-only component
// Redirect to administrator if accessed from frontend
$app = Factory::getApplication();

if ($app->isClient('site')) {
    $app->enqueueMessage('This component is only available in the administrator area.', 'warning');
    $app->redirect('administrator/index.php?option=com_simon');
}

// Get the application
$app = Factory::getApplication();

// Define the component name
$component = 'com_simon';

// Execute the task
$controller = BaseController::getInstance($component);
$controller->execute($app->input->get('task'));
$controller->redirect();

