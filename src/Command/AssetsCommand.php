<?php
namespace App\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Console\AbstractCommand;
use App\Services\AssetsCommonService;
use Symfony\Component\Console\Input\InputArgument;



class AssetsCommand extends \Pimcore\Console\AbstractCommand{
    public function __construct(bool $requireFolderName= false)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->requireFolderName = $requireFolderName;

        parent::__construct();
    }
    protected function configure(): void{
        $this->setName('app:create-assets-folder')
        ->setDescription('Create folder in Assets Section.')
        ->addArgument('argument', $this->requireFolderName ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User password');
        //->addArgument('argument',InputArgument::REQUIRED,'Please give the parent folder name and document name');;
    }

    
    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output){
        $argument = $input->getArgument('argument');
        if($argument == null)
        {
            $argument = "TestDoc";
        }
        $parentId = AssetsCommonService::getDocumentFolderId($argument);
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);
    
        // outputs a message followed by a "\n"
        $output->writeln('Whoa!');
    
        // outputs a message without adding a "\n" at the end of the line
        $output->write('You are about to ');
        $output->write('create a user.');
        $output->writeln("Create folder successfully id is:".$parentId);
        return 0;
    }
}