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

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;

/**
 * Roadmap item model
 *
 * @since  1.0.0
 */
class RoadmapItemModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  1.0.0
     */
    public $typeAlias = 'com_simon.roadmap';

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  \Joomla\CMS\Table\Table  A Table object
     *
     * @since   1.0.0
     */
    public function getTable($name = 'Roadmap', $prefix = 'Administrator\\Table\\', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \Joomla\CMS\Form\Form|boolean  A Form object on success, false on failure
     *
     * @since   1.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_simon.roadmap', 'roadmap', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.0.0
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_simon.edit.roadmap.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_simon.roadmap', $data);

        return $data;
    }

    /**
     * Get suggestions for a roadmap item
     *
     * @param   int  $roadmapId  The roadmap ID
     *
     * @return  array
     *
     * @since   1.0.0
     */
    public function getRoadmapSuggestions($roadmapId)
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('s.*, rs.order')
            ->from($db->quoteName('#__simon_roadmap_suggestions', 'rs'))
            ->join('INNER', $db->quoteName('#__simon_suggestions', 's') . ' ON s.id = rs.suggestion_id')
            ->where($db->quoteName('rs.roadmap_id') . ' = ' . (int) $roadmapId)
            ->order($db->quoteName('rs.order') . ' ASC');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Save roadmap suggestions
     *
     * @param   int    $roadmapId     The roadmap ID
     * @param   array  $suggestionIds Array of suggestion IDs in order
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public function saveRoadmapSuggestions($roadmapId, $suggestionIds)
    {
        $db = $this->getDatabase();

        // Delete existing mappings
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__simon_roadmap_suggestions'))
            ->where($db->quoteName('roadmap_id') . ' = ' . (int) $roadmapId);
        $db->setQuery($query);
        $db->execute();

        // Insert new mappings
        if (!empty($suggestionIds)) {
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__simon_roadmap_suggestions'))
                ->columns([$db->quoteName('roadmap_id'), $db->quoteName('suggestion_id'), $db->quoteName('order')]);

            foreach ($suggestionIds as $order => $suggestionId) {
                $query->values((int) $roadmapId . ', ' . (int) $suggestionId . ', ' . (int) $order);
            }

            $db->setQuery($query);
            $db->execute();

            // Update suggestion roadmap_id
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__simon_suggestions'))
                ->set($db->quoteName('roadmap_id') . ' = ' . (int) $roadmapId)
                ->where($db->quoteName('id') . ' IN (' . implode(',', array_map('intval', $suggestionIds)) . ')');
            $db->setQuery($query);
            $db->execute();
        }

        return true;
    }
}

