# TYPO3 Extension Development Setup with DDEV

This guide shows how to set up a TYPO3 development environment using DDEV to develop and test the `llms_txt_generator` extension.

## Prerequisites

- [DDEV](https://ddev.readthedocs.io/en/stable/#installation) installed
- [Docker](https://www.docker.com/get-started) installed
- Basic knowledge of TYPO3 and Composer

## Step-by-Step Setup

### 1. Create TYPO3 Project Directory

```bash
mkdir typo3-llms-dev
cd typo3-llms-dev
```

### 2. Initialize DDEV

```bash
ddev config --project-type=typo3 --docroot=public --create-docroot
```

### 3. Create TYPO3 Composer Project

Create a `composer.json` file in the project root:

```json
{
    "name": "dkd/typo3-llms-dev",
    "type": "project",
    "description": "TYPO3 development environment for LLMs.txt Generator extension",
    "license": "GPL-2.0-or-later",
    "require": {
        "typo3/cms-core": "^12.4",
        "typo3/cms-backend": "^12.4",
        "typo3/cms-frontend": "^12.4",
        "typo3/cms-extbase": "^12.4",
        "typo3/cms-fluid": "^12.4",
        "typo3/cms-fluid-styled-content": "^12.4",
        "typo3/cms-scheduler": "^12.4",
        "typo3/cms-install": "^12.4"
    },
    "require-dev": {
        "typo3/testing-framework": "^8.0",
        "phpunit/phpunit": "^10.0"
    },
    "config": {
        "vendor-dir": ".Build/vendor",
        "bin-dir": ".Build/bin",
        "allow-plugins": {
            "typo3/cms-composer-installers": true,
            "typo3/class-alias-loader": true
        }
    },
    "extra": {
        "typo3/cms": {
            "web-dir": "public"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "TYPO3\\\\CMS\\\\Composer\\\\Plugin\\\\Core\\\\InstallerScriptsRegistration::register",
            "TYPO3\\\\CMS\\\\Composer\\\\Plugin\\\\Tx_Extensionmanager_Utility_InstallUtility::install"
        ]
    }
}
```

### 4. Start DDEV and Install TYPO3

```bash
# Start DDEV
ddev start

# Install TYPO3 via Composer
ddev composer install

# Create TYPO3 database
ddev exec touch public/FIRST_INSTALL
```

### 5. Install Your Extension

There are two ways to add your extension:

#### Option A: Local Development (Recommended)

Link your extension directory:

```bash
# Create extensions directory
mkdir -p public/typo3conf/ext

# Symlink your extension
ln -s /path/to/your/llms_txt_generator public/typo3conf/ext/llms_txt_generator

# Or copy the extension
cp -r /path/to/your/llms_txt_generator public/typo3conf/ext/
```

#### Option B: Composer Development

Add to your main `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/path/to/your/llms_txt_generator"
        }
    ],
    "require": {
        "dkd/llms-txt-generator": "*"
    }
}
```

Then run:
```bash
ddev composer require dkd/llms-txt-generator
```

### 6. Complete TYPO3 Setup

1. Open your browser and go to the DDEV URL (shown after `ddev start`)
2. Follow the TYPO3 installation wizard
3. Create an admin user
4. Choose "Empty Site" or import a site package

### 7. Activate Your Extension

```bash
# Via CLI (recommended)
ddev exec vendor/bin/typo3 extension:activate llms_txt_generator

# Or via Backend: Extensions > Installed Extensions > Activate
```

### 8. Configure Extension

1. Go to Admin Tools > Settings > Extension Configuration
2. Find "llms_txt_generator" and configure settings
3. Or go to Admin Tools > LLMs.txt Generator (your new module)

## Development Workflow

### Testing the Extension

```bash
# Generate llms.txt via CLI
ddev exec vendor/bin/typo3 llms:generate

# Run tests (when implemented)
ddev exec vendor/bin/phpunit -c public/typo3conf/ext/llms_txt_generator/Tests/Build/UnitTests.xml
```

### Debugging

```bash
# Check TYPO3 logs
ddev exec tail -f var/log/typo3_*.log

# Access database
ddev mysql

# SSH into container
ddev ssh
```

### Development Commands

```bash
# Clear all caches
ddev exec vendor/bin/typo3 cache:flush

# Show scheduler tasks
ddev exec vendor/bin/typo3 scheduler:list

# Run specific scheduler task
ddev exec vendor/bin/typo3 scheduler:run
```

## File Structure

After setup, your project should look like:

```
typo3-llms-dev/
├── .ddev/
│   └── config.yaml
├── public/
│   ├── typo3conf/
│   │   └── ext/
│   │       └── llms_txt_generator/  # Your extension
│   ├── typo3/
│   └── index.php
├── var/
├── .Build/
└── composer.json
```

## Troubleshooting

### Common Issues

1. **Extension not found**: Check if the extension is in the correct directory
2. **Database connection**: Ensure DDEV is running (`ddev start`)
3. **Permissions**: Check file permissions in the extension directory
4. **Cache issues**: Clear TYPO3 caches (`ddev exec vendor/bin/typo3 cache:flush`)

### DDEV Commands

```bash
# Restart DDEV
ddev restart

# Stop DDEV
ddev stop

# View project info
ddev describe

# View logs
ddev logs
```

## Next Steps

1. Create sample pages and content in TYPO3
2. Test the extension functionality
3. Implement the business logic in the service classes
4. Add unit and functional tests
5. Create documentation

## Resources

- [DDEV Documentation](https://ddev.readthedocs.io/)
- [TYPO3 Documentation](https://docs.typo3.org/)
- [TYPO3 Extension Development](https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ExtensionArchitecture/Index.html)