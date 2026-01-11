<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Site\View\Roadmap;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Roadmap view for site
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Roadmap items
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
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('r.*')
			->from($db->quoteName('#__simon_roadmap', 'r'))
			->where($db->quoteName('r.published') . ' = 1')
			->order($db->quoteName('r.year') . ' DESC, ' . $db->quoteName('r.quarter') . ' DESC');
		
		$db->setQuery($query);
		$roadmapItems = $db->loadObjectList();
		
		// Load suggestions for each roadmap item
		foreach ($roadmapItems as $item) {
			$query = $db->getQuery(true)
				->select('s.*, rs.order')
				->from($db->quoteName('#__simon_roadmap_suggestions', 'rs'))
				->join('INNER', $db->quoteName('#__simon_suggestions', 's') . ' ON s.id = rs.suggestion_id')
				->where($db->quoteName('rs.roadmap_id') . ' = ' . (int) $item->id)
				->where($db->quoteName('s.published') . ' = 1')
				->order($db->quoteName('rs.order') . ' ASC');
			
			$db->setQuery($query);
			$item->suggestions = $db->loadObjectList();
		}
		
		$this->items = $roadmapItems;
		
		parent::display($tpl);
	}
}

