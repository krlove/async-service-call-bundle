<?php

namespace Krlove\AsyncServiceCallBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CallServiceCommand
 * @package Krlove\AsyncServiceCallBundle\Command
 */
class CallServiceCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('krlove:service:call')
            ->setDescription('Calls a service method with arguments')
            ->addArgument('service', InputArgument::REQUIRED, 'Service ID')
            ->addArgument('method', InputArgument::REQUIRED, 'Method to call on the service')
            ->addOption('args', null, InputOption::VALUE_OPTIONAL, 'Arguments to supply to the method');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceId = $input->getArgument('service');
        if (!$this->getContainer()->has($serviceId)) {
            throw new InvalidArgumentException(sprintf('Service %s doesn\'t exist', $serviceId));
        }
        $service = $this->getContainer()->get($serviceId);

        $method = $input->getArgument('method');
        if (!method_exists($service, $method)) {
            throw new InvalidArgumentException(
                sprintf('Method %s doesn\'t exist on class %s', $method, get_class($service))
            );
        }

        $serviceArgs = unserialize($input->getOption('args'));

        call_user_func_array([$service, $method], $serviceArgs); // todo log result
    }
}
