<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace App\Services;

use App\Model\CustomerManagementFramework\PasswordRecoveryInterface;
use Carbon\Carbon;
use CustomerManagementFrameworkBundle\CustomerProvider\CustomerProviderInterface;
use CustomerManagementFrameworkBundle\Model\CustomerInterface;
use Pimcore\Mail;
use Pimcore\Model\Document\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\Document\dFolder;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Data\ExternalImage;


class CommonService
{
    /**
     * CommonService constructor.
     */
    public function __construct()
    { 
    }

    /**
     * Get the folder id otherwise create new folder
     * @param $folderName
     * @param $parentObj
     * @return folderId
    **/
    public static function getFolderId($folderName, $parentObj = '') {
        try {
            $dataFolder = \Pimcore\Model\DataObject::getByPath('/' . $folderName);
            if ($dataFolder == null) {
                $folderNameEx = explode('/', $folderName);
                
                // if(count($folderNameEx) > 0){
                //     $dataFolderInner = \Pimcore\Model\DataObject::getByPath('/' . $folderNameEx[0][1]);
                //     return $dataFolderInner;
                //     if($dataFolderInner==null){
                //         $dataFolder = self::createFolder($folderName, $parentObj); 
                //         return $dataFolder->getId();
                //     }else{
                //         $subCategory = "'/' . $folderNameEx[0][1] . '/' . $folderNameEx[0][2]";
                //         $dataFolderInnerInner = \Pimcore\Model\DataObject::getByPath($subCategory);
                //         if($dataFolderInnerInner!=null){
                //             return $dataFolder->getId();
                //         }else{
                //             $dataFolder = self::createFolder($folderName, $parentObj);
                //             return $dataFolder->getId();
                //         }
                        
                //     }
                //     // if($dataFolderInner!=null){
                //     //     $subCategory = "'/' . $folderNameEx[0][1] . '/' . $folderNameEx[0][2]";
                //     //     $dataFolderInnerInner = \Pimcore\Model\DataObject::getByPath($subCategory);
                //     //     if($dataFolderInnerInner!=null){
                //     //         return $dataFolder->getId();
                //     //     }else{
                //     //         $dataFolder = self::createFolder($folderName, $parentObj);
                //     //     }
                //     // }else{
                //     //     $dataFolder = self::createFolder($folderName, $parentObj);
                //     // }
                // }else{
                //     $dataFolder = self::createFolder($folderName, $parentObj);
                //     return $dataFolder->getId();
                // }
                $dataFolder = self::createFolder($folderName, $parentObj); 
                return $dataFolder->getId();
            }else{
                return $dataFolder->getId();
            }
            
        } catch (Exception $ex) {
            return $e->getMessage();
        }
    }
    
    /**
     * Create a new folder
     * @param $folderName, $parentObj
     * @return folderId
    **/
    public static function createFolder($folderName, $parentObj) {
        try {
            $folderObj = new \Pimcore\Model\DataObject\Folder();
            $folderObj->setParentId(($parentObj) ? $parentObj : 1 );
            $folderNameEx = explode('/', $folderName);
            $folderObj->setKey(end($folderNameEx));
            $folderObj->save();
            return $folderObj;
        } catch (Exception $ex) {
            return $e->getMessage();
        }
    }

    /**
     * Parse CSV to Array
     * @param $csv, $extension, $ignoreFirstLine
     * @return CSV array
    **/
    public function parseCSV($csv=array(),$extension,$ignoreFirstLine)
    {
        // File Type Validation
        if( empty($extension) || $extension != 'csv' ){
            $output->writeln('File type is invalid.'); 
        }else{
            
            $rows[] = array();
            if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
                $i = 0;
                while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                    $i++;
                    if ($ignoreFirstLine && $i == 1) { continue; }
                    foreach ($data as $item){
                        $rows[] = explode(",",$item);
                    }
                }
                fclose($handle);
            }
        }
        array_shift($rows);
        return $rows;
    }

    /**
     * Create Category Object from CSV Array
     * @param $csvArray, $categoryFolderId
     * @return success output
    **/
    public static function createCategoryObj($csvArray,$categoryFolderId){
        $updateCounter = $insertCounter = 0;
        $totalRecords = count($csvArray);
        
        foreach ($csvArray as $row) {
            try {
                //Set category key
                $categoryKey = 'category-'.$row['0'];
                // Check is exist or not
                $updateCategory = Category::getByCategoryId($row['0'], 1);
        
                // Check for Category Parent Id
                $parentCategoryId = $categoryFolderId;
                if(!empty($row['2'])) {
                    $parentCategoryKey = 'category-'.$row['2'];
                    $parentCategory = CommonService::getFolderId('/Category/'.$parentCategoryKey,$parentCategoryKey);
                    //dd($parentCategory);exit;
                    $parentCategoryId = $parentCategory;
                }
                // if parent is exist then Update
                if($updateCategory) {
                    // Update Object values Like name and category parent id  
                    $updateCategory->setcategoryName($row['1']);
                    $updateCategory->setcategoryParentId($row['2']);
                    
                    $checkParentId = $updateCategory->getParentId();
                    if($checkParentId != $parentCategoryId) {
                        $updateCategory->setParentId($parentCategoryId);
                    }
                    $updateCategory->save();
                    $updateCounter++;
                } else { // else create new one
                    // Create the instance of category class
                    $category = new Category();

                    // Set the object values like categoryid, category parent id,category name
                    $category->setcategoryId($row['0']);
                    $category->setcategoryParentId($row['2']);
                    $category->setcategoryName($row['1']);
                    $category->setKey(\Pimcore\Model\Element\Service::getValidKey($categoryKey, 'object'));
                    $category->setParentId($parentCategoryId);
                    $category->setPublished(true);
                    $category->save();
                    $insertCounter++;
                }
            } catch (\Exception $e) {
                $errors[] = array($row['0'], $e->getMessage());
            }
        }
        $errorCounter = count($errors);
        if($errorCounter > 1) {
            $fileName= 'auditCategoryLog_'.date('Ymdhis').'.csv';
            $filePath = 'public/'.$fileName;

            self::categoryCSVLog($fileName,$filePath, $errors);
            $errorCounter--;
        } else {
            $output = array('msg'=>'These are the following output','insert'=>$insertCounter.' Inserted','update'=>$updateCounter . " Updated, ",'total'=>$totalRecords . " Records Processed!");
            return $output;
        }
        $output = array('msg'=>'These are the following output','insert'=>$insertCounter.' Inserted','update'=>$updateCounter . " Updated, ",'total'=>$totalRecords . " Records Processed!");
        return $output;
    }

    /**
     * Create Product Object from CSV Array
     * @param $csvArray, $productFolderId
     * @return success output
    **/
    public static function createProductObj($csvArray,$productFolderId){
        $errors = [['Product Id', 'Error Message']];
        $updateCounter = $insertCounter = 0;
        $totalRecords = count($csvArray);
        
        foreach ($csvArray as $row) {
            try {
                // Check is exist or not
                $updateProduct = Product::getByProductSKU($row['0'], 1);

                // Check for Parent
                $parentProductId = $productFolderId;
                if(!empty($row['4'])) {
                    $parentProduct = Product::getByProductSKU($row['4'], 1);
                    $parentProductId = $parentProduct;
                }
        
             
                // if parent is exist then Update
                if($updateProduct) {
                    // Update Object values Like name and category parent id  
                    $product_image = new ExternalImage();
                    $product_image->setUrl($row['2']);

                    $productCategories = array();
                    $productCategory = explode("-", $row['3']);
                    foreach($productCategory as $eachCategoryId) {
                        $productCategories[] = DataObject\Category::getByCategoryId($eachCategoryId, 1);
                    }

                    $updateProduct->setProductName($row['1']);
                    $updateProduct->setImage($product_image);
                    $updateProduct->setCategory($productCategories);

                    $checkParentId = $updateProduct->getParentId();
                    if($checkParentId != $parentProductId) {
                        $updateProduct->setParentId($parentProductId);
                    }

                    $updateProduct->save();

                    $updateCounter++;
                } else { // else create new one
                    $product_image = new ExternalImage();
                    $product_image->setUrl($row['2']);
                    
                    $productCategories = array();
                    $productCategory = explode("-", $row['3']);
                    
                    foreach($productCategory as $eachCategoryId) {
                        $productCategories[] = DataObject\Category::getByCategoryId($eachCategoryId, 1);
                    }
                    
                    $product = new Product();
                    $product->setProductSKU($row['0']);
                    $product->setProductName($row['1']);
                    $product->setImage($product_image);
                    $product->setCategory($productCategories);
                    $product->setKey(\Pimcore\Model\Element\Service::getValidKey('product-'.$row['0'], 'object'));
                    $product->setParentId($parentProductId);
                    $product->setPublished(true);
                    $product->save();

                    $insertCounter++;
                }
            } catch (\Exception $e) {
                $errors[] = array($row['0'], $e->getMessage());
            }
        }
        $errorCounter = count($errors);
        if($errorCounter > 1) {
            $fileName= 'auditProductLog_'.date('Ymdhis').'.csv';
            $filePath = 'public/'.$fileName;

            self::productCSVLog($fileName,$filePath, $errors);
            $errorCounter--;
        } else {
            $output = array('msg'=>'These are the following output','insert'=>$insertCounter.' Inserted','update'=>$updateCounter . " Updated, ",'total'=>$totalRecords . " Records Processed!");
            return $output;
        }
        $output = array('msg'=>'These are the following output','insert'=>$insertCounter.' Inserted','update'=>$updateCounter . " Updated, ",'total'=>$totalRecords . " Records Processed!");
            return $output;
        
    }

    /**
     * Create Category CSV Log file if error is coming during  CSV import
     * @param $fileName, $filePath, $errors
     * @return 
    **/
    public static function categoryCSVLog($fileName,$filePath, $errors){
        // open in write only mode (write at the start of the file)    
        $fp = fopen($filePath, 'w'); 
        foreach ($errors as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        //$assetFolderId = self::getFolderId("/AuditLog");
        // Move the file to Assets section of admin 
        $newAsset = new \Pimcore\Model\Asset();
        $newAsset->setFilename($fileName);
        $newAsset->setData(file_get_contents($filePath));
        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/"));
        $newAsset->save(["versionNote" => "my new version"]);
    }

    /**
     * Create Product CSV Log file if error is coming during  CSV import
     * @param $fileName, $filePath, $errors
     * @return 
    **/
    public static function productCSVLog($fileName,$filePath, $errors){ 
        // open in write only mode (write at the start of the file)    
        $fp = fopen($filePath, 'w'); 
        foreach ($errors as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        //$assetFolderId = self::getFolderId("/AuditLog");
        // Move the file to Assets section of admin 
        $newAsset = new \Pimcore\Model\Asset();
        $newAsset->setFilename($fileName);
        $newAsset->setData(file_get_contents($filePath));
        $newAsset->setParent(\Pimcore\Model\Asset::getByPath("/"));
        $newAsset->save(["versionNote" => "my new version"]);
    }    
}
