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
class AppPaymentReportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:PaymentReport')
            ->setDescription('Report about payments for some period')
            ->addArgument('dateFrom', InputArgument::REQUIRED, 'Date From')
            ->addArgument('dateTo',InputArgument::REQUIRED,'Date to')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //init variable
        $message ='';
        $sumProductValue = 0;
        //get Argument
        $dateFrom = $input->getArgument('dateFrom');
        $dateTo = $input->getArgument('dateTo');
        //.validate argument
        if(!is_numeric(strtotime($dateFrom)))
        {
            $message .= 'First Argument isn\'t date';
            $output->writeln($message);
            exit;
        }else{
            $dateFrom = date("Y-m-d", strtotime($dateFrom));
        }
        if(!is_numeric(strtotime($dateTo)))
        {
            $message .= 'Second Argument isn\'t date';
            $output->writeln($message);
            exit;
        }else{
            $dateTo = date("Y-m-d", strtotime($dateTo));
        }
        //get data from table payments by functions from Repository
        $payments = $this -> getContainer()->get('doctrine')->getRepository('AppBundle:Payment')
            ->getPayments($dateFrom,$dateTo,'COUNT(pay.id)', '');
        $newPayments = $this -> getContainer()->get('doctrine')->getRepository('AppBundle:Payment')
            ->getPayments($dateFrom,$dateTo,'COUNT(pay.id)', ' pay.status = \'New\' AND');
        $paidPayments = $this -> getContainer()->get('doctrine')->getRepository('AppBundle:Payment')
            ->getPayments($dateFrom,$dateTo,'COUNT(pay.id)', ' pay.status = \'Paid\' AND');
        $sumPrice = $this -> getContainer()->get('doctrine')->getRepository('AppBundle:Payment')
            ->getPayments($dateFrom,$dateTo,'SUM(pay.price)', ' pay.status = \'Paid\' AND');
        $maxUserPrice = $this -> getContainer()->get('doctrine')->getRepository('AppBundle:Payment')
            ->getMaxUserPayments($dateFrom,$dateTo);
        $sumProduct = $this -> getContainer()->get('doctrine')->getRepository('AppBundle:Payment')
            ->getPayments($dateFrom,$dateTo,'pay.productArray', ' pay.status = \'Paid\' AND ');

        // count paid product
        foreach($sumProduct as $keynum => $value)
        {
            foreach ($value as $keyProd => $valueProd)
            {
                foreach($valueProd as $keyRow => $valueRow)
                {
                    $sumProductValue += $valueRow;
                }
            }
        }
        // generate output console message
        $message .= ' All payments:'.$payments[0][1].'
 New Payments:'.$newPayments[0][1].'
 Paid Payments:'.$paidPayments[0][1].'
 Sum $:'.$sumPrice[0][1].'|
 User max paid:'.max($maxUserPrice)[1].'
 Paid product:'.$sumProductValue.'
         ';
        $output->writeln($message);
    }

}
