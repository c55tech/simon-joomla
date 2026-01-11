<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

/**
 * Suggestion table class
 *
 * @since  1.0.0
 */
class SuggestionTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  Database connector object
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseDriver $db)
    {
        $this->typeAlias = 'com_simon.suggestion';
        parent::__construct('#__simon_suggestions', 'id', $db);
    }

    /**
     * Method to bind an associative array or object to the Table instance.
     *
     * @param   array|object  $src     An associative array or object to bind to the Table instance.
     * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0.0
     */
    public function bind($src, $ignore = [])
    {
        if (isset($src['description']) && is_array($src['description'])) {
            $src['description'] = implode("\n\n", $src['description']);
        }

        return parent::bind($src, $ignore);
    }

    /**
     * Method to perform sanity checks on the Table instance properties to ensure they are safe to store in the database.
     *
     * @return  boolean  True if the instance is sane and able to be stored in the database.
     *
     * @since   1.0.0
     */
    public function check()
    {
        if (empty($this->title)) {
            $this->setError('Title is required');
            return false;
        }

        if (empty($this->description)) {
            $this->setError('Description is required');
            return false;
        }

        if (empty($this->user_id)) {
            $this->setError('User ID is required');
            return false;
        }

        if (empty($this->status)) {
            $this->status = 'submitted';
        }

        if (empty($this->priority)) {
            $this->priority = 'medium';
        }

        return true;
    }
}

