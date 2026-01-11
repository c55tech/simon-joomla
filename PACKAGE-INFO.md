# SIMON Joomla Package Information

## Package Created

**File:** `pkg_simon.zip`  
**Size:** ~48KB  
**Location:** `modules/simon-joomla/pkg_simon.zip`

## Package Contents

The package includes:

1. **Component** (`com_simon`)
   - All component files including:
     - SQL files: `sql/install.mysql.utf8.sql` and `sql/uninstall.mysql.utf8.sql`
     - Admin files, controllers, models, views, templates
     - Site files
     - CLI command

2. **System Plugin** (`plg_system_simon`)
   - Plugin files for automatic cron submission

3. **CLI Command** (`cli_simon`)
   - CLI command for manual data submission

## Installation

### Method 1: Package Installation (Recommended)

1. **Upload Package:**
   - Go to: **Extensions → Manage → Install**
   - Click **Upload Package File** tab
   - Select `pkg_simon.zip`
   - Click **Upload & Install**

2. **Verify Installation:**
   - Go to: **Extensions → Manage → Extensions**
   - Filter by **Component** - should see "SIMON"
   - Filter by **Plugin** - should see "System - SIMON"
   - Both should be **Enabled**

3. **Enable Plugin:**
   - Go to: **Extensions → Plugins**
   - Search for "SIMON"
   - Enable it if not already enabled

### Method 2: Manual Installation

If package installation fails, use manual installation:

1. Extract the package
2. Copy `packages/com_simon` to `/administrator/components/com_simon`
3. Copy `packages/plg_system_simon` to `/plugins/system/simon`
4. Copy `packages/cli_simon/simon.php` to `/cli/simon.php`
5. Go to **Extensions → Manage → Discover**
6. Click **Discover** and install the extensions

## Troubleshooting SQL File Error

If you see the error:
```
JInstaller: :Install: SQL File not found [ROOT]/administrator/components/com_simon/sql/install.mysql.utf8.sql
```

### Solution 1: Verify Package Contents

Check that SQL files are in the package:
```bash
unzip -l pkg_simon.zip | grep "sql/install"
```

Should show:
```
packages/com_simon/sql/install.mysql.utf8.sql
packages/com_simon/sql/uninstall.mysql.utf8.sql
```

### Solution 2: Manual File Check

After installation, verify SQL files exist:
```bash
ls -la /path/to/joomla/administrator/components/com_simon/sql/
```

Should show both `install.mysql.utf8.sql` and `uninstall.mysql.utf8.sql`

### Solution 3: Recreate Package

If SQL files are missing, recreate the package:
```bash
cd modules/simon-joomla
./create-package.sh
```

### Solution 4: Manual SQL File Copy

If package installation fails, manually copy SQL files:
```bash
# Extract package
unzip pkg_simon.zip -d /tmp/simon_pkg

# Copy SQL files manually
cp -r /tmp/simon_pkg/packages/com_simon/sql /path/to/joomla/administrator/components/com_simon/
```

## Package Structure

```
pkg_simon.zip
├── pkg_simon.xml (package manifest)
└── packages/
    ├── com_simon/ (component)
    │   ├── simon.xml (component manifest)
    │   ├── sql/
    │   │   ├── install.mysql.utf8.sql
    │   │   └── uninstall.mysql.utf8.sql
    │   ├── admin/
    │   ├── site/
    │   └── cli/
    ├── plg_system_simon/ (plugin)
    └── cli_simon/ (CLI command)
```

## SQL Files

The SQL files are **required** by Joomla's installer, even though they contain only comments (no actual table creation). The component uses Joomla's component parameters for configuration, so no custom tables are needed.

**File:** `sql/install.mysql.utf8.sql`
```sql
-- SIMON Component Installation SQL
-- This component uses Joomla component parameters for configuration
-- No custom database tables are required
```

**File:** `sql/uninstall.mysql.utf8.sql`
```sql
-- SIMON Component Uninstallation SQL
-- This component uses Joomla component parameters for configuration
-- No custom database tables need to be dropped
```

## Verification

After installation, verify:

1. **Component is registered:**
   - **Extensions → Manage → Extensions**
   - Filter by Component
   - Find "SIMON" - should be Enabled

2. **Plugin is enabled:**
   - **Extensions → Plugins**
   - Search for "SIMON"
   - Should be enabled

3. **Component is accessible:**
   - **Components → SIMON**
   - Should see menu items: Dashboard, Settings, Client, Site

4. **SQL files exist:**
   ```bash
   ls -la administrator/components/com_simon/sql/
   ```

## Recreating the Package

To recreate the package with latest changes:

```bash
cd modules/simon-joomla
./create-package.sh
```

The script will:
1. Clean up old package files
2. Copy component (excluding test files)
3. Copy plugin
4. Create CLI package
5. Create ZIP archive
6. Clean up temporary files

## Notes

- Test files (`test*.php`, `check-installation.php`) are automatically excluded from the package
- The package includes all necessary files for installation
- SQL files are required even though they're empty (Joomla requirement)
- Media files are included in the component structure

