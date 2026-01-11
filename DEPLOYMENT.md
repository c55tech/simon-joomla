# SIMON Joomla Component Deployment Guide

## Overview

The SIMON Joomla integration consists of:
- **Component** (`com_simon`) - Main admin interface
- **System Plugin** (`plg_system_simon`) - Automatic cron submission
- **CLI Command** (`cli/simon`) - Manual submission command

## Deployment Methods

There are **two ways** to deploy the SIMON component to Joomla:

1. **Package Installation** (Recommended) - Single ZIP file installs everything
2. **Manual Installation** - Copy files and use Joomla's Discover feature

---

## Method 1: Package Installation (Recommended)

### Step 1: Create the Package

The package structure should be:

```
pkg_simon.zip
├── pkg_simon.xml          (package manifest)
└── packages/
    ├── com_simon/         (component)
    │   ├── simon.xml
    │   ├── admin/
    │   ├── site/
    │   ├── sql/
    │   └── script.php
    ├── plg_system_simon/  (system plugin)
    │   ├── simon.xml
    │   └── simon.php
    └── cli_simon/         (CLI command)
        └── simon.php
```

**To create the package:**

1. **Create package directory structure:**
   ```bash
   cd modules/simon-joomla
   mkdir -p pkg_simon/packages
   ```

2. **Copy component:**
   ```bash
   cp -r com_simon pkg_simon/packages/
   ```

3. **Copy plugin:**
   ```bash
   cp -r plg_system_simon pkg_simon/packages/
   ```

4. **Create CLI package:**
   ```bash
   mkdir -p pkg_simon/packages/cli_simon
   cp com_simon/cli/simon.php pkg_simon/packages/cli_simon/
   # Create CLI manifest (see below)
   ```

5. **Copy package manifest:**
   ```bash
   cp simon.xml pkg_simon/pkg_simon.xml
   ```

6. **Create ZIP:**
   ```bash
   cd pkg_simon
   zip -r ../pkg_simon.zip .
   cd ..
   ```

### Step 2: Install the Package

1. **Login to Joomla Admin**
   - Navigate to: `https://your-site.com/administrator`

2. **Go to Extension Manager**
   - **Extensions → Manage → Install**

3. **Upload Package**
   - Click **Upload Package File** tab
   - Click **Choose File** and select `pkg_simon.zip`
   - Click **Upload & Install**

4. **Verify Installation**
   - Go to **Extensions → Manage → Extensions**
   - Filter by **Component** - should see "SIMON"
   - Filter by **Plugin** - should see "System - SIMON"
   - Both should be **Enabled**

5. **Enable the Plugin** (if not auto-enabled)
   - Go to **Extensions → Plugins**
   - Search for "SIMON"
   - Click the status icon to enable it

---

## Method 2: Manual Installation

### Step 1: Copy Files to Joomla

**For DDEV environment:**
```bash
# SSH into DDEV
ddev ssh

# Copy component
cp -r /var/www/html/modules/simon-joomla/com_simon /var/www/html/administrator/components/

# Copy plugin
cp -r /var/www/html/modules/simon-joomla/plg_system_simon /var/www/html/plugins/system/

# Copy CLI command
mkdir -p /var/www/html/cli
cp /var/www/html/modules/simon-joomla/com_simon/cli/simon.php /var/www/html/cli/simon.php
```

**For standard Joomla installation:**
```bash
# Component
cp -r com_simon /path/to/joomla/administrator/components/

# Plugin
cp -r plg_system_simon /path/to/joomla/plugins/system/

# CLI
mkdir -p /path/to/joomla/cli
cp com_simon/cli/simon.php /path/to/joomla/cli/simon.php
```

### Step 2: Discover Extensions

1. **Login to Joomla Admin**
   - Navigate to: `https://your-site.com/administrator`

2. **Go to Discover**
   - **Extensions → Manage → Discover**

3. **Run Discovery**
   - Click **Discover** button in toolbar
   - Wait for scan to complete

4. **Install Discovered Extensions**
   - Check boxes next to:
     - **SIMON** (Component)
     - **System - SIMON** (Plugin)
   - Click **Install** button

5. **Enable the Plugin**
   - Go to **Extensions → Plugins**
   - Search for "SIMON"
   - Enable it

### Step 3: Clear Cache

1. **System → Clear Cache**
   - Select **All**
   - Click **Delete**

2. **Or via command line:**
   ```bash
   ddev ssh
   rm -rf /var/www/html/cache/*
   rm -rf /var/www/html/administrator/cache/*
   rm -f /var/www/html/cache/autoload_psr4.php
   rm -f /var/www/html/administrator/cache/autoload_psr4.php
   ```

---

## Post-Installation Steps

### 1. Verify Installation

**Check Component:**
- Go to **Components → SIMON**
- Should see menu items: Dashboard, Settings, Client, Site

**Check Plugin:**
- Go to **Extensions → Plugins**
- Search for "SIMON"
- Should be enabled

**Check CLI:**
```bash
php cli/joomla.php simon:submit
# Should show error about missing config (this is expected)
```

### 2. Configure Component

1. **Navigate to Settings**
   - **Components → SIMON → Settings**

2. **Enter Configuration:**
   - **API URL**: `http://localhost:3000` (or your SIMON API URL)
   - **Auth Key**: Your SIMON authentication key
   - **Enable Cron**: Yes (if you want automatic submission)
   - **Cron Interval**: 3600 (seconds, default: 1 hour)

3. **Save**

### 3. Create Client and Site

1. **Create Client:**
   - **Components → SIMON → Client**
   - Fill in client information
   - Click **Create/Update Client**
   - Note the **Client ID**

2. **Create Site:**
   - **Components → SIMON → Site**
   - Fill in site information
   - Click **Create/Update Site**
   - Note the **Site ID**

### 4. Test Submission

```bash
php cli/joomla.php simon:submit
```

Should see: "Site data submitted successfully!"

---

## File Structure Reference

### Component Structure
```
administrator/components/com_simon/
├── simon.xml                    # Component manifest
├── script.php                   # Installation script
├── sql/
│   ├── install.mysql.utf8.sql
│   └── uninstall.mysql.utf8.sql
├── admin/
│   ├── simon.php                # Admin entry point
│   ├── access.xml
│   ├── config.xml
│   ├── services/
│   │   └── provider.php         # Service provider
│   ├── src/
│   │   ├── Extension/
│   │   │   └── SimonComponent.php
│   │   ├── Controller/
│   │   ├── Model/
│   │   ├── View/
│   │   └── Helper/
│   ├── forms/
│   └── tmpl/
└── site/
    ├── simon.php                # Site entry point
    └── src/
        └── Controller/
```

### Plugin Structure
```
plugins/system/simon/
├── simon.xml                    # Plugin manifest
└── simon.php                    # Plugin class
```

### CLI Structure
```
cli/
└── simon.php                    # CLI command
```

---

## Troubleshooting

### Component Not Found After Installation

1. **Check if registered:**
   - Go to **Extensions → Manage → Extensions**
   - Filter by Component
   - Search for "simon"
   - Should show as Enabled

2. **Clear all caches:**
   ```bash
   ddev ssh
   rm -rf /var/www/html/cache/*
   rm -rf /var/www/html/administrator/cache/*
   rm -f /var/www/html/cache/autoload_psr4.php
   rm -f /var/www/html/administrator/cache/autoload_psr4.php
   ```

3. **Re-discover:**
   - **Extensions → Manage → Discover**
   - Click **Discover** again

### Plugin Not Working

1. **Check if enabled:**
   - **Extensions → Plugins**
   - Search for "SIMON"
   - Enable if disabled

2. **Check plugin order:**
   - Ensure it's not being blocked by another plugin

3. **Check logs:**
   - **System → Information → Log Files**
   - Look for errors in `simon` category

### CLI Command Not Found

1. **Verify file exists:**
   ```bash
   ls -la /var/www/html/cli/simon.php
   ```

2. **Check permissions:**
   ```bash
   chmod 644 /var/www/html/cli/simon.php
   ```

3. **Test from Joomla root:**
   ```bash
   cd /var/www/html
   php cli/joomla.php simon:submit
   ```

---

## Creating CLI Manifest (for Package)

If creating a package, you need a CLI manifest file:

**File:** `packages/cli_simon/simon.xml`

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="file" version="4.0" method="upgrade">
    <name>SIMON CLI Command</name>
    <version>1.0.0</version>
    <creationDate>2024-01-01</creationDate>
    <author>SIMON Team</author>
    <copyright>Copyright (C) 2024 SIMON Team. All rights reserved.</copyright>
    <license>GPL-2.0-or-later</license>
    <description>SIMON CLI command for manual data submission</description>
    
    <files>
        <filename>simon.php</filename>
    </files>
</extension>
```

---

## Quick Deployment Script

Save this as `create-package.sh`:

```bash
#!/bin/bash

# Create package directory
mkdir -p pkg_simon/packages
cd pkg_simon

# Copy component
cp -r ../com_simon packages/

# Copy plugin
cp -r ../plg_system_simon packages/

# Create CLI package
mkdir -p packages/cli_simon
cp ../com_simon/cli/simon.php packages/cli_simon/

# Copy package manifest
cp ../simon.xml pkg_simon.xml

# Create ZIP
cd ..
zip -r pkg_simon.zip pkg_simon/

echo "Package created: pkg_simon.zip"
```

Make executable and run:
```bash
chmod +x create-package.sh
./create-package.sh
```

