<?php
declare(strict_types=1);

namespace Dkd\LlmsTxtGenerator\Task;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use Dkd\LlmsTxtGenerator\Service\LlmsGenerator;
use Dkd\LlmsTxtGenerator\Service\SiteConfigurationService;

class GenerateLlmsTask extends AbstractTask
{
    public string $siteIdentifier = '';
    public bool $generateFullVersion = false;

    public function execute(): bool
    {
        try {
            $llmsGenerator = GeneralUtility::makeInstance(LlmsGenerator::class);
            $siteConfigurationService = GeneralUtility::makeInstance(SiteConfigurationService::class);
            
            $siteIdentifier = $this->siteIdentifier ?: null;
            $results = $llmsGenerator->generate($siteIdentifier, $this->generateFullVersion);
            
            // Update last generated time
            foreach ($results as $site => $result) {
                $siteConfigurationService->setLastGeneratedTime($site);
            }
            
            return true;
        } catch (\Exception $e) {
            $this->logException($e);
            return false;
        }
    }

    protected function logException(\Exception $e): void
    {
        $logger = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)
            ->getLogger(__CLASS__);
        $logger->error('Failed to generate llms.txt', [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    public function getAdditionalInformation(): string
    {
        $info = [];
        
        if ($this->siteIdentifier) {
            $info[] = 'Site: ' . $this->siteIdentifier;
        } else {
            $info[] = 'All sites';
        }
        
        if ($this->generateFullVersion) {
            $info[] = 'Full version';
        }
        
        return implode(', ', $info);
    }
}