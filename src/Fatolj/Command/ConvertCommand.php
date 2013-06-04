<?php

namespace Fatolj\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Writer\TranslationWriter;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;
use JMS\TranslationBundle\Translation\Loader\Symfony\XliffLoader;
use Transliterator\Transliterator;

class ConvertCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDescription('Transliterates given translation file.')
            ->setDefinition(array(
                new InputArgument('source', InputArgument::REQUIRED, 'Path to source (input) file'),
                new InputArgument('locale', InputArgument::OPTIONAL, 'Locale', 'en'),
                new InputArgument('domain', InputArgument::OPTIONAL, 'Translation domain', 'messages'),
                new InputArgument('direction', InputArgument::OPTIONAL, 'Transliteration direction', true),
            ))
            ->setHelp(sprintf(
                '%sTransliterates given translation file.%s',
                PHP_EOL,
                PHP_EOL
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Loading translations from <comment>%s</comment>.', $input->getArgument('source')));
        $loader = new XliffLoader();
        $catalogue = $loader->load(
            $input->getArgument('source'),
            $input->getArgument('locale')
        );

        $output->writeln('Transliterating...');
        $transliterator = new Transliterator(substr($input->getArgument('locale'), 0, 2));
        $messages = [];
        foreach ($catalogue->all() as $id => $translation) {
            $messages[$id] = $transliterator->transliterate($translation, $input->getArgument('direction'));
        }
        $convertedCatalogue = new MessageCatalogue($input->getArgument('locale'), $messages);

        $output->writeln(sprintf('Dumping result to <comment>%s</comment>.', dirname($input->getArgument('source'))));
        $writer = new TranslationWriter();
        $writer->addDumper('xliff', new XliffFileDumper());
        $writer->writeTranslations(
            $convertedCatalogue,
            'xliff',
            [
                'path'   => dirname($input->getArgument('source')),
                'domain' => $input->getArgument('domain')
            ]
        );
    }
}
