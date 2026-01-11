<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Language\Text;

/**
 * Site controller for SIMON component
 *
 * @since  1.0.0
 */
class SiteController extends FormController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_SIMON_SITE';

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.0.0
	 */
	public function save($key = null, $urlVar = null)
	{
		$result = parent::save($key, $urlVar);

		// Model handles success/error messages, including API submission status
		// Only set generic message if model didn't set one
		if ($result && !$this->getMessage()) {
			$this->setMessage(Text::_('COM_SIMON_SITE_SAVED_SUCCESSFULLY'));
		}

		return $result;
	}
}

