<?php
declare(strict_types=1);

namespace Dkd\LlmsTxtGenerator\Service;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteConfigurationService
{
    protected ExtensionConfiguration $extensionConfiguration;
    protected Registry $registry;
    protected SiteFinder $siteFinder;

    public function __construct(
        ExtensionConfiguration $extensionConfiguration,
        Registry $registry,
        SiteFinder $siteFinder
    ) {
        $this->extensionConfiguration = $extensionConfiguration;
        $this->registry = $registry;
        $this->siteFinder = $siteFinder;
    }

    public function getConfigurationForSite(?string $siteIdentifier = null): array
    {
        // Get global extension configuration
        $globalConfig = $this->extensionConfiguration->get('llms_txt_generator');
        
        // Get site-specific configuration from registry
        $siteConfig = [];
        if ($siteIdentifier) {
            $siteConfig = $this->registry->get('llms_txt_generator', 'site_' . $siteIdentifier, []);
        }

        // Merge configurations (site-specific overrides global)
        return array_merge($globalConfig, $siteConfig);
    }

    public function saveSettings(array $settings, ?string $siteIdentifier = null): void
    {
        if ($siteIdentifier) {
            $this->registry->set('llms_txt_generator', 'site_' . $siteIdentifier, $settings);
        } else {
            // Save global settings
            foreach ($settings as $key => $value) {
                $this->extensionConfiguration->set('llms_txt_generator', $key, $value);
            }
        }
    }

    public function getAllSites(): array
    {
        $sites = [];
        $allSites = $this->siteFinder->getAllSites();

        foreach ($allSites as $site) {
            $sites[] = [
                'identifier' => $site->getIdentifier(),
                'base' => (string)$site->getBase(),
                'rootPageId' => $site->getRootPageId(),
                'languages' => $this->getLanguagesForSite($site),
                'configuration' => $this->getConfigurationForSite($site->getIdentifier()),
                'lastGenerated' => $this->getLastGeneratedTime($site->getIdentifier()),
            ];
        }

        return $sites;
    }

    protected function getLanguagesForSite($site): array
    {
        $languages = [];
        foreach ($site->getLanguages() as $language) {
            $languages[] = [
                'languageId' => $language->getLanguageId(),
                'title' => $language->getTitle(),
                'locale' => $language->getLocale(),
                'iso' => $language->getTwoLetterIsoCode(),
            ];
        }
        return $languages;
    }

    protected function getLastGeneratedTime(string $siteIdentifier): ?int
    {
        return $this->registry->get('llms_txt_generator', 'last_generated_' . $siteIdentifier);
    }

    public function setLastGeneratedTime(string $siteIdentifier): void
    {
        $this->registry->set('llms_txt_generator', 'last_generated_' . $siteIdentifier, time());
    }

    public function getSettings(): array
    {
        return $this->extensionConfiguration->get('llms_txt_generator');
    }
}