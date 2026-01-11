<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;

/**
 * Roadmap list model
 *
 * @since  1.0.0
 */
class RoadmapModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @since   1.0.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'status', 'a.status',
                'quarter', 'a.quarter',
                'year', 'a.year',
                'start_date', 'a.start_date',
                'end_date', 'a.end_date',
                'created', 'a.created',
                'published', 'a.published',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  DatabaseQuery  A DatabaseQuery object to retrieve the data set.
     *
     * @since   1.0.0
     */
    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            $this->getState(
                'list.select',
                'a.*'
            )
        );
        $query->from($db->quoteName('#__simon_roadmap', 'a'));

        // Filter by status
        $status = $this->getState('filter.status');
        if ($status !== null && $status !== '') {
            $query->where($db->quoteName('a.status') . ' = ' . $db->quote($status));
        }

        // Filter by year
        $year = $this->getState('filter.year');
        if ($year !== null && $year !== '') {
            $query->where($db->quoteName('a.year') . ' = ' . (int) $year);
        }

        // Filter by quarter
        $quarter = $this->getState('filter.quarter');
        if ($quarter !== null && $quarter !== '') {
            $query->where($db->quoteName('a.quarter') . ' = ' . $db->quote($quarter));
        }

        // Filter by published
        $published = $this->getState('filter.published');
        if ($published !== null && $published !== '') {
            $query->where($db->quoteName('a.published') . ' = ' . (int) $published);
        }

        // Search
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
            $query->where('(a.title LIKE ' . $search . ' OR a.description LIKE ' . $search . ')');
        }

        // Add the list ordering clause
        $orderCol = $this->state->get('list.ordering', 'a.year, a.quarter');
        $orderDirn = $this->state->get('list.direction', 'DESC');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    /**
     * Method to auto-populate the model state.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function populateState($ordering = 'a.year, a.quarter', $direction = 'DESC')
    {
        $app = Factory::getApplication();

        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        $status = $app->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', '', 'string');
        $this->setState('filter.status', $status);

        $year = $app->getUserStateFromRequest($this->context . '.filter.year', 'filter_year', '', 'string');
        $this->setState('filter.year', $year);

        $quarter = $app->getUserStateFromRequest($this->context . '.filter.quarter', 'filter_quarter', '', 'string');
        $this->setState('filter.quarter', $quarter);

        $published = $app->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
        $this->setState('filter.published', $published);

        parent::populateState($ordering, $direction);
    }

}
