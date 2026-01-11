<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Administrator\View\Client;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * Client view class for SIMON component
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The form object
	 *
	 * @var    \Joomla\CMS\Form\Form
	 * @since  1.0.0
	 */
	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function display($tpl = null)
	{
		// Get the model
		$model = $this->getModel();
		
		// Get the form
		$this->form = $model->getForm();
		
		if (empty($this->form)) {
			throw new \Exception('Form not found', 500);
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function addToolbar()
	{
		ToolbarHelper::title(Text::_('COM_SIMON_CLIENT_TITLE'), 'users');
		ToolbarHelper::apply('client.apply');
		ToolbarHelper::save('client.save');
		ToolbarHelper::cancel('client.cancel');
	}
}

