# Joomla Component 404 Debugging Guide

## Quick Debug Steps

### 1. Enable Error Reporting
1. Go to **System → Global Configuration**
2. **Server** tab → Set **Error Reporting** to **Maximum**
3. **System** tab → Enable **Debug System** → **Yes**
4. Save

### 2. Check Browser Console
- Press **F12** to open Developer Tools
- Check **Console** tab for JavaScript errors
- Check **Network** tab for failed requests (look for 404s)

### 3. Check File Locations
Verify these files exist in your Joomla installation:

```
/administrator/components/com_simon/
├── admin/
│   ├── simon.php                    ← Entry point (REQUIRED)
│   ├── services/
│   │   └── provider.php             ← Service provider (REQUIRED)
│   ├── src/
│   │   ├── Controller/
│   │   │   ├── DisplayController.php
│   │   │   └── SettingsController.php
│   │   ├── Extension/
│   │   │   └── SimonComponent.php   ← Extension class (REQUIRED)
│   │   └── View/
│   │       ├── Dashboard/
│   │       ├── Settings/
│   │       ├── Client/
│   │       └── Site/
│   └── tmpl/
│       ├── dashboard/
│       ├── settings/
│       ├── client/
│       └── site/
└── simon.xml                         ← Manifest (REQUIRED)
```

### 4. Common Issues

#### Issue: "404 Component not found"
**Possible causes:**
- Entry point file (`admin/simon.php`) missing or incorrect
- Service provider not registered
- Extension class not found
- Namespace mismatch

**Fix:**
1. Verify `admin/simon.php` exists and is readable
2. Check `admin/services/provider.php` is correct
3. Verify namespace in `simon.xml` matches code: `Joomla\Component\Simon`
4. Clear Joomla cache: **System → Clear Cache**

#### Issue: "Class not found"
**Fix:**
1. Reinstall component
2. Check file permissions (should be 644 for files, 755 for directories)
3. Verify all files were copied correctly

### 5. Manual Component Check

Create a test file to verify component is accessible:

**File:** `/administrator/components/com_simon/test.php`
```php
<?php
defined('_JEXEC') or die;
echo "Component files are accessible!";
```

Then visit: `yoursite.com/administrator/components/com_simon/test.php`

If you see the message, files are accessible. If 404, files aren't in the right place.

### 6. Check Component Registration

1. Go to **Extensions → Manage → Extensions**
2. Filter by **Component**
3. Look for **SIMON** in the list
4. Check if it shows as **Enabled**

### 7. Reinstall Steps

1. **Uninstall** the component (if installed)
2. **Delete** the folder: `/administrator/components/com_simon/`
3. **Clear cache**: System → Clear Cache
4. **Reinstall** via Discover or manual copy
5. **Enable** the component
6. **Clear cache** again

### 8. Check Logs Directly

If you have SSH/terminal access:
```bash
# Find Joomla root
cd /path/to/joomla

# Check for error logs
ls -la administrator/logs/
ls -la logs/

# View recent errors
tail -f administrator/logs/error.php
```

### 9. Test URL Structure

Try accessing the component directly:
- `yoursite.com/administrator/index.php?option=com_simon`
- `yoursite.com/administrator/index.php?option=com_simon&view=dashboard`

If these work, the component is loading but routing might be the issue.

### 10. Verify Manifest

Check `simon.xml` has correct structure:
- `<namespace path="src">Joomla\Component\Simon</namespace>`
- Entry point listed: `<filename>simon.php</filename>`
- Service provider folder: `<folder>services</folder>`

