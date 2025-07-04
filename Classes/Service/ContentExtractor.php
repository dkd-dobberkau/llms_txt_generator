<?php
declare(strict_types=1);

namespace Dkd\LlmsTxtGenerator\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class ContentExtractor
{
    protected PageRepository $pageRepository;
    protected ConnectionPool $connectionPool;
    protected array $configuration;

    public function __construct(
        PageRepository $pageRepository,
        ConnectionPool $connectionPool,
        array $configuration = []
    ) {
        $this->pageRepository = $pageRepository;
        $this->connectionPool = $connectionPool;
        $this->configuration = $configuration;
    }

    public function extractPageContent(int $pageId, int $languageId = 0): array
    {
        $page = $this->pageRepository->getPage($pageId, true);
        
        if (empty($page)) {
            return [];
        }

        $content = [
            'title' => $page['title'] ?? '',
            'description' => $page['description'] ?? '',
            'keywords' => $page['keywords'] ?? '',
            'abstract' => $page['abstract'] ?? '',
            'content' => $this->getPageContentElements($pageId, $languageId),
            'url' => $this->buildPageUrl($pageId, $languageId),
        ];

        return $content;
    }

    protected function getPageContentElements(int $pageId, int $languageId): string
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        
        $constraints = [
            $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pageId, \PDO::PARAM_INT)),
            $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($languageId, \PDO::PARAM_INT)),
        ];

        if (!empty($this->configuration['include_content_types'])) {
            $types = GeneralUtility::trimExplode(',', $this->configuration['include_content_types'], true);
            $constraints[] = $queryBuilder->expr()->in('CType', $queryBuilder->createNamedParameter($types, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY));
        }

        $result = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(...$constraints)
            ->orderBy('sorting')
            ->execute();

        $content = [];
        while ($row = $result->fetchAssociative()) {
            $content[] = $this->processContentElement($row);
        }

        return implode("\n\n", array_filter($content));
    }

    protected function processContentElement(array $element): string
    {
        $processedContent = '';

        switch ($element['CType']) {
            case 'text':
            case 'textpic':
            case 'textmedia':
                $processedContent = $element['bodytext'] ?? '';
                break;
            case 'bullets':
                $processedContent = $this->processBulletList($element['bodytext'] ?? '');
                break;
            case 'table':
                $processedContent = $this->processTable($element['bodytext'] ?? '');
                break;
            case 'html':
                $processedContent = strip_tags($element['bodytext'] ?? '');
                break;
            default:
                // Handle other content types as needed
                break;
        }

        if (!empty($element['header'])) {
            $processedContent = $element['header'] . "\n" . $processedContent;
        }

        return trim($processedContent);
    }

    protected function processBulletList(string $content): string
    {
        $lines = GeneralUtility::trimExplode("\n", $content, true);
        return implode("\n", array_map(function ($line) {
            return '- ' . $line;
        }, $lines));
    }

    protected function processTable(string $content): string
    {
        // Simple table processing - can be enhanced
        return str_replace(['<table>', '</table>', '<tr>', '</tr>', '<td>', '</td>'], '', $content);
    }

    protected function buildPageUrl(int $pageId, int $languageId): string
    {
        // This is a simplified version - in production, use proper URL generation
        return '/page-' . $pageId . ($languageId > 0 ? '-lang-' . $languageId : '');
    }

    public function getAllPages(int $rootPageId = 0, array $excludePageUids = []): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        
        $constraints = [
            $queryBuilder->expr()->eq('deleted', 0),
            $queryBuilder->expr()->eq('hidden', 0),
        ];

        if (!empty($this->configuration['include_page_types'])) {
            $types = GeneralUtility::trimExplode(',', $this->configuration['include_page_types'], true);
            $constraints[] = $queryBuilder->expr()->in('doktype', $queryBuilder->createNamedParameter($types, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
        }

        if (!empty($excludePageUids)) {
            $constraints[] = $queryBuilder->expr()->notIn('uid', $queryBuilder->createNamedParameter($excludePageUids, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
        }

        $result = $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(...$constraints)
            ->orderBy('sorting')
            ->execute();

        $pages = [];
        while ($row = $result->fetchAssociative()) {
            $pages[] = $row;
        }

        return $pages;
    }
}