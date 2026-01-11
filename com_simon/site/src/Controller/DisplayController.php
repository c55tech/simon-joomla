<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * SIMON Component Controller (Site)
 * 
 * This component is administrator-only, so this redirects to admin
 */
class DisplayController extends BaseController
{
    /**
     * The default view.
     *
     * @var    string
     */
    protected $default_view = 'dashboard';

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe URL parameters
     *
     * @return  BaseController|boolean  This object to support chaining.
     */
    public function display($cachable = false, $urlparams = [])
    {
        $app = Factory::getApplication();
        $input = $app->input;
        $view = $input->get('view', 'dashboard');
        
        // Redirect all views to administrator
        $app->enqueueMessage('This component is only available in the administrator area.', 'info');
        $app->redirect('administrator/index.php?option=com_simon');
        
        return parent::display();
    }
}

