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
 * Roadmap table class
 *
 * @since  1.0.0
 */
class RoadmapTable extends Table
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
        $this->typeAlias = 'com_simon.roadmap';
        parent::__construct('#__simon_roadmap', 'id', $db);
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

        if (empty($this->status)) {
            $this->status = 'planned';
        }

        return true;
    }
}

