<?php
declare(strict_types=1);

namespace Dkd\LlmsTxtGenerator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Dkd\LlmsTxtGenerator\Service\LlmsGenerator;

class GenerateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Generate llms.txt files for TYPO3 sites')
            ->addOption(
                'site',
                's',
                InputOption::VALUE_OPTIONAL,
                'Site identifier to generate for (omit for all sites)'
            )
            ->addOption(
                'full',
                'f',
                InputOption::VALUE_NONE,
                'Generate full-content version'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('LLMs.txt Generator');

        try {
            $llmsGenerator = GeneralUtility::makeInstance(LlmsGenerator::class);
            
            $siteIdentifier = $input->getOption('site');
            $fullVersion = $input->getOption('full');
            
            $io->info('Starting generation...');
            
            $results = $llmsGenerator->generate($siteIdentifier, $fullVersion);
            
            foreach ($results as $site => $languages) {
                $io->section('Site: ' . $site);
                foreach ($languages as $languageId => $info) {
                    $io->writeln(sprintf(
                        '  - Language %d: %s (%d bytes, %d pages)',
                        $languageId,
                        $info['filename'],
                        $info['size'],
                        $info['pages']
                    ));
                }
            }
            
            $io->success('Generation completed successfully!');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('Generation failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}