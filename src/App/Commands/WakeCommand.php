<?php
namespace Console\App\Commands;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
 
class WakeCommand extends Command
{
    protected function configure()
    {
        $this->setName('wake')
            ->setDescription('Wakes up a device')
            ->setHelp('Allows you to wake up a device identified by host-identifier.')
            ->addArgument('host-identifier', InputArgument::REQUIRED, 'Pass the host-identifier.');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Hello World!, %s', $input->getArgument('host-identifier')));
    }
}
