# SIMON Joomla Component

Joomla component and plugin for integrating with the SIMON monitoring system.

## Quick Start

**For detailed deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md)**

## Installation

### Via Composer (Recommended)

Add the repository to your `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/c55tech/simon-joomla"
    }
  ],
  "require": {
    "simon/integration": "dev-main"
  }
}
```

Then install:

```bash
composer require simon/integration:dev-main
```

### Package Installation

1. **Package Installation:**
   - Download the `pkg_simon.zip` package
   - Go to **Extensions → Manage → Install**
   - Upload and install the package

2. **Manual Installation:**
   - Copy the component to: `/administrator/components/com_simon`
   - Copy the plugin to: `/plugins/system/simon`
   - Copy the CLI to: `/cli/simon`
   - Go to **Extensions → Manage → Discover**
   - Click **Discover** to find new extensions
   - Install the discovered extensions

## Configuration

### Step 1: Configure Settings

1. Navigate to: **Components → SIMON → Settings**
2. Enter:
   - **API URL**: Base URL of your SIMON API (e.g., `http://localhost:3000`)
   - **Auth Key**: Your SIMON authentication key
   - **Enable Cron**: Check to enable automatic submission
   - **Cron Interval**: Time between submissions (in seconds, default: 3600)
3. Click **Save**

### Step 2: Create Client

1. Navigate to: **Components → SIMON → Client**
2. Fill in:
   - Client Name (required)
   - Contact Name (optional)
   - Contact Email (optional)
3. Click **Create/Update Client**
4. Note the Client ID

### Step 3: Create Site

1. Navigate to: **Components → SIMON → Site**
2. Fill in:
   - Site Name
   - Site URL
   - External ID (optional)
3. Click **Create/Update Site**
4. Note the Site ID

### Step 4: Test Submission

Use the CLI command to test:

```bash
php cli/joomla.php simon:submit
```

Or enable cron and wait for automatic submission.

## CLI Command

Submit site data manually via CLI:

```bash
php cli/joomla.php simon:submit
```

## Cron Integration

If enabled, the plugin automatically submits site data when Joomla cron runs, based on the configured interval.

## What Data is Collected

The component collects and submits:

- **Core**: Joomla version and update status
- **Log Summary**: Error/warning counts from log entries
- **Environment**: PHP version, database info, web server
- **Extensions**: All installed components, modules, and plugins with versions
- **Templates**: All installed templates with versions

## Requirements

- Joomla 4.0 or higher
- PHP 7.4 or higher
- cURL extension enabled

## Troubleshooting

- Check Joomla logs: **System → Information → Log Files**
- Verify API URL is accessible from your Joomla server
- Ensure Client ID and Site ID are configured
- Test with CLI command: `php cli/joomla.php simon:submit`
