<?php
namespace Mediashare\Marathon\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class GitignoreCommand extends Command {
    protected static $defaultName = 'timer:gitignore';

    protected function configure() {
        $this
            ->setName('timer:gitignore')
            ->setDescription('Adding <comment>.marathon</comment> rule into <comment>.gitgnore</comment>')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (file_exists($filepath = './.gitignore')):
            $currentContent = file_get_contents($filepath);
            if (str_contains($currentContent, '.marathon')):
                $output->writeln("<info><comment>.gitignore</comment> contains already <comment>.marathon</comment> rule.</info>");
                return Command::SUCCESS;
            endif;
        endif;

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('<question>Adding <comment>.marathon</comment> rule into <comment>.gitgnore</comment> ?</question> <comment>[Y/n]</comment>', true);

        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $content = "";
        if (file_exists($filepath)):
            $content = "\n\n";
        else:
            $output->writeln('<info><comment>'.$filepath.'</comment> creating</info>');
        endif;
        $content .= "###> mediashare/marathon ###\n.marathon\n###< mediashare/marathon ###\n";

        file_put_contents($filepath, $content, FILE_APPEND);

        $output->writeln('<info>Adding <comment>.marathon</comment> rule into <comment>'.$filepath.'</comment></info>');

        return Command::SUCCESS;
    }
}
