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
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct('app/translation/translate');
        $this->translator = $translator;
    }

    protected function configure()
    {
        $this->addArgument('message', InputArgument::REQUIRED, 'Message that will be translated.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = $input->getArgument('message');
        $output->writeln($this->translator->translate($message));
        return 0;
    }
}
