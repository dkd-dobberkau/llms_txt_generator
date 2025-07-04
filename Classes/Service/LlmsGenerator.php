<?php
declare(strict_types=1);

namespace Dkd\LlmsTxtGenerator\Service;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class LlmsGenerator
{
    protected ContentExtractor $contentExtractor;
    protected SiteConfigurationService $siteConfigurationService;
    protected CacheManager $cacheManager;
    protected ExtensionConfiguration $extensionConfiguration;
    protected SiteFinder $siteFinder;

    public function __construct(
        ContentExtractor $contentExtractor,
        SiteConfigurationService $siteConfigurationService,
        CacheManager $cacheManager,
        ExtensionConfiguration $extensionConfiguration,
        SiteFinder $siteFinder
    ) {
        $this->contentExtractor = $contentExtractor;
        $this->siteConfigurationService = $siteConfigurationService;
        $this->cacheManager = $cacheManager;
        $this->extensionConfiguration = $extensionConfiguration;
        $this->siteFinder = $siteFinder;
    }

    public function generate(?string $siteIdentifier = null, bool $fullVersion = false): array
    {
        $results = [];
        
        if ($siteIdentifier) {
            $site = $this->siteFinder->getSiteByIdentifier($siteIdentifier);
            $results[$siteIdentifier] = $this->generateForSite($site, $fullVersion);
        } else {
            $sites = $this->siteFinder->getAllSites();
            foreach ($sites as $site) {
                $results[$site->getIdentifier()] = $this->generateForSite($site, $fullVersion);
            }
        }

        return $results;
    }

    protected function generateForSite(Site $site, bool $fullVersion): array
    {
        $configuration = $this->siteConfigurationService->getConfigurationForSite($site->getIdentifier());
        $languages = $site->getLanguages();
        $results = [];

        foreach ($languages as $language) {
            $content = $this->generateContent($site, $language->getLanguageId(), $configuration, $fullVersion);
            $filename = $this->getFilename($language->getLanguageId(), $fullVersion);
            $this->writeFile($site->getBase()->getPath() . $filename, $content);
            $results[$language->getLanguageId()] = [
                'filename' => $filename,
                'size' => strlen($content),
                'pages' => substr_count($content, "\n## "),
            ];
        }

        return $results;
    }

    protected function generateContent(Site $site, int $languageId, array $configuration, bool $fullVersion): string
    {
        $cacheIdentifier = md5($site->getIdentifier() . '_' . $languageId . '_' . ($fullVersion ? 'full' : 'index'));
        $cache = $this->cacheManager->getCache('llms_txt_generator');

        if ($configuration['enable_caching'] && $cache->has($cacheIdentifier)) {
            return $cache->get($cacheIdentifier);
        }

        $rootPageId = $site->getRootPageId();
        $excludePageUids = GeneralUtility::intExplode(',', $configuration['exclude_page_uids'] ?? '', true);
        
        $pages = $this->contentExtractor->getAllPages($rootPageId, $excludePageUids);
        $contentData = [
            'site' => [
                'title' => $configuration['site_title'] ?? $site->getConfiguration()['websiteTitle'] ?? '',
                'description' => $configuration['site_description'] ?? '',
                'url' => (string)$site->getBase(),
            ],
            'pages' => [],
            'fullVersion' => $fullVersion,
        ];

        foreach ($pages as $page) {
            $pageContent = $this->contentExtractor->extractPageContent((int)$page['uid'], $languageId);
            
            if ($fullVersion || $this->shouldIncludeInIndex($page, $configuration)) {
                $contentData['pages'][] = $pageContent;
            }
        }

        $content = $this->renderTemplate($contentData, $configuration);

        if ($configuration['enable_caching']) {
            $cache->set($cacheIdentifier, $content, [], 86400); // Cache for 24 hours
        }

        return $content;
    }

    protected function shouldIncludeInIndex(array $page, array $configuration): bool
    {
        // Implement logic to determine if page should be in index version
        // For now, include all pages
        return true;
    }

    protected function renderTemplate(array $data, array $configuration): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        
        if (!empty($configuration['custom_template_path'])) {
            $view->setTemplatePathAndFilename($configuration['custom_template_path']);
        } else {
            $view->setTemplateRootPaths(['EXT:llms_txt_generator/Resources/Private/Templates/']);
            $view->setTemplate('Llms/Default');
        }

        $view->assignMultiple($data);
        return $view->render();
    }

    protected function getFilename(int $languageId, bool $fullVersion): string
    {
        $filename = 'llms';
        
        if ($languageId > 0) {
            // Add language ISO code - simplified version
            $filename .= '-' . $this->getLanguageIsoCode($languageId);
        }
        
        if ($fullVersion) {
            $filename .= '-full';
        }
        
        return $filename . '.txt';
    }

    protected function getLanguageIsoCode(int $languageId): string
    {
        // Simplified - in production, get from site configuration
        $languageMap = [
            1 => 'de',
            2 => 'fr',
            3 => 'es',
        ];
        
        return $languageMap[$languageId] ?? 'en';
    }

    protected function writeFile(string $path, string $content): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            GeneralUtility::mkdir_deep($directory);
        }

        file_put_contents($path, $content);
    }

    public function preview(?string $siteIdentifier = null): string
    {
        $configuration = $this->siteConfigurationService->getConfigurationForSite($siteIdentifier);
        $site = $siteIdentifier ? $this->siteFinder->getSiteByIdentifier($siteIdentifier) : $this->siteFinder->getAllSites()[0];
        
        return $this->generateContent($site, 0, $configuration, false);
    }
}