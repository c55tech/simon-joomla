# How SIMON Joomla Component Submits Data

## Overview

The Joomla component submits site data to the SIMON API in three ways:

1. **Automatic (via System Plugin)** - Runs on cron/after page render
2. **Manual (via CLI)** - Command line submission
3. **Programmatic** - Can be called from other code

## Data Submission Flow

### 1. Data Collection (`DataHelper::collectSiteData()`)

The component collects the following data:

- **Core Information:**
  - Joomla version
  - Update status (up-to-date/outdated)

- **Log Summary:**
  - Total log entries (last 24 hours)
  - Error count
  - Warning count

- **Environment:**
  - PHP version
  - Memory limit
  - Max execution time
  - Web server type
  - Database type and version
  - PHP modules/extensions

- **Extensions:**
  - All components, modules, and plugins
  - Version numbers
  - Enabled/disabled status
  - Custom vs. core identification

- **Templates:**
  - All installed templates
  - Version numbers
  - Active/inactive status

### 2. API Submission (`DataHelper::submitToApi()`)

**Endpoint:** `{API_URL}/api/intake`

**Method:** POST

**Headers:**
- `Content-Type: application/json`
- `X-Auth-Key: {auth_key}`

**Payload Structure:**
```json
{
  "client_id": 123,
  "site_id": 456,
  "cms_type": "joomla",
  "site_name": "My Joomla Site",
  "site_url": "https://example.com",
  "data": {
    "core": {
      "version": "4.4.0",
      "status": "up-to-date"
    },
    "log_summary": {
      "total": 150,
      "error": 5,
      "warning": 20
    },
    "environment": {
      "php_version": "8.1.0",
      "memory_limit": "256M",
      "database_type": "mysql",
      "database_version": "8.0.0"
    },
    "extensions": [...],
    "themes": [...]
  }
}
```

## Submission Methods

### Method 1: Automatic (System Plugin)

**File:** `plg_system_simon/simon.php`

**How it works:**
1. Plugin listens to `onAfterRender` event
2. Only runs on frontend (site application)
3. Checks if cron is enabled in component settings
4. Checks if enough time has passed since last submission (based on `cron_interval`)
5. Collects data and submits to API
6. Updates `last_submission` timestamp

**Configuration:**
- Enable in: **Components → SIMON → Settings**
- Set `enable_cron` to Yes
- Set `cron_interval` (seconds, default: 3600 = 1 hour)

**Note:** This runs on every page load (frontend only) but only submits if the interval has passed.

### Method 2: Manual (CLI Command)

**File:** `com_simon/cli/simon.php`

**Usage:**
```bash
php cli/joomla.php simon:submit
```

**What it does:**
1. Checks if API URL and Auth Key are configured
2. Checks if Client ID and Site ID are configured
3. Collects all site data
4. Submits to SIMON API
5. Shows success/error message

**Use cases:**
- Testing the integration
- Manual data submission
- Scheduled cron jobs
- Troubleshooting

### Method 3: Programmatic

You can call it from other Joomla code:

```php
use Joomla\Component\Simon\Administrator\Helper\DataHelper;

// Collect data
$siteData = DataHelper::collectSiteData();

// Submit to API
$payload = [
    'client_id' => 123,
    'site_id' => 456,
    'cms_type' => 'joomla',
    'site_name' => 'My Site',
    'site_url' => 'https://example.com',
    'data' => $siteData,
];

$response = DataHelper::submitToApi('intake', $payload);
```

## Configuration Required

Before data can be submitted, you must configure:

1. **API Settings** (Components → SIMON → Settings):
   - `api_url` - Base URL of SIMON API (e.g., `http://localhost:3000`)
   - `auth_key` - Authentication key from SIMON

2. **Client and Site IDs**:
   - `client_id` - Client ID from SIMON
   - `site_id` - Site ID from SIMON

## Error Handling

- All errors are logged to Joomla's log system
- Log category: `simon`
- Check logs: **System → Information → Log Files**

Common errors:
- API URL or Auth Key not configured
- Client ID or Site ID missing
- Network/connection errors
- API returned error status

## API Response

On success, the API should return a JSON response (typically the created/updated snapshot).

On failure:
- Returns `false`
- Logs error to Joomla logs
- Plugin/CLI shows error message

