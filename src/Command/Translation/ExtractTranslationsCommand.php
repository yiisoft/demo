<?php

declare(strict_types=1);

namespace App\Command\Translation;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractTranslationsCommand extends Command
{
    public function __construct()
    {
        parent::__construct('translation/extract');
    }

    protected function configure()
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'The path where the extractor will search for messages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $extractor = new \Yiisoft\Translator\Extractor\TranslationExtractor($path);

        $defaultCategory = 'app';
        $translatorCall = '->translate';

        $messages = $extractor->extract($defaultCategory, $translatorCall);

        print_r($messages);
        return 0;
    }
}
