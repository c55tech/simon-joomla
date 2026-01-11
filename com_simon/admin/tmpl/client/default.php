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
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
?>

<?php if (!empty($this->form)) : ?>
<form action="<?php echo Route::_('index.php?option=com_simon&view=client'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->form->renderFieldset('client'); ?>
		</div>
	</div>
	
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<?php else : ?>
<div class="alert alert-danger">
	<p><?php echo Text::_('COM_SIMON_ERROR_FORM_NOT_LOADED'); ?></p>
</div>
<?php endif; ?>

