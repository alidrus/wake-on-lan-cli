<?php
namespace Console\App\Commands;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Noodlehaus\Config;
use Noodlehaus\Parser\Json;
 
class HostsCommand extends Command
{
    protected function configure()
    {
        $this->setName('hosts')
            ->setDescription('List configured hosts');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $configFile = sprintf(
            '%s/%s',
            getenv('HOME'),
            '.wol_config.json'
        );

        if (!file_exists($configFile))
        {
            $io->error('Missing config file '.$configFile);
            return 1;
        }

        $config = Config::load($configFile);

        $hosts = $config->get('hosts');

        $rows = [];

        foreach($hosts as $label=>$host)
        {
            array_push($rows, [
                $label,
                $host['broadcastAddress'],
                $host['macAddress'],
            ]);
        }

        $header = ['Host Label', 'Broadcast Address', 'MAC Address'];

        $io->table($header, $rows);

        return 0;
    }
}
