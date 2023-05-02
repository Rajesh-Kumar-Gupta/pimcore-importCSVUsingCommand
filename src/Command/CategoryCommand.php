<?php
namespace App\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\Category;
use Symfony\Component\Finder\Finder;

use App\Services\CommonService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class CategoryCommand extends \Pimcore\Console\AbstractCommand{
    protected function configure(){
        $this->setName('category')->setDescription('Import the Product CSV file.');
    }
   
    /**
     * {@inheritdoc}
     */
    protected function execute1(InputInterface $input, OutputInterface $output){
        //Create
        $category = new \Pimcore\Model\DataObject\Category();
        $category->setCategoryId('Category id 1');
        $category->setCategoryName('Category Name');
        $category->setParentsId('Par2');
        $category->setParentId(2);
        $category->setKey('Category 1');
        $category->setPublished(true);
        $category->save();
        $output->writeln('Category data imported successfull.');
        return 0;
    }


    public function execute2(InputInterface $input, OutputInterface $output)
    {
        try{
            //$commonService = new CommonService();
            $categoryObjectList = new \Pimcore\Model\DataObject\Category();
            $folderId = CommonService::getFolderId('/'.ucfirst('Categories'));
            $finder = new Finder();
            $finder->files()
                ->in($this->csvParsingOptions['finder_in'])
                ->name($this->csvParsingOptions['finder_name']);
        
            $ignoreFirstLine = $this->csvParsingOptions['ignoreFirstLine'];
            
            $extension = $this->csvParsingOptions['finder_extension'];
            
            // File Type Validation
            if( empty($extension) || $extension != 'csv' ){
                $output->writeln('File type is invalid.'); 
            }else{
                foreach ($finder as $file) { $csv = $file; }
                $rows = array();
                if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
                    $i = 0;
                    while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                        $i++;
                        if ($ignoreFirstLine && $i == 1) { continue; }
                        foreach($data as $row) {
                            $col = explode(",",$row);
                            $categoryId = $col[0];
                            $categoryName = $col[1];
                            $categoryParentId = $col[2];
                            
                            $folderId = CommonService::getFolderId('Categories/'.ucfirst($categoryParentId));
                            $categoryObject = new \Pimcore\Model\DataObject\Category();
                            $categoryObject->setKey($categoryId);
                            $categoryObject->setParentId($folderId);
                            $categoryObject->setPublished(true);
                            $categoryObject->setcategoryId($categoryId);
                            $categoryObject->setcategoryName($categoryName);
                            $categoryObject->setParentsId($categoryParentId);
                            $categoryObject = $categoryObject->save();
                        }
                    }
                    fclose($handle);
                }
                $output->writeln('Category imported successfull.');
            }
            
        }
        catch(\Exception $e){
            error_log($e->getMessage());
        }
        
        return 0;
    }

    private $csvParsingOptions = array(
        'finder_in' => 'src/Resources/',
        'finder_name' => 'category.csv',
        'finder_extension' => 'csv',
        'ignoreFirstLine' => true
    );
    

    public function execute(InputInterface $input, OutputInterface $output){
        $errors = [['Category Id', 'Error Message']];
        $commonService = new CommonService();

        $finder = new Finder();
        $finder->files()
            ->in($this->csvParsingOptions['finder_in'])
            ->name($this->csvParsingOptions['finder_name']);

        $ignoreFirstLine = $this->csvParsingOptions['ignoreFirstLine'];
        $extension = $this->csvParsingOptions['finder_extension'];

        foreach ($finder as $file) { $csv = $file; }

        $csvArray = $commonService->parseCSV($csv,$extension,$ignoreFirstLine);
        //dd($results);exit;
        $categoryFolderId = CommonService::getFolderId("Category");
        $finalResult = CommonService::createCategoryObj($csvArray,$categoryFolderId);
        $output->writeln($finalResult);
        return 0;
    }
}