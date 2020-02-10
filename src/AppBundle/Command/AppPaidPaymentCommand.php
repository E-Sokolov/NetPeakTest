<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Payment;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppPaidPaymentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:paidPayment')
            ->setDescription('Update payment to status PAID')
            ->addArgument('paymentId', InputArgument::REQUIRED, 'Id of row in Payment table ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = '';
        $paymentId = $input->getArgument('paymentId');
        if(!is_numeric($paymentId))
        {
            $message .= 'Payment ID must be a numeric value';
            $output->writeln($message);
            exit;
        }
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $payment =  $this->getContainer()->get('doctrine')->getRepository('AppBundle:Payment')->find($paymentId);
        if($payment == NULL)
        {
            $message .= 'Payment ID doesn\'t exist';
            $output->writeln($message);
            exit;
        }
        $payment -> setStatus('Paid');
        $entityManager->flush();
        $message .= 'Payment: '.$payment->getId() .' successfully update';
        $output->writeln($message);
    }

}
