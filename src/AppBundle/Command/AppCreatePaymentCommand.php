<?php
namespace AppBundle\Command;

use AppBundle\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

class AppCreatePaymentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:createPayment')
            ->setDescription('create payment: table payment')
            ->setHelp('Parameters: 1). User id 2).Product Id - quantity of Products example app:createPayment(1,1-10 2-4 3-8)')

            ->addArgument('userId', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('products',InputArgument::IS_ARRAY,'Array of Products(ProductId - quantity)')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init
        $message = '';
        $productOutput = array();

        $userId = $input->getArgument('userId');
        // valid UserId
        if(is_numeric($userId) == true)
        {
            $message .= 'UserId:'.$userId.'|';
        }else{
            $message .= 'first Argument must be a userId  (number)';
            $output->writeln($message);
            exit;
        }
        $productArg = $input -> getArgument('products');
        $price = 0;
        foreach($productArg as $key => $value){
            //divide ProductId and Quantity
            $valueArr = explode('-',$value);
            // valid ProductId
            if(!is_numeric($valueArr[0]))
            {
                $message .='Product Id must be a Numeric Value';
                $output->writeln($message);
                exit;
            }
            //valid quantity
            if(!is_numeric($valueArr[1]))
            {
                $message .='quantityId must be a Numeric Value';
                $output->writeln($message);
                exit;
            }
            //valid quantity ( !> 10)
            if($valueArr[1] > 10)
            {
                $message .='quantity must be 10 or lower';
                $output->writeln($message);
                exit;
            }
            // valid count product
            if(empty($productOutput[$valueArr[0]])){
                $productOutput[$valueArr[0]] = $valueArr[1];
            }else{
                $message .= 'the Same Product Can\'t repeate';
                $output->writeln($message);
                exit;
            }

            // product exist ?
            $productExist = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Product')->find($valueArr[0]);
            if($productExist == NULL)
            {
                $message .= 'product:'.$valueArr[0].'- Doesn\'t Exist |';
                $output->writeln($message);
                exit;
            }

            //Count Price for Payment
            $price = $price + $valueArr[1] * $productExist -> getPrice($valueArr[0]);
        }
        //output variable to console
        foreach ($productOutput as $key => $value)
        {
            $message .= 'Product:'.$key. ' - Quantity:'.$value.'|';
        }
        $message .= 'Price: '.$price.'|';

        //create object for DataBase
        $Payment = new Payment();
        $Payment ->setUserId($userId);
        $Payment ->setProductArray($productOutput);
        $now = new \DateTime('NOW');
        $Payment ->setDate($now);
        $Payment ->setPrice($price);
        $Payment ->setStatus('New');

        // INSERT INTO DataBase
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($Payment);
        $entityManager->flush();

        $message .= 'Saved new payment with id '.$Payment->getId();

        // console output
        $output->writeln($message);
    }
}