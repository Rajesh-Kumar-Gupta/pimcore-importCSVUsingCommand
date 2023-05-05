<?php

namespace App\Services;

use CustomerManagementFrameworkBundle\CustomerProvider\CustomerProviderInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\Asset;


class AssetsCommonService
{
    /**
     * CommonService constructor.
     */
    public function __construct()
    { 
    }

    /**
     * Get the folder id otherwise create new folder in admin document section
     * @param $folderName
     * @param $parentObj
     * @return folderId
    **/
    public static function getDocumentFolderId($folderName, $parentObj = '') {
        try {
            $dataFolder = Asset::getByPath('/' . $folderName);

            if ($dataFolder == null) {
                $folderNameEx = explode('/', $folderName);
                
                $dataFolder = self::createDocumentFolder($folderName, $parentObj); 
                return $dataFolder->getId();
            }else{
                return $dataFolder->getId();
            }
            
        } catch (Exception $ex) {
            return $e->getMessage();
        }
    }
    
    /**
     * Create a new folder in Document section
     * @param $folderName, $parentObj
     * @return folderId
    **/
    public static function createDocumentFolder($folderName, $parentObj) {
        try {
            $folderObj = new \Pimcore\Model\Asset\Folder();
            $folderObj->setParentId(($parentObj) ? $parentObj : 1 );
            $folderNameEx = explode('/', $folderName);
            $folderObj->setKey(end($folderNameEx));
            $folderObj->save();
            return $folderObj;
        } catch (Exception $ex) {
            return $e->getMessage();
        }
    }
}
