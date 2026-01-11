<?php
/**
 * @package     SIMON
 * @subpackage  plg_system_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Simon\Administrator\Helper\DataHelper;

defined('_JEXEC') or die;

/**
 * SIMON System Plugin
 *
 * Handles cron-based automatic data submission
 */
class PlgSystemSimon extends CMSPlugin
{
    /**
     * Application object
     *
     * @var    \Joomla\CMS\Application\CMSApplication
     */
    protected $app;

    /**
     * Load the language file on instantiation
     *
     * @var    boolean
     */
    protected $autoloadLanguage = true;

    /**
     * Constructor
     *
     * @param   object  &$subject  The object to observe
     * @param   array   $config    An optional associative array of configuration settings
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }

    /**
     * Listen to the onAfterRender event to handle cron submission
     *
     * @return  void
     */
    public function onAfterRender()
    {
        // Only run in site application
        if (!$this->app->isClient('site')) {
            return;
        }

        $params = DataHelper::getParams();

        // Check if cron is enabled
        if (!$params->get('enable_cron', 0)) {
            return;
        }

        // Check last submission time
        $lastSubmission = $params->get('last_submission', 0);
        $interval = (int) $params->get('cron_interval', 3600);
        $now = time();

        if (($now - $lastSubmission) < $interval) {
            return;
        }

        // Submit data
        $this->submitData();

        // Update last submission time
        $params->set('last_submission', $now);
    }

    /**
     * Submit site data to SIMON API
     *
     * @return  boolean
     */
    private function submitData()
    {
        $params = DataHelper::getParams();
        $clientId = $params->get('client_id');
        $siteId = $params->get('site_id');

        if (empty($clientId) || empty($siteId)) {
            return false;
        }

        // Collect data
        $siteData = DataHelper::collectSiteData();
        $config = Factory::getConfig();
        $app = Factory::getApplication();

        $payload = [
            'client_id' => (int) $clientId,
            'site_id' => (int) $siteId,
            'cms_type' => 'joomla',
            'site_name' => $config->get('sitename'),
            'site_url' => $app->get('uri')->base(),
            'data' => $siteData,
        ];

        // Submit to API
        $response = DataHelper::submitToApi('intake', $payload);

        return $response !== false;
    }
}
