# TYPO3 Extension: LLMs.txt Generator

Automatically generates `llms.txt` files for TYPO3 websites to optimize them for Large Language Model consumption.

## Features

- **Automatic Generation**: Creates llms.txt files automatically from your TYPO3 content
- **Multi-language Support**: Generates separate files for each language
- **Multi-site Support**: Works with TYPO3 multi-site installations
- **Content Filtering**: Choose which pages and content types to include
- **Backend Module**: User-friendly interface for configuration and manual generation
- **Scheduler Integration**: Automate generation via TYPO3 Scheduler
- **CLI Commands**: Generate files via command line

## Installation

### Composer Installation

```bash
composer require dkd/llms-txt-generator
```

### Extension Manager

1. Download the extension from TER or GitHub
2. Install via Extension Manager
3. Configure extension settings

## Configuration

### Extension Configuration

Configure basic settings in the Extension Manager:

- **Site Title**: Default title for llms.txt
- **Site Description**: Default description
- **Include Page Types**: Which page types to include (default: 1)
- **Max Content Length**: Maximum content length per page
- **Enable Multi-language**: Generate separate files for each language

### TypoScript Configuration

Include static template and adjust settings:

```typoscript
plugin.tx_llmstxtgenerator.settings {
    siteTitle = Your Site Title
    siteDescription = Your site description
    includedPageTypes = 1,4
    maxContentLength = 10000
}
```

## Usage

### Backend Module

Access the module under **Admin Tools > LLMs.txt Generator**:

1. View all configured sites
2. Generate llms.txt for individual sites or all sites
3. Preview generated content before saving
4. Configure site-specific settings

### CLI Commands

```bash
# Generate for all sites
./vendor/bin/typo3 llms:generate

# Generate for specific site
./vendor/bin/typo3 llms:generate --site=main

# Generate full-content version
./vendor/bin/typo3 llms:generate --full
```

### Scheduler Task

1. Create new Scheduler task of type "LLMs.txt Generator Task"
2. Configure site and full version options
3. Set execution frequency

## Output

The extension generates files in your site root:

- `llms.txt` - Default file for main language
- `llms-de.txt` - German version
- `llms-en.txt` - English version
- `llms-full.txt` - Full content version (if enabled)

## Development

### Requirements

- TYPO3 11.5 or 12.4
- PHP 7.4 or higher

### Testing

```bash
composer test
composer test:unit
composer test:functional
```

## License

GPL-2.0-or-later