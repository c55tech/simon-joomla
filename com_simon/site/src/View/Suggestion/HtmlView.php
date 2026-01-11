<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Site\View\Suggestion;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Simon\Administrator\Table\SuggestionTable;

/**
 * Suggestion view for site
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
	 * User's suggestions
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $items;

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
		$user = Factory::getUser();
		$layout = $this->getLayout();
		
		if ($layout === 'submit') {
			// Load form for submission
			$form = \Joomla\CMS\Form\Form::getInstance('suggestion_submit', JPATH_ADMINISTRATOR . '/components/com_simon/forms/suggestion_submit.xml');
			$this->form = $form;
		} elseif ($layout === 'mysuggestions') {
			// Load user's suggestions
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__simon_suggestions'))
				->where($db->quoteName('user_id') . ' = ' . (int) $user->id)
				->order($db->quoteName('created') . ' DESC');
			
			$db->setQuery($query);
			$this->items = $db->loadObjectList();
		}
		
		parent::display($tpl);
	}
}

