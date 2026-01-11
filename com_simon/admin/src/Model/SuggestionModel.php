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
use Joomla\Component\Simon\Administrator\Table\SuggestionTable;

/**
 * Suggestion model
 *
 * @since  1.0.0
 */
class SuggestionModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  1.0.0
     */
    public $typeAlias = 'com_simon.suggestion';

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
    public function getTable($name = 'Suggestion', $prefix = 'Administrator\\Table\\', $options = [])
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
        $form = $this->loadForm('com_simon.suggestion', 'suggestion', ['control' => 'jform', 'load_data' => $loadData]);

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
        $data = $app->getUserState('com_simon.edit.suggestion.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_simon.suggestion', $data);

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     *
     * @since   1.0.0
     */
    public function save($data)
    {
        $table = $this->getTable();

        // If new item, set user info
        if (empty($data['id'])) {
            $user = Factory::getUser();
            $data['user_id'] = $user->id;
            $data['user_name'] = $user->name;
            $data['user_email'] = $user->email;
        }

        return parent::save($data);
    }
}

