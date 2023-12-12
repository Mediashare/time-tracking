<?php
namespace Mediashare\Marathon\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class UpgradeCommand extends Command {
    protected static $defaultName = 'timer:upgrade';

    protected function configure() {
        $this
            ->setName('timer:upgrade')
            ->setDescription('<comment>Upgrading</comment> to latest version of Marathon');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!\Phar::running()):
            $output->writeln("<info>Use <comment>git pull</comment> for upgrade Marathon</info>");
            return Command::INVALID;
        endif;

        $file = \Phar::running();
        $file = str_replace('phar://', '', $file);
        $url = 'https://github.com/Mediashare/marathon/raw/master/marathon';
        $tmp = tempnam(sys_get_temp_dir(), uniqid('marathon-', true).'.tmp');
        if (!is_writable(\pathinfo($tmp, PATHINFO_DIRNAME))):
            $text = "<error>You have not permission for write <comment>".$tmp."</comment> file</error>";
            $output->writeln($text);
            $text = "<error>You can use sudo command for allow permission</error>";
            $output->writeln($text);
            return Command::FAILURE;
        endif;

        // Download
        file_put_contents($tmp, file_get_contents($url));
        if (!\file_exists($tmp)):
            $text = "<error>Error download <comment>".$url."</comment></error>";
            $output->writeln($text);
            return Command::FAILURE;
        endif;
        
        // Check version
        $filesystem = new Filesystem();
        // if (filesize($file) !== filesize($tmp)
        //     || md5_file($file) !== md5_file($tmp)):
        //     $filesystem->remove($tmp);
        //     $output->writeln("<info>Marathon run already with last version</info>");
        //     return 0;
        // endif;
        
        // Replace binary file
        $filesystem->remove($file);
        $filesystem->rename($tmp, $file);
        $filesystem->chmod($file, 0755);

        $output->writeln("<info>Marathon successly <comment>upgraded</comment></info>");

        return Command::SUCCESS;
    }
}
