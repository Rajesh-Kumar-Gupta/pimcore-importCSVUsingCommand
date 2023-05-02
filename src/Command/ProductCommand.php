<?php
namespace App\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\Product;
use Symfony\Component\Finder\Finder;
use App\Services\CommonService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Pimcore\Model\DataObject\Data\ExternalImage;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Service;


class ProductCommand extends \Pimcore\Console\AbstractCommand{
    protected function configure(){
        $this->setName('product')->setDescription('Import the Product CSV file.');
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

        $productFolderId = CommonService::getFolderId("Product");
        $finalResult = CommonService::createProductObj($csvArray,$productFolderId);
        $output->writeln($finalResult);
        return 0;
    }
}