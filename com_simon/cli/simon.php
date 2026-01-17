<?php
/**
 * @package     SIMON
 * @subpackage  CLI
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Console\JoomlaConsole;
use Joomla\Component\Simon\Administrator\Helper\DataHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SIMON CLI command to submit site data
 */
class SimonSubmitCommand extends JoomlaConsole
{
    /**
     * The default command name
     *
     * @var    string
     */
    protected static $defaultName = 'simon:submit';

    /**
     * Configure the command.
     *
     * @return  void
     */
    protected function configure(): void
    {
        $this->setDescription('Submit site data to SIMON API');
        $this->setHelp('This command collects site data and submits it to the configured SIMON API endpoint.');
    }

    /**
     * Internal function to execute the command.
     *
     * @param   InputInterface   $input   The input to inject into the command.
     * @param   OutputInterface  $output  The output to inject into the command.
     *
     * @return  integer  The command exit code
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->ioStyle->title('SIMON Data Submission');

        // Check configuration
        $apiUrl = DataHelper::getApiUrl();
        $authKey = DataHelper::getAuthKey();

        if (empty($apiUrl) || empty($authKey)) {
            $this->ioStyle->error('SIMON API URL or Auth Key not configured. Please configure in Component Settings.');
            return 1;
        }

        // Get client and site IDs
        $params = DataHelper::getParams();
        $clientId = $params->get('client_id');
        $siteId = $params->get('site_id');

        if (empty($clientId) || empty($siteId)) {
            $this->ioStyle->error('Client ID or Site ID not configured. Please configure in Component Settings.');
            return 1;
        }

        $this->ioStyle->text('Collecting site data...');

        // Collect data
        $siteData = DataHelper::collectSiteData();
        $config = Factory::getConfig();
        $app = Factory::getApplication();
        $authKey = DataHelper::getAuthKey();

        $payload = array_merge([
            'client_id' => (int) $clientId,
            'site_id' => (int) $siteId,
            'auth_key' => $authKey,
            'application_type' => 'joomla',
            'site' => [
                'name' => $config->get('sitename'),
                'url' => $app->get('uri')->base(),
                'application_type' => 'joomla',
            ],
        ], $siteData);

        $this->ioStyle->text('Submitting to SIMON API...');

        // Submit to API
        $response = DataHelper::submitToApi('intake', $payload);

        if ($response) {
            $this->ioStyle->success('Site data submitted successfully!');
            return 0;
        }

        $this->ioStyle->error('Failed to submit site data. Check logs for details.');
        return 1;
    }
}
