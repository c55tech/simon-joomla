# SIMON Component Installation Checklist

## ✅ Pre-Installation Verification

All files are confirmed to exist:
- ✓ Manifest file (simon.xml)
- ✓ Entry point (admin/simon.php)
- ✓ Service provider (admin/services/provider.php)
- ✓ Extension class (admin/src/Extension/SimonComponent.php)
- ✓ Controllers, Views, Templates

## 📋 Installation Steps

### Step 1: Verify Component Location
The component should be at:
```
/var/www/html/administrator/components/com_simon/
```

Verify with:
```bash
ddev ssh
ls -la /var/www/html/administrator/components/com_simon/
```

You should see:
- `simon.xml` (manifest)
- `admin/` folder
- `sql/` folder
- `script.php`

### Step 2: Discover the Component

1. **Login to Joomla Admin**
   - Go to: `https://simon-joomla.ddev.site:8443/administrator`

2. **Navigate to Discover**
   - Go to: **Extensions → Manage → Discover**
   - Or direct URL: `https://simon-joomla.ddev.site:8443/administrator/index.php?option=com_installer&view=discover`

3. **Run Discovery**
   - Click the **Discover** button in the top toolbar
   - Wait for the scan to complete (may take 10-30 seconds)

4. **Check Results**
   - Look for "SIMON" or "com_simon" in the list
   - It should show as "Component" type
   - Status should be "Not Installed"

### Step 3: Install the Component

1. **Select the Component**
   - Check the box next to SIMON

2. **Install**
   - Click **Install** button in the top toolbar
   - Wait for installation to complete
   - You should see a success message

### Step 4: Verify Installation

1. **Check Extensions List**
   - Go to: **Extensions → Manage → Extensions**
   - Filter by **Component**
   - Search for "simon"
   - Verify it shows as **Enabled**

2. **Check Menu**
   - Look for **Components → SIMON** in the admin menu
   - You should see submenu items:
     - Dashboard
     - Settings
     - Client
     - Site

### Step 5: Clear Cache

1. **Clear System Cache**
   - Go to: **System → Clear Cache**
   - Or click the cache icon in the toolbar
   - Select "All" and click "Delete"

### Step 6: Test Access

Try accessing:
```
https://simon-joomla.ddev.site:8443/administrator/index.php?option=com_simon&view=dashboard
```

Or use the menu: **Components → SIMON → Dashboard**

## 🔧 Troubleshooting

### If Discover Doesn't Find the Component

1. **Check File Permissions**
   ```bash
   ddev ssh
   ls -la /var/www/html/administrator/components/com_simon/simon.xml
   ```
   Should be readable (644 or 755)

2. **Verify Manifest Location**
   The `simon.xml` must be in:
   ```
   /var/www/html/administrator/components/com_simon/simon.xml
   ```
   NOT in the admin folder!

3. **Check Manifest Format**
   - Open: `/var/www/html/administrator/components/com_simon/simon.xml`
   - Verify it's valid XML
   - Check that `<extension type="component">` is correct

4. **Manual Discovery Refresh**
   - Go to: **Extensions → Manage → Discover**
   - Click **Purge Cache** (if available)
   - Click **Discover** again

### If Component Installs But Shows 404

1. **Check if Enabled**
   - Go to: **Extensions → Manage → Extensions**
   - Find SIMON
   - Ensure it's **Enabled** (toggle if needed)

2. **Verify Entry Point**
   - Check: `/var/www/html/administrator/components/com_simon/admin/simon.php` exists
   - Should be readable

3. **Check Service Provider**
   - Verify: `/var/www/html/administrator/components/com_simon/admin/services/provider.php` exists

4. **Clear All Caches**
   - System → Clear Cache
   - Clear browser cache
   - Try incognito/private browsing

### If You See "Component not found" Error

This usually means:
- Component not registered in database → Use Discover
- Component disabled → Enable in Extensions manager
- Entry point missing → Verify `admin/simon.php` exists
- Service provider error → Check PHP error logs

## 📝 Quick Verification Commands

```bash
# SSH into DDEV
ddev ssh

# Check component structure
ls -la /var/www/html/administrator/components/com_simon/
ls -la /var/www/html/administrator/components/com_simon/admin/

# Check if manifest is valid XML
php -l /var/www/html/administrator/components/com_simon/simon.xml

# Check Joomla error logs
tail -f /var/www/html/administrator/logs/error.php
```

## ✅ Success Indicators

You'll know it's working when:
- ✓ Component appears in **Extensions → Manage → Extensions**
- ✓ **Components → SIMON** appears in admin menu
- ✓ Dashboard view loads without 404
- ✓ No errors in Joomla logs

