<?php

declare(strict_types=1);

namespace App\Command\Translation;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Translator\TranslatorInterface;

final class TranslateCommand extends Command
{
    protected static $defaultName = 'translator/translate';
    protected static $defaultDescription = 'Translates a message';

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('message', InputArgument::REQUIRED, 'Message that will be translated.');
        $this->addArgument('locale', InputArgument::OPTIONAL, 'Translation locale.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = $input->getArgument('message');
        $locale = $input->getArgument('locale');

        $output->writeln($this->translator->translate($message, [], null, $locale));
        return 0;
    }
}
