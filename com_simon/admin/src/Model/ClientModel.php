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
use Joomla\Component\Simon\Administrator\Helper\DataHelper;
use Joomla\CMS\Language\Text;

/**
 * Client model for SIMON component
 */
class ClientModel extends AdminModel
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
        $form = $this->loadForm('com_simon.client', 'client', ['control' => 'jform', 'load_data' => $loadData]);

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
        $data = $app->getUserState('com_simon.edit.client.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_simon.client', $data);

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
        
        // Load client-specific params with 'client_' prefix
        $item->name = $params->get('client_name', '');
        $item->contact_name = $params->get('client_contact_name', '');
        $item->contact_email = $params->get('client_contact_email', '');
        $item->notes = $params->get('client_notes', '');
        $item->status = $params->get('client_status', 1);
        
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
        
        // Get existing params from the loaded table
        $params = new Registry($table->params);
        
        // Validate required fields
        if (empty($data['name'])) {
            $this->setError(Text::_('COM_SIMON_ERROR_CLIENT_NAME_REQUIRED'));
            return false;
        }
        
        // Update params with new data, using 'client_' prefix
        $params->set('client_name', $data['name'] ?? '');
        $params->set('client_contact_name', $data['contact_name'] ?? '');
        $params->set('client_contact_email', $data['contact_email'] ?? '');
        $params->set('client_notes', $data['notes'] ?? '');
        $params->set('client_status', $data['status'] ?? 1);
        
        // Convert Registry to JSON string for storage
        $table->params = (string) $params;
        
        if (!$table->store()) {
            $this->setError($table->getError());
            return false;
        }
        
        // Submit to SIMON API
        $apiData = [
            'name' => $data['name'] ?? '',
            'contact_name' => $data['contact_name'] ?? '',
            'contact_email' => $data['contact_email'] ?? '',
            'notes' => $data['notes'] ?? '',
        ];
        
        $response = DataHelper::submitToApi('clients', $apiData);
        
        if ($response) {
            // Handle both object and array responses
            $clientId = null;
            if (is_object($response) && isset($response->client_id)) {
                $clientId = $response->client_id;
            } elseif (is_array($response) && isset($response['client_id'])) {
                $clientId = $response['client_id'];
            } elseif (is_object($response) && isset($response->client) && isset($response->client->id)) {
                $clientId = $response->client->id;
            } elseif (is_array($response) && isset($response['client']) && isset($response['client']['id'])) {
                $clientId = $response['client']['id'];
            }
            
            if ($clientId) {
                // Store client_id for future use
                $params->set('client_id', $clientId);
                $table->params = (string) $params;
                $table->store();
                
                $app = Factory::getApplication();
                $app->enqueueMessage(
                    'Client saved successfully! Client ID: ' . $clientId,
                    'success'
                );
            } else {
                // Response received but no client_id found
                $app = Factory::getApplication();
                $app->enqueueMessage(
                    'Client data saved locally, but API response was unexpected. Please check your API configuration.',
                    'warning'
                );
            }
        } else {
            // API submission failed, but local save succeeded
            $app = Factory::getApplication();
            $app->enqueueMessage(
                'Client data saved locally, but failed to submit to SIMON API. Please check your API configuration.',
                'warning'
            );
        }
        
        // Clear component cache to ensure params are reloaded
        $this->cleanCache('_system');
        $this->cleanCache('com_simon');
        
        return true;
    }
}

