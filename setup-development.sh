#!/bin/bash

# TYPO3 + DDEV Development Setup Script for LLMs.txt Generator Extension
# Usage: ./setup-development.sh [project-name]

PROJECT_NAME=${1:-"typo3-llms-dev"}
EXTENSION_PATH=$(pwd)

echo "🚀 Setting up TYPO3 development environment for llms_txt_generator extension"
echo "Project name: $PROJECT_NAME"
echo "Extension path: $EXTENSION_PATH"

# Create project directory
echo "📁 Creating project directory..."
mkdir -p "../$PROJECT_NAME"
cd "../$PROJECT_NAME"

# Copy project files
echo "📋 Copying configuration files..."
cp "$EXTENSION_PATH/typo3-project-composer.json" "./composer.json"
mkdir -p .ddev
cp "$EXTENSION_PATH/.ddev/config.yaml" "./.ddev/config.yaml"

# Update project name in DDEV config
sed -i.bak "s/typo3-llms-dev/$PROJECT_NAME/g" .ddev/config.yaml
rm .ddev/config.yaml.bak

# Create symlink to extension
echo "🔗 Creating extension symlink..."
mkdir -p public/typo3conf/ext
ln -sf "$EXTENSION_PATH" "./llms_txt_generator"

# Start DDEV
echo "🐳 Starting DDEV..."
ddev start

# Install TYPO3
echo "📦 Installing TYPO3..."
ddev composer install

# Enable first install
echo "🔧 Enabling TYPO3 installation..."
ddev exec touch public/FIRST_INSTALL

# Install extension via composer (using path repository)
echo "🧩 Installing llms_txt_generator extension..."
ddev composer require dkd/llms-txt-generator

echo "✅ Setup complete!"
echo ""
echo "Next steps:"
echo "1. Open your browser and go to: $(ddev describe | grep 'Primary URL' | awk '{print $3}')"
echo "2. Complete the TYPO3 installation wizard"
echo "3. Activate the llms_txt_generator extension:"
echo "   ddev exec vendor/bin/typo3 extension:activate llms_txt_generator"
echo "4. Access the extension module at: Admin Tools > LLMs.txt Generator"
echo ""
echo "Development commands:"
echo "- Generate llms.txt: ddev exec vendor/bin/typo3 llms:generate"
echo "- Clear caches: ddev exec vendor/bin/typo3 cache:flush"
echo "- View logs: ddev logs"
echo ""
echo "Happy developing! 🎉"