<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Administrator\View\Roadmap;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Roadmap list view
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * An array of items
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var    \Joomla\CMS\Pagination\Pagination
	 * @since  1.0.0
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var    \Joomla\CMS\Object\CMSObject
	 * @since  1.0.0
	 */
	protected $state;

	/**
	 * Form object for search filters
	 *
	 * @var    \Joomla\CMS\Form\Form
	 * @since  1.0.0
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	public $activeFilters;

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
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Load suggestions for each roadmap item
		foreach ($this->items as $item) {
			$item->suggestions = $this->getModel()->getRoadmapSuggestions($item->id);
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
		ToolbarHelper::title(Text::_('COM_SIMON_ROADMAP_TITLE'), 'calendar');
		ToolbarHelper::addNew('roadmap.add');
		ToolbarHelper::editList('roadmap.edit');
		ToolbarHelper::publish('roadmap.publish', 'JTOOLBAR_PUBLISH', true);
		ToolbarHelper::unpublish('roadmap.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'roadmap.delete', 'JTOOLBAR_DELETE');
		ToolbarHelper::preferences('com_simon');
	}
}

