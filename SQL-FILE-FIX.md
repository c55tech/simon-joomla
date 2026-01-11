# SQL File Installation Error - Troubleshooting

## Error
```
JInstaller: :Install: SQL File not found [ROOT]/administrator/components/com_simon/sql/install.mysql.utf8.sql
```

## Current Status

The package has been updated with:
1. SQL files explicitly listed in root-level `<files>` section
2. Install/uninstall SQL sections in manifest
3. SQL files included in package at `packages/com_simon/sql/`

## Package Structure
```
pkg_simon.zip
├── pkg_simon.xml
└── packages/
    └── com_simon/
        ├── simon.xml (component manifest)
        └── sql/
            ├── install.mysql.utf8.sql
            └── uninstall.mysql.utf8.sql
```

## Manifest Configuration

The component manifest (`simon.xml`) now includes:

```xml
<files>
    <folder>sql</folder>
</files>

<install>
    <sql>
        <file driver="mysql" charset="utf8">sql/install.mysql.utf8.sql</file>
    </sql>
</install>

<uninstall>
    <sql>
        <file driver="mysql" charset="utf8">sql/uninstall.mysql.utf8.sql</file>
    </sql>
</uninstall>
```

## If Error Persists

### Option 1: Manual Installation
1. Extract `pkg_simon.zip`
2. Copy `packages/com_simon` to `/administrator/components/com_simon`
3. Manually create the `sql` directory if needed:
   ```bash
   mkdir -p /path/to/joomla/administrator/components/com_simon/sql
   cp packages/com_simon/sql/*.sql /path/to/joomla/administrator/components/com_simon/sql/
   ```
4. Go to **Extensions → Manage → Discover**
5. Click **Discover** and install

### Option 2: Remove SQL Requirements
Since the SQL files are empty (only comments), you can remove the install/uninstall SQL sections from the manifest. The component will still work as it uses component parameters, not custom tables.

### Option 3: Verify Package Contents
```bash
unzip -l pkg_simon.zip | grep sql
```

Should show:
```
packages/com_simon/sql/install.mysql.utf8.sql
packages/com_simon/sql/uninstall.mysql.utf8.sql
```

### Option 4: Check File Permissions
After installation, verify SQL files exist and are readable:
```bash
ls -la /path/to/joomla/administrator/components/com_simon/sql/
chmod 644 /path/to/joomla/administrator/components/com_simon/sql/*.sql
```

## Note

The SQL files contain only comments (no actual SQL statements) because the component uses Joomla's component parameters for configuration and doesn't require custom database tables. The files are required by Joomla's installer structure, but the component will function even if the SQL execution fails (since there's no SQL to execute).

