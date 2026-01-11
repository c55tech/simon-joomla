# SIMON CLI Usage Guide

## How to Execute the CLI

### Method 1: Using npm Scripts (Recommended)

The easiest way is to use the npm scripts defined in `package.json`:

```bash
# General CLI access
npm run cli [command] [options]

# Specific commands with shortcuts
npm run cli:user:create
npm run cli:user:list
npm run cli:client:create
npm run cli:client:list
npm run cli:client:auth-key
npm run cli:client:show-auth-key
npm run cli:db
```

### Method 2: Using the Wrapper Script

You can use the `simon` wrapper script directly:

```bash
# From project root
./simon [command] [options]

# Examples
./simon user:list
./simon client:create --name "Acme Corp"
./simon client:auth-key -i 1
```

### Method 3: Direct DDEV Execution

Run directly via DDEV:

```bash
ddev exec "cd /var/www/html/backend && node simon-cli.js [command] [options]"
```

### Method 4: Direct Node.js (if not using DDEV)

If you have Node.js installed and `.env` configured:

```bash
cd backend
node simon-cli.js [command] [options]
```

## Available Commands

### User Management

```bash
# Create a new user
npm run cli user:create -- --email user@example.com --name "John Doe"

# List all users
npm run cli user:list

# Update user password
npm run cli user:update-password -- --email user@example.com

# Log out user(s)
npm run cli user:logout -- --email user@example.com

# Delete a user
npm run cli user:delete -- --email user@example.com
```

### Client Management

```bash
# Create a new client
npm run cli client:create -- --name "Acme Corporation" --contact-name "John Smith" --contact-email "john@acme.com"

# List all clients
npm run cli client:list

# Generate/update auth key for a client
npm run cli client:auth-key -- --id 1

# Show client auth key
npm run cli client:show-auth-key -- --id 1

# Delete a client
npm run cli client:delete -- --id 1
```

### Site Management

```bash
# Create a new site
npm run cli site:create -- --client-id 1 --cms drupal --name "Main Site" --url "https://example.com"

# List sites (all or for a specific client)
npm run cli site:list
npm run cli site:list -- --client-id 1

# Delete a site
npm run cli site:delete -- --id 1
```

### Auth Key Management

```bash
# Create a new auth key
npm run cli auth-key:create -- --name "Production Key" --max-sites 10

# List all auth keys
npm run cli auth-key:list

# Update an auth key
npm run cli auth-key:update -- --id 1 --notes "Updated notes"

# Retire an auth key
npm run cli auth-key:retire -- --id 1

# Associate auth key with client
npm run cli auth-key:associate -- --key-id 1 --client-id 1

# Disassociate auth key from client
npm run cli auth-key:disassociate -- --key-id 1 --client-id 1
```

### Database

```bash
# Open database console
npm run cli db:open
```

## Command Shortcuts

Many commands have shorter aliases:

- `user:create` → `ucrt`
- `user:list` → `ul`
- `client:create` → `ccrt`
- `client:list` → `cl`
- `client:auth-key` → `ca`
- `site:create` → `scrt`
- `site:list` → `sl`

## Examples

### Create a client and site

```bash
# 1. Create client
npm run cli client:create -- --name "Test Client" --contact-email "test@example.com"

# 2. Get the client ID from output, then create site
npm run cli site:create -- --client-id 1 --cms joomla --name "Test Site" --url "https://test.example.com"

# 3. Generate auth key for the client
npm run cli client:auth-key -- --id 1
```

### List all data

```bash
# List users
npm run cli user:list

# List clients
npm run cli client:list

# List sites for a client
npm run cli site:list -- --client-id 1
```

## Getting Help

To see all available commands and options:

```bash
npm run cli -- --help
```

To get help for a specific command:

```bash
npm run cli -- user:create --help
npm run cli -- client:create --help
```

## Notes

- All commands run inside the DDEV container by default
- Database connection is automatically configured for DDEV
- Commands that modify data will prompt for confirmation unless `--yes` flag is used
- Some commands require specific permissions or roles

