# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a TYPO3 extension project called `llms_txt_generator` that automatically generates `llms.txt` files for TYPO3 websites to optimize them for Large Language Model consumption.

## Development Commands

### TYPO3 CLI Commands (once implemented)
```bash
# Generate llms.txt for all sites
./vendor/bin/typo3 llms:generate

# Generate for specific site
./vendor/bin/typo3 llms:generate --site=1

# Generate full-content version
./vendor/bin/typo3 llms:generate --full
```

### Installation
```bash
composer require vendor/llms-txt-generator
```

## Project Architecture

### Extension Structure
```
llms_txt_generator/
├── Classes/
│   ├── Controller/
│   │   └── LlmsBackendController.php      # Backend module controller
│   ├── Service/
│   │   ├── ContentExtractor.php           # Extracts content from TYPO3 pages
│   │   ├── LlmsGenerator.php              # Core generation logic
│   │   └── SiteConfigurationService.php   # Handles multi-site configurations
│   ├── Task/
│   │   └── GenerateLlmsTask.php           # Scheduler task for automation
│   └── ViewHelpers/                       # Fluid ViewHelpers
├── Configuration/
│   ├── Backend/                           # Backend module configuration
│   ├── TCA/                               # Table Configuration Array
│   └── TypoScript/                        # TypoScript configuration
├── Resources/
│   ├── Private/
│   │   ├── Templates/                     # Fluid templates
│   │   ├── Partials/                      # Reusable template parts
│   │   └── Layouts/                       # Template layouts
│   └── Public/                            # Public assets
├── ext_emconf.php                         # Extension configuration
└── ext_conf_template.txt                  # Extension settings template
```

### Key Components

1. **ContentExtractor Service**: Responsible for extracting content from TYPO3 pages, handling content elements, and respecting visibility settings.

2. **LlmsGenerator Service**: Core service that orchestrates the generation process, applies templates, and handles multi-language support.

3. **SiteConfigurationService**: Manages site-specific configurations, domain handling, and language versions.

4. **Backend Module**: Provides admin interface for configuration, preview, and manual generation triggers.

## TYPO3 Development Standards

### Namespace Convention
```php
namespace Vendor\LlmsTxtGenerator\{Component};
```

### Service Registration
Services should be registered in `Configuration/Services.yaml` using TYPO3's dependency injection.

### TypoScript Configuration
```typoscript
plugin.tx_llmstxtgenerator {
    settings {
        siteTitle = {$site.title}
        siteDescription = {$site.description}
        includedPageTypes = 1,4
        maxContentLength = 10000
        outputFormat = markdown
    }
}
```

### Backend Module Registration
Register backend modules in `ext_tables.php` following TYPO3 conventions.

## Key Features to Implement

1. **Multi-language Support**: Generate separate llms.txt files for each language (llms-de.txt, llms-en.txt)

2. **Content Filtering**: 
   - Page type filtering (exclude sysfolders)
   - Category-based selection
   - Priority system for pages
   - Include/exclude lists

3. **Automation**:
   - Scheduler task for automatic regeneration
   - Hooks for content update triggers
   - Cache integration

4. **Template System**: Use Fluid templates for customizable output format

## Development Guidelines

### TYPO3 Best Practices
- Follow TYPO3 Core APIs for database queries (use QueryBuilder)
- Use TYPO3's caching framework for performance
- Implement proper access control for backend module
- Use TYPO3's logging framework for error handling

### Extension Configuration
- Store configuration in `ext_conf_template.txt`
- Use TypoScript for frontend-related settings
- Implement FlexForms for content element configuration if needed

### Testing Considerations
- Test with multiple TYPO3 sites configuration
- Verify language handling works correctly
- Test with large page trees for performance
- Ensure proper handling of access-restricted pages