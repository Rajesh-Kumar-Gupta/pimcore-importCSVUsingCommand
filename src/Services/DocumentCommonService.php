<?php

namespace App\Services;

use App\Model\CustomerManagementFramework\PasswordRecoveryInterface;
use Carbon\Carbon;
use CustomerManagementFrameworkBundle\CustomerProvider\CustomerProviderInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Folder;


class DocumentCommonService
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
            $dataFolder = \Pimcore\Model\Document::getByPath('/' . $folderName);
            //return $dataFolder;
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
            $folderObj = new \Pimcore\Model\Document\Folder();
            $folderObj->setParentId(($parentObj) ? $parentObj : 1 );
            $folderNameEx = explode('/', $folderName);
            $folderObj->setKey(end($folderNameEx));
            $folderObj->save();
            return $folderObj;
        } catch (Exception $ex) {
            return $e->getMessage();
        }
    }

    public static function createDocument($documentKey, $documentParentId){
        //CREATE A PAGE DOCUMENT
        $page = new \Pimcore\Model\Document\Page();
        $page->setKey($documentKey);
        $page->setParentId($documentParentId); // id of a document or folder
        $page->save(["versionNote" => "my new version"]);
    }


    
}
