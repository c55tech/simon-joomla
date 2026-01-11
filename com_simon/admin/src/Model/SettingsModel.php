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
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

/**
 * Settings model for SIMON component
 */
class SettingsModel extends AdminModel
{
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     *
     * @since   1.0.0
     */
    public function getTable($name = 'Extension', $prefix = '\\Joomla\\CMS\\Table\\', $options = [])
    {
        return Table::getInstance('extension');
    }

    /**
     * Method to get the form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \Joomla\CMS\Form\Form|boolean  A Form object on success, false on failure
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_simon.settings', 'settings', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_simon.edit.settings.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_simon.settings', $data);

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        $item = new \stdClass();
        
        // Load params directly from the Extension table to avoid cache issues
        $table = Table::getInstance('extension');
        
        if ($table->load(['element' => 'com_simon', 'type' => 'component'])) {
            $params = new Registry($table->params);
        } else {
            $params = new Registry();
        }
        
        $item->api_url = $params->get('api_url', '');
        $item->auth_key = $params->get('auth_key', '');
        $item->enable_cron = $params->get('enable_cron', 0);
        $item->cron_interval = $params->get('cron_interval', 3600);
        
        return $item;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     */
    public function save($data)
    {
        $table = Table::getInstance('extension');
        
        if (!$table->load(['element' => 'com_simon', 'type' => 'component'])) {
            $this->setError($table->getError());
            return false;
        }
        
        // Get existing params from the loaded table (already decoded from JSON)
        $params = new Registry($table->params);
        
        // Update params with new data
        foreach ($data as $key => $value) {
            $params->set($key, $value);
        }
        
        // Convert Registry to JSON string for storage
        $table->params = (string) $params;
        
        if (!$table->store()) {
            $this->setError($table->getError());
            return false;
        }
        
        // Clear component cache to ensure params are reloaded
        $this->cleanCache('_system');
        $this->cleanCache('com_simon');
        
        return true;
    }
}
