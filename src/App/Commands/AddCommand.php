<?php
namespace Console\App\Commands;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Noodlehaus\Config;
use Noodlehaus\Parser\Json;
 
class AddCommand extends Command
{
    protected function configure()
    {
        $this->setName('add')
            ->setDescription('Add new host configuration')
            ->addOption('init', 'i', InputOption::VALUE_NONE, 'Initialize config file before adding host.')
            ->addArgument('label', InputArgument::REQUIRED, 'Label for host')
            ->addArgument('broadcastAddress', InputArgument::REQUIRED, 'Broadcast address')
            ->addArgument('mac', InputArgument::REQUIRED, 'MAC address');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get options and arguments
        $init             = $input->getOption('init');
        $label            = $input->getArgument('label');
        $broadcastAddress = $input->getArgument('broadcastAddress');
        $mac              = $input->getArgument('mac');

        // full path of config file
        $configFile = sprintf(
            '%s/%s',
            getenv('HOME'),
            '.wol_config.json'
        );

        $init = ($init || !file_exists($configFile));

        $config = $init ? new Config('{}', new Json, true) : Config::load($configFile);

        $config->set('hosts.'.$label.'.broadcastAddress', $broadcastAddress);
        $config->set('hosts.'.$label.'.macAddress', $mac);

        $jsonString = json_encode($config->all(), JSON_PRETTY_PRINT);

        $numBytes = file_put_contents($configFile, $jsonString);

        if ($numBytes === 0)
        {
            $output->writeln('<error>Unable to write to config file '.$configFile.'</>');
            return 1;
        }

        $output->writeln('Config file '.$configFile.' updated.');

        return 0;
    }
}
