<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Finder\Finder;
use App\Services\CommonService;
use Pimcore\Model\DataObject;
use Symfony\Component\Console\Input\InputArgument;


class ProductCommand extends AbstractCommand{
    protected function configure(){
        $this->setName('product')->setDescription('Import the Product CSV file.')->addArgument('parentFolderName',InputArgument::REQUIRED,'Please give the parent folder name');
    }

    private $csvParsingOptions = array(
        'finder_in' => 'src/Resources/',
        'finder_name' => 'product.csv',
        'finder_extension' => 'csv',
        'ignoreFirstLine' => true
    );
    
    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output){
        $commonService = new CommonService();

        $finder = new Finder();
        $finder->files()
            ->in($this->csvParsingOptions['finder_in'])
            ->name($this->csvParsingOptions['finder_name']);

        $ignoreFirstLine = $this->csvParsingOptions['ignoreFirstLine'];

        $extension = $this->csvParsingOptions['finder_extension'];
        foreach ($finder as $file) { $csv = $file; }
        $csvArray = $commonService->parseCSV($csv,$extension,$ignoreFirstLine);

        $folderName = $input->getArgument('parentFolderName');
        $productFolderId = CommonService::getFolderId($folderName);

        $finalResult = CommonService::createProductObj($csvArray,$productFolderId);
        $output->writeln($finalResult);
        return 0;
    }
}