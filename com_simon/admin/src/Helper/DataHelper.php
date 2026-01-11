<?php
/**
 * @package     SIMON
 * @subpackage  com_simon
 *
 * @copyright   Copyright (C) 2024 SIMON Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Simon\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTTP\HttpFactory;
use Joomla\CMS\Log\Log;

/**
 * SIMON Helper class
 */
class DataHelper
{
    /**
     * Get component parameters
     *
     * @return  \Joomla\Registry\Registry
     */
    public static function getParams()
    {
        $app = Factory::getApplication();
        return $app->getParams('com_simon');
    }

    /**
     * Get API URL
     *
     * @return  string
     */
    public static function getApiUrl()
    {
        $params = self::getParams();
        return rtrim($params->get('api_url', ''), '/');
    }

    /**
     * Get Auth Key
     *
     * @return  string
     */
    public static function getAuthKey()
    {
        $params = self::getParams();
        return $params->get('auth_key', '');
    }

    /**
     * Submit data to SIMON API
     *
     * @param   string  $endpoint  API endpoint
     * @param   array   $data      Data to submit
     *
     * @return  object|false  Response object or false on failure
     */
    public static function submitToApi($endpoint, $data)
    {
        $apiUrl = self::getApiUrl();
        $authKey = self::getAuthKey();

        if (empty($apiUrl) || empty($authKey)) {
            Log::add('SIMON: API URL or Auth Key not configured', Log::ERROR, 'simon');
            return false;
        }

        try {
            $http = HttpFactory::getHttp();
            $url = $apiUrl . '/api/' . ltrim($endpoint, '/');
            
            $headers = [
                'Content-Type' => 'application/json',
                'X-Auth-Key' => $authKey,
            ];

            $response = $http->post($url, json_encode($data), $headers, 30);

            if ($response->code >= 200 && $response->code < 300) {
                return json_decode($response->body);
            }

            Log::add('SIMON API Error: ' . $response->code . ' - ' . $response->body, Log::ERROR, 'simon');
            return false;
        } catch (\Exception $e) {
            Log::add('SIMON API Exception: ' . $e->getMessage(), Log::ERROR, 'simon');
            return false;
        }
    }

    /**
     * Collect site data
     *
     * @return  array
     */
    public static function collectSiteData()
    {
        $app = Factory::getApplication();
        $data = [];

        // Core version
        $jversion = new \Joomla\CMS\Version();
        $data['core'] = [
            'version' => $jversion->getShortVersion(),
            'status' => self::getCoreStatus(),
        ];

        // Log summary
        $data['log_summary'] = self::getLogSummary();

        // Environment
        $data['environment'] = self::getEnvironment();

        // Extensions
        $data['extensions'] = self::getExtensions();

        // Templates
        $data['themes'] = self::getTemplates();

        return $data;
    }

    /**
     * Get core status
     *
     * @return  string
     */
    private static function getCoreStatus()
    {
        $jversion = new \Joomla\CMS\Version();
        $updateInfo = $jversion->getUpdateInformation();

        if (isset($updateInfo['hasUpdate']) && $updateInfo['hasUpdate']) {
            return 'outdated';
        }

        return 'up-to-date';
    }

    /**
     * Get log summary
     *
     * @return  array
     */
    private static function getLogSummary()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*) as total')
            ->select('SUM(CASE WHEN level = ' . $db->quote('ERROR') . ' THEN 1 ELSE 0 END) as error')
            ->select('SUM(CASE WHEN level = ' . $db->quote('WARNING') . ' THEN 1 ELSE 0 END) as warning')
            ->from($db->quoteName('#__log_entries'))
            ->where($db->quoteName('date') . ' > DATE_SUB(NOW(), INTERVAL 24 HOUR)');

        $db->setQuery($query);
        $result = $db->loadObject();

        return [
            'total' => (int) ($result->total ?? 0),
            'error' => (int) ($result->error ?? 0),
            'warning' => (int) ($result->warning ?? 0),
            'by_level' => [],
        ];
    }

    /**
     * Get environment information
     *
     * @return  array
     */
    private static function getEnvironment()
    {
        $config = Factory::getConfig();
        $db = Factory::getDbo();

        return [
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => (int) ini_get('max_execution_time'),
            'web_server' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'database_type' => $config->get('dbtype'),
            'database_version' => $db->getVersion(),
            'php_modules' => get_loaded_extensions(),
        ];
    }

    /**
     * Get installed extensions
     *
     * @return  array
     */
    private static function getExtensions()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('extension_id, name, element, type, enabled, manifest_cache')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' IN (' . $db->quote('component') . ', ' . $db->quote('module') . ', ' . $db->quote('plugin') . ')')
            ->where($db->quoteName('state') . ' = 0');

        $db->setQuery($query);
        $extensions = $db->loadObjectList();

        $result = [];
        foreach ($extensions as $ext) {
            $manifest = json_decode($ext->manifest_cache, true);
            $version = $manifest['version'] ?? '0.0.0';

            $result[] = [
                'type' => $ext->type === 'component' ? 'component' : ($ext->type === 'module' ? 'module' : 'plugin'),
                'machine_name' => $ext->element,
                'human_name' => $ext->name,
                'version' => $version,
                'status' => $ext->enabled ? 'enabled' : 'disabled',
                'is_custom' => strpos($ext->element, 'com_') === 0 && strpos($ext->element, 'com_simon') !== 0 ? true : false,
            ];
        }

        return $result;
    }

    /**
     * Get installed templates
     *
     * @return  array
     */
    private static function getTemplates()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('extension_id, name, element, enabled, manifest_cache')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('template'))
            ->where($db->quoteName('client_id') . ' = 0');

        $db->setQuery($query);
        $templates = $db->loadObjectList();

        $result = [];
        foreach ($templates as $template) {
            $manifest = json_decode($template->manifest_cache, true);
            $version = $manifest['version'] ?? '0.0.0';

            $result[] = [
                'machine_name' => $template->element,
                'human_name' => $template->name,
                'version' => $version,
                'status' => $template->enabled ? 'active' : 'inactive',
                'is_custom' => strpos($template->element, 'tpl_') === 0,
            ];
        }

        return $result;
    }
}
