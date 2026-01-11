<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Simon\Administrator\Table\SuggestionTable;

/**
 * Suggestion submission controller for site
 *
 * @since  1.0.0
 */
class SuggestionController extends BaseController
{
	/**
	 * Submit a suggestion
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function submit()
	{
		$app = Factory::getApplication();
		$user = Factory::getUser();
		$input = $app->input;
		
		// Check if user is logged in
		if ($user->guest) {
			$app->enqueueMessage(Text::_('COM_SIMON_ERROR_LOGIN_REQUIRED'), 'error');
			$app->redirect('index.php?option=com_users&view=login');
			return;
		}
		
		// Get form data
		$data = [
			'title' => $input->getString('title', ''),
			'description' => $input->getString('description', ''),
			'category' => $input->getString('category', ''),
			'priority' => $input->getString('priority', 'medium'),
			'status' => 'submitted',
			'user_id' => $user->id,
			'user_name' => $user->name,
			'user_email' => $user->email,
			'published' => 1,
		];
		
		// Validate
		if (empty($data['title']) || empty($data['description'])) {
			$app->enqueueMessage(Text::_('COM_SIMON_ERROR_TITLE_DESCRIPTION_REQUIRED'), 'error');
			$app->redirect('index.php?option=com_simon&view=suggestion&layout=submit');
			return;
		}
		
		// Save suggestion
		$table = new SuggestionTable(Factory::getDbo());
		
		if (!$table->save($data)) {
			$app->enqueueMessage(Text::_('COM_SIMON_ERROR_SUBMIT_FAILED') . ': ' . $table->getError(), 'error');
			$app->redirect('index.php?option=com_simon&view=suggestion&layout=submit');
			return;
		}
		
		$app->enqueueMessage(Text::_('COM_SIMON_SUGGESTION_SUBMITTED_SUCCESS'), 'success');
		$app->redirect('index.php?option=com_simon&view=suggestion&layout=mysuggestions');
	}
}

