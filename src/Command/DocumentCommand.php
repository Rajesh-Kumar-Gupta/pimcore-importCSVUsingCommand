<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Console\AbstractCommand;
use App\Services\DocumentCommonService;
use Symfony\Component\Console\Input\InputArgument;

class DocumentCommand extends AbstractCommand{
    protected function configure(){
        $this->setName('document')->setDescription('Create folder in document Section.')->addArgument('argument',InputArgument::REQUIRED,'Please give the parent folder name and document name');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output){
        $argument = $input->getArgument('argument');
        $argument = explode(',',$argument);

        if(count($argument)== 2){
            $folderName = $argument[0];
            $documentName = $argument[1];
        }else{
            $folderName = "DemoFolder";
            $documentName = "DemoDocument";
        }

        $documentParentId = DocumentCommonService::getDocumentFolderId($folderName);
   
        DocumentCommonService::createDocument($documentName, $documentParentId);
        $output->writeln("Folder created successfully id is:".$documentParentId);
        return 0;
    }
}