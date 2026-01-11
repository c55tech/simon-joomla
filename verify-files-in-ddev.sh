#!/bin/bash
# Script to verify component files are in DDEV

echo "Checking if component files exist in DDEV..."
echo ""

ddev ssh -s << 'EOF'
echo "=== Checking component directory ==="
ls -la /var/www/html/administrator/components/com_simon/ 2>&1
echo ""

echo "=== Checking admin directory ==="
ls -la /var/www/html/administrator/components/com_simon/admin/ 2>&1
echo ""

echo "=== Checking service provider ==="
ls -la /var/www/html/administrator/components/com_simon/admin/services/provider.php 2>&1
echo ""

echo "=== Checking entry point ==="
ls -la /var/www/html/administrator/components/com_simon/admin/simon.php 2>&1
echo ""

echo "=== Checking Extension class ==="
ls -la /var/www/html/administrator/components/com_simon/admin/src/Extension/SimonComponent.php 2>&1
echo ""

echo "=== Checking manifest ==="
ls -la /var/www/html/administrator/components/com_simon/simon.xml 2>&1
echo ""

echo "=== Testing PHP syntax (if files exist) ==="
if [ -f /var/www/html/administrator/components/com_simon/admin/services/provider.php ]; then
    php -l /var/www/html/administrator/components/com_simon/admin/services/provider.php
fi

if [ -f /var/www/html/administrator/components/com_simon/admin/simon.php ]; then
    php -l /var/www/html/administrator/components/com_simon/admin/simon.php
fi

if [ -f /var/www/html/administrator/components/com_simon/admin/src/Extension/SimonComponent.php ]; then
    php -l /var/www/html/administrator/components/com_simon/admin/src/Extension/SimonComponent.php
fi
EOF

