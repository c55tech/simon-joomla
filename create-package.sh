#!/bin/bash

# SIMON Joomla Package Creation Script
# Creates pkg_simon.zip for Joomla installation

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

echo "Creating SIMON Joomla package..."

# Clean up any existing package directory
rm -rf pkg_simon
rm -f pkg_simon.zip

# Create package directory structure
mkdir -p pkg_simon/packages

# Copy component (excluding test files)
echo "Copying component..."
rsync -av --exclude='test*.php' --exclude='check-installation.php' --exclude='*.sh' com_simon/ pkg_simon/packages/com_simon/ 2>/dev/null || \
(cp -r com_simon pkg_simon/packages/ && \
find pkg_simon/packages/com_simon -name 'test*.php' -delete && \
find pkg_simon/packages/com_simon -name 'check-installation.php' -delete && \
find pkg_simon/packages/com_simon -name '*.sh' -delete)

# Copy plugin
echo "Copying plugin..."
cp -r plg_system_simon pkg_simon/packages/

# Create CLI package
echo "Creating CLI package..."
mkdir -p pkg_simon/packages/cli_simon
cp com_simon/cli/simon.php pkg_simon/packages/cli_simon/

# Create CLI manifest
cat > pkg_simon/packages/cli_simon/simon.xml << 'EOF'
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
EOF

# Copy package manifest
echo "Copying package manifest..."
cp simon.xml pkg_simon/pkg_simon.xml

# Create ZIP archive
echo "Creating ZIP archive..."
cd pkg_simon
zip -r ../pkg_simon.zip . -q
cd ..

# Clean up
rm -rf pkg_simon

echo ""
echo "✓ Package created successfully: pkg_simon.zip"
echo ""
echo "To install:"
echo "  1. Go to Joomla Admin → Extensions → Manage → Install"
echo "  2. Upload pkg_simon.zip"
echo "  3. Click 'Upload & Install'"
echo ""

