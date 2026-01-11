#!/bin/bash
# Script to clear all Joomla caches that might prevent component from loading

echo "Clearing all Joomla caches..."

ddev ssh << 'EOF'
cd /var/www/html

echo "1. Clearing cache directory..."
rm -rf cache/*
rm -rf administrator/cache/*

echo "2. Clearing autoload caches (CRITICAL)..."
rm -f cache/autoload_psr4.php
rm -f administrator/cache/autoload_psr4.php

echo "3. Clearing tmp directory..."
rm -rf tmp/*

echo "4. Clearing fof (if exists)..."
rm -rf cache/fof/* 2>/dev/null || true

echo "5. Setting proper permissions..."
chmod -R 755 cache
chmod -R 755 administrator/cache
chmod -R 755 tmp

echo "✓ All caches cleared!"
echo ""
echo "Next steps:"
echo "1. Go to Joomla admin: Extensions → Manage → Extensions"
echo "2. Find SIMON component"
echo "3. Uninstall it"
echo "4. Go to: Extensions → Manage → Discover"
echo "5. Click Discover"
echo "6. Install SIMON again"
echo "7. Clear cache via System → Clear Cache"
EOF

echo ""
echo "Done! Now follow the steps above to reinstall the component."



