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
 * Suggestions model
 *
 * @since  1.0.0
 */
class SuggestionsModel extends ListModel
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
                'priority', 'a.priority',
                'category', 'a.category',
                'user_name', 'a.user_name',
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
        $query->from($db->quoteName('#__simon_suggestions', 'a'));

        // Filter by status
        $status = $this->getState('filter.status');
        if ($status !== null && $status !== '') {
            $query->where($db->quoteName('a.status') . ' = ' . $db->quote($status));
        }

        // Filter by priority
        $priority = $this->getState('filter.priority');
        if ($priority !== null && $priority !== '') {
            $query->where($db->quoteName('a.priority') . ' = ' . $db->quote($priority));
        }

        // Filter by category
        $category = $this->getState('filter.category');
        if ($category !== null && $category !== '') {
            $query->where($db->quoteName('a.category') . ' = ' . $db->quote($category));
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
        $orderCol = $this->state->get('list.ordering', 'a.created');
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
    protected function populateState($ordering = 'a.created', $direction = 'DESC')
    {
        $app = Factory::getApplication();

        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        $status = $app->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', '', 'string');
        $this->setState('filter.status', $status);

        $priority = $app->getUserStateFromRequest($this->context . '.filter.priority', 'filter_priority', '', 'string');
        $this->setState('filter.priority', $priority);

        $category = $app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '', 'string');
        $this->setState('filter.category', $category);

        $published = $app->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
        $this->setState('filter.published', $published);

        parent::populateState($ordering, $direction);
    }
}

