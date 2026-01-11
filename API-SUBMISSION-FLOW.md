# SIMON Joomla Component - API Submission Flow

## Current Implementation Status

### ✅ What Works

1. **Site Details (Snapshot Data) Submission** - ✅ **IMPLEMENTED**
   - **When:** Automatic (via plugin) or manual (via CLI)
   - **Endpoint:** `POST /api/intake`
   - **How:** Uses `DataHelper::submitToApi('intake', $payload)`
   - **Location:** `plg_system_simon/simon.php` and `cli/simon.php`

### ❌ What's Missing

1. **Client Information Submission** - ❌ **NOT IMPLEMENTED**
   - **Should submit to:** `POST /api/clients`
   - **Current behavior:** Only saves to component parameters
   - **Missing:** API submission when client form is saved

2. **Site Information Submission** - ❌ **NOT IMPLEMENTED**
   - **Should submit to:** `POST /api/sites`
   - **Current behavior:** Only saves to component parameters
   - **Missing:** API submission when site form is saved

---

## Current Flow

### 1. Client Information (Currently Only Local Storage)

**User Action:**
1. User fills out Client form (Components → SIMON → Client)
2. Clicks "Save"

**What Happens:**
```
User Input → ClientController::save()
  → ClientModel::save()
    → Saves to component parameters (client_name, client_contact_name, etc.)
    → NO API SUBMISSION
```

**Storage Location:**
- `#__extensions.params` (JSON)
- Parameters: `client_name`, `client_contact_name`, `client_contact_email`, `client_notes`, `client_status`

**Missing:**
- API call to `POST /api/clients`
- Storing returned `client_id`
- Error handling for API failures

---

### 2. Site Information (Currently Only Local Storage)

**User Action:**
1. User fills out Site form (Components → SIMON → Site)
2. Clicks "Save"

**What Happens:**
```
User Input → SiteController::save()
  → SiteModel::save()
    → Saves to component parameters (site_name, site_url, etc.)
    → NO API SUBMISSION
```

**Storage Location:**
- `#__extensions.params` (JSON)
- Parameters: `site_name`, `site_url`, `site_external_id`, `site_auth_token`, `site_notes`, `site_status`

**Missing:**
- API call to `POST /api/sites`
- Requires `client_id` (must be created first)
- Storing returned `site_id`
- Error handling for API failures

---

### 3. Site Details (Snapshot Data) - ✅ **WORKING**

**Automatic Submission (Plugin):**
```
Frontend Page Load → plg_system_simon::onAfterRender()
  → Checks if cron enabled and interval passed
    → submitData()
      → DataHelper::collectSiteData()
      → DataHelper::submitToApi('intake', $payload)
        → POST /api/intake
```

**Manual Submission (CLI):**
```
php cli/joomla.php simon:submit
  → DataHelper::collectSiteData()
  → DataHelper::submitToApi('intake', $payload)
    → POST /api/intake
```

**Payload Structure:**
```json
{
  "client_id": 123,
  "site_id": 456,
  "cms_type": "joomla",
  "site_name": "My Joomla Site",
  "site_url": "https://example.com",
  "data": {
    "core": { "version": "4.4.0", "status": "up-to-date" },
    "log_summary": { "total": 150, "error": 5, "warning": 20 },
    "environment": { "php_version": "8.1.0", ... },
    "extensions": [...],
    "themes": [...]
  }
}
```

---

## What Needs to Be Added

### 1. Client API Submission

**Add to `ClientModel::save()`:**

```php
public function save($data)
{
    // ... existing save to params code ...
    
    // Submit to SIMON API
    $apiData = [
        'name' => $data['name'] ?? '',
        'contact_name' => $data['contact_name'] ?? '',
        'contact_email' => $data['contact_email'] ?? '',
        'notes' => $data['notes'] ?? '',
    ];
    
    $response = DataHelper::submitToApi('clients', $apiData);
    
    if ($response && isset($response->client_id)) {
        // Store client_id for future use
        $params->set('client_id', $response->client_id);
        $table->params = (string) $params;
        $table->store();
    }
    
    return true;
}
```

**API Endpoint:** `POST /api/clients`
**Response:** `{ "success": true, "client_id": 123, "client": {...} }`

---

### 2. Site API Submission

**Add to `SiteModel::save()`:**

```php
public function save($data)
{
    // ... existing save to params code ...
    
    // Get client_id (required for site creation)
    $clientId = $params->get('client_id');
    
    if (empty($clientId)) {
        $this->setError('Client ID is required. Please create a client first.');
        return false;
    }
    
    // Submit to SIMON API
    $apiData = [
        'client_id' => (int) $clientId,
        'cms' => 'joomla',
        'name' => $data['name'] ?? '',
        'url' => $data['url'] ?? '',
        'external_id' => $data['external_id'] ?? '',
        'auth_token' => $data['auth_token'] ?? '',
        'notes' => $data['notes'] ?? '',
    ];
    
    $response = DataHelper::submitToApi('sites', $apiData);
    
    if ($response && isset($response->site_id)) {
        // Store site_id for future use
        $params->set('site_id', $response->site_id);
        $table->params = (string) $params;
        $table->store();
    }
    
    return true;
}
```

**API Endpoint:** `POST /api/sites`
**Response:** `{ "success": true, "site_id": 456, "site": {...} }`

---

## Complete Flow (After Implementation)

### Step 1: Configure Settings
1. User enters API URL and Auth Key
2. Saved to component parameters

### Step 2: Create Client
1. User fills Client form
2. Clicks "Save"
3. **Local Save:** Data saved to component parameters
4. **API Submission:** `POST /api/clients` with client data
5. **Store ID:** `client_id` saved to component parameters
6. User sees success message with Client ID

### Step 3: Create Site
1. User fills Site form
2. Clicks "Save"
3. **Validation:** Checks if `client_id` exists
4. **Local Save:** Data saved to component parameters
5. **API Submission:** `POST /api/sites` with site data (includes `client_id`)
6. **Store ID:** `site_id` saved to component parameters
7. User sees success message with Site ID

### Step 4: Automatic Data Submission
1. Plugin runs on frontend page loads
2. Checks if cron enabled and interval passed
3. **Collects Data:** `DataHelper::collectSiteData()`
4. **API Submission:** `POST /api/intake` with snapshot data
5. Uses stored `client_id` and `site_id`

---

## Data Storage Summary

### Component Parameters Structure

```json
{
  "api_url": "http://localhost:3000",
  "auth_key": "secret-key",
  "enable_cron": 1,
  "cron_interval": 3600,
  "last_submission": 1234567890,
  
  "client_id": 123,
  "client_name": "Acme Corp",
  "client_contact_name": "John Smith",
  "client_contact_email": "john@acme.com",
  "client_notes": "Notes here",
  "client_status": 1,
  
  "site_id": 456,
  "site_name": "Main Website",
  "site_url": "https://example.com",
  "site_external_id": "ext-123",
  "site_auth_token": "token-123",
  "site_notes": "Site notes",
  "site_status": 1
}
```

---

## API Endpoints Used

1. **`POST /api/clients`** - Create/update client
   - **When:** Client form saved
   - **Auth:** `X-Auth-Key` header
   - **Returns:** `client_id`

2. **`POST /api/sites`** - Create/update site
   - **When:** Site form saved
   - **Auth:** `X-Auth-Key` header (client's auth key)
   - **Requires:** `client_id` in payload
   - **Returns:** `site_id`

3. **`POST /api/intake`** - Submit site snapshot data
   - **When:** Automatic (plugin) or manual (CLI)
   - **Auth:** `X-Auth-Key` header
   - **Requires:** `client_id` and `site_id` in payload
   - **Payload:** Full site data snapshot

---

## Current Limitations

1. ❌ Client data is saved locally but never sent to SIMON API
2. ❌ Site data is saved locally but never sent to SIMON API
3. ❌ No `client_id` or `site_id` stored (required for intake submissions)
4. ❌ Intake submissions will fail without `client_id` and `site_id`

---

## Recommended Implementation

The component should be updated to:

1. **Submit client data to API** when Client form is saved
2. **Store `client_id`** from API response
3. **Submit site data to API** when Site form is saved (requires `client_id`)
4. **Store `site_id`** from API response
5. **Show user-friendly messages** with IDs after successful API submission
6. **Handle API errors gracefully** (show error messages, don't break save)

This matches the Drupal module's behavior where data is saved locally AND submitted to the API.

