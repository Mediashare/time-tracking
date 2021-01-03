<?php
namespace Mediashare\Command;

use Phar;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class UpgradeCommand extends Command {
    protected static $defaultName = 'upgrade';
    
    protected function configure() {
        $this
            ->setName('upgrade')
            ->setDescription('Download latest version of Time Tracking');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!\Phar::running()):
            $text = "<comment>Use <git pull> for upgrade Time Tracking</comment>";
            $output->writeln($text);
            return 0;
        endif;

        $file = \Phar::running();
        $file = str_replace('phar://', '', $file);
        $url = 'https://gitlab.marquand.pro/MarquandT/time-tracking/-/raw/master/time-tracking?inline=false';
        $tmp = $file.'.tmp';
        if (!is_writable(\pathinfo($tmp)['dirname'])):
            $text = "<error>You have not permission for write ".$tmp." file</error>";
            $output->writeln($text);
            $text = "<error>You can use sudo command for allow permission.</error>";
            $output->writeln($text);
            return 0;
        endif;

        // Download
        file_put_contents($tmp, file_get_contents($url));
        if (!\file_exists($tmp)):
            $text = "<error>Error download [".$url."]</error>";
            $output->writeln($text);
            return 0;
        endif;

        // Replace binary file
        $filesystem = new Filesystem();
        $filesystem->remove($file);
        $filesystem->rename($tmp, $file);
        $filesystem->chmod($file, 0755);

        $text = "<info>Time Tracking successly updated</info>";
        $output->writeln($text);

        return 1;
    }
}
