<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerScript;

/**
 * Installation script for SIMON component
 *
 * @since  1.0.0
 */
class Com_SimonInstallerScript extends InstallerScript
{
	/**
	 * Minimum Joomla version required to install the extension
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $minimumJoomla = '4.0.0';

	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $minimumPhp = '7.4.0';

	/**
	 * Method to run after the install routine
	 *
	 * @param   string            $type    The action being performed
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0.0
	 */
	public function postflight($type, $parent)
	{
		return true;
	}
}

