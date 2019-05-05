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
 
class WakeCommand extends Command
{
    protected function configure()
    {
        $this->setName('wake')
            ->setDescription('Wake a device up')
            ->addArgument('label', InputArgument::REQUIRED, 'Host label of the machine to wake up.');
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

        $label = $input->getArgument('label');

        $broadcastAddress = $config->get('hosts.'.$input->getArgument('label').'.broadcastAddress');

        $mac = $config->get('hosts.'.$input->getArgument('label').'.macAddress');

        if ($broadcastAddress === null || $mac === null)
        {
            $io->error('Missing configuration for "'.$label.'"');
            return 1;
        }

        // construct magic packet
        $macAddressBytes = pack('H*', preg_replace('/[^0-9a-f]/i', '', $mac));
        $magicPacket = str_repeat(chr(0), 6).str_repeat($macAddressBytes, 16);

        //
        // socket stuff from here on
        //

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        if ($socket === false)
        {
            $io->error('Error: Unable to create socket.');
            return 1;
        }

        $options = socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, true);

        if ($options === false)
        {
            $io->error('Error: Unable to set socket option.');
            return 1;
        }

        //
        // send the magic packet
        //

        $sent = socket_sendto($socket, $magicPacket, strlen($magicPacket), 0, $broadcastAddress, 7);

        if ($sent === false)
        {
            $io->error('Error: Unable to send broadcast.');
            return 1;
        }

        $io->success("Broadcast successful.");
        socket_close($socket);

        return 0;
    }
}
