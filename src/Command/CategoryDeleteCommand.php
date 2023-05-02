<?php
namespace App\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Pimcore\Console\AbstractCommand;
use WorkBundle\Services\CommonService;
use Symfony\Component\Finder\Finder;


class CategoryDeleteCommand extends \Pimcore\Console\AbstractCommand{
    protected function configure(){
        $this->setName('deleteCategory')->setDescription('Delete the Category by Id.');
    }

  
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        //Create
        $category = new \Pimcore\Model\DataObject\Category();
        
        $category->setParentId(2);
        $category->setKey('Category 1');
    
        $category->delete();
        $output->writeln('Category data deleted successfull.');
        return 0;
    }
}