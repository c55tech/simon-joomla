<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;

/**
 * Component class for com_simon
 *
 * @since  1.0.0
 */
class SimonComponent extends MVCComponent
{
	use HTMLRegistryAwareTrait;

	/**
	 * Constructor
	 *
	 * @param   ComponentDispatcherFactoryInterface  $dispatcherFactory  The component dispatcher factory
	 *
	 * @since   1.0.0
	 */
	public function __construct(ComponentDispatcherFactoryInterface $dispatcherFactory)
	{
		parent::__construct($dispatcherFactory);
	}
}

