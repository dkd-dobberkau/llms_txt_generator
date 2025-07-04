<?php
declare(strict_types=1);

namespace Dkd\LlmsTxtGenerator\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Dkd\LlmsTxtGenerator\Service\LlmsGenerator;
use Dkd\LlmsTxtGenerator\Service\SiteConfigurationService;

class LlmsBackendController extends ActionController
{
    protected ModuleTemplateFactory $moduleTemplateFactory;
    protected LlmsGenerator $llmsGenerator;
    protected SiteConfigurationService $siteConfigurationService;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
        LlmsGenerator $llmsGenerator,
        SiteConfigurationService $siteConfigurationService
    ) {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->llmsGenerator = $llmsGenerator;
        $this->siteConfigurationService = $siteConfigurationService;
    }

    public function indexAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        
        $sites = $this->siteConfigurationService->getAllSites();
        $this->view->assign('sites', $sites);
        
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function generateAction(): ResponseInterface
    {
        $siteIdentifier = $this->request->getArgument('site') ?? null;
        
        try {
            $result = $this->llmsGenerator->generate($siteIdentifier);
            $this->addFlashMessage(
                'Successfully generated llms.txt',
                'Success',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
            );
        } catch (\Exception $e) {
            $this->addFlashMessage(
                'Error generating llms.txt: ' . $e->getMessage(),
                'Error',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        }
        
        return $this->redirect('index');
    }

    public function previewAction(): ResponseInterface
    {
        $siteIdentifier = $this->request->getArgument('site') ?? null;
        $content = $this->llmsGenerator->preview($siteIdentifier);
        
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->view->assign('content', $content);
        $this->view->assign('siteIdentifier', $siteIdentifier);
        
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function settingsAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        
        $settings = $this->siteConfigurationService->getSettings();
        $this->view->assign('settings', $settings);
        
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function saveAction(): ResponseInterface
    {
        $settings = $this->request->getArgument('settings');
        
        try {
            $this->siteConfigurationService->saveSettings($settings);
            $this->addFlashMessage(
                'Settings saved successfully',
                'Success',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
            );
        } catch (\Exception $e) {
            $this->addFlashMessage(
                'Error saving settings: ' . $e->getMessage(),
                'Error',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        }
        
        return $this->redirect('settings');
    }
}