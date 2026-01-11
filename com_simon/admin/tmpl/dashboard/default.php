<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<h2><?php echo Text::_('COM_SIMON_DASHBOARD_WELCOME'); ?></h2>
					<p><?php echo Text::_('COM_SIMON_DASHBOARD_DESCRIPTION'); ?></p>
					
					<div class="alert alert-info">
						<?php echo Text::_('COM_SIMON_DASHBOARD_INFO'); ?>
					</div>
					
					<div class="row mt-4">
						<div class="col-md-6">
							<div class="card">
								<div class="card-header">
									<h3><?php echo Text::_('COM_SIMON_DASHBOARD_QUICK_LINKS'); ?></h3>
								</div>
								<div class="card-body">
									<ul class="list-unstyled">
										<li>
											<a href="<?php echo 'index.php?option=com_simon&view=settings'; ?>">
												<?php echo Text::_('COM_SIMON_SUBMENU_SETTINGS'); ?>
											</a>
										</li>
										<li>
											<a href="<?php echo 'index.php?option=com_simon&view=client'; ?>">
												<?php echo Text::_('COM_SIMON_SUBMENU_CLIENT'); ?>
											</a>
										</li>
										<li>
											<a href="<?php echo 'index.php?option=com_simon&view=site'; ?>">
												<?php echo Text::_('COM_SIMON_SUBMENU_SITE'); ?>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

