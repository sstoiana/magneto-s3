<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * File storage database model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magneto_S3_Model_File_Storage_S3 extends Magneto_S3_Model_File_Storage_S3_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_file_storage_database';


    /**
     * Collect errors during sync process
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Class construct
     *
     * @param string $connectionName
     */
    public function __construct($connectionName = null)
    {
    	Mage::log(__METHOD__);
    	
        $this->_init('core/file_storage_database');
		
		parent::__construct($connectionName);
		

		
        
    }

    /**
     * Create tables for file and directory storages
     *
     * @return Mage_Core_Model_File_Storage_Database
     */
    public function init()
    {
        Mage::log(__METHOD__);
        return $this;
    }

    /**
     * Return storage name
     *
     * @return string
     */
    public function getStorageName()
    {
        Mage::log(__METHOD__);
    	//TODO: replace dummy
    	return Mage::helper('core')->__('S3 bucket "%s"', $this->getConnectionName());
        //return Mage::helper('core')->__('database "%s"', $this->getConnectionName());
    }

    /**
     * Load object data by filename
     *
     * @param  string $filePath
     * @return Mage_Core_Model_File_Storage_Database
     */
    public function loadByFilename($filePath)
    {
    	$filePath = str_replace(array('//', '\\'), '/', $filePath);
		Mage::log(__METHOD__.' '.$filePath);
		
		$_content = $this->_s3->getObject($this->_bucket . DS . $filePath);
		
		$this->setFilename(basename($filePath));
		$this->setDirectory(dirname($filePath));
        $this->setContent( $_content );
		$this->setId($_content);
		
        return $this;
    }

    /**
     * Check if there was errors during sync process
     *
     * @return bool
     */
    public function hasErrors()
    {
        Mage::log(__METHOD__);
        return (!empty($this->_errors));
    }

    /**
     * Clear files and directories in storage
     *
     * @return Mage_Core_Model_File_Storage_Database
     */
    public function clear()
    {
        Mage::log(__METHOD__);
        // $this->getDirectoryModel()->clearDirectories();
        // $this->_getResource()->clearFiles();
        $this->_s3->cleanBucket($this->_bucket);
        return $this;
    }

    /**
     * Export directories from storage
     *
     * @param  int $offset
     * @param  int $count
     * @return bool|array
     */
    public function exportDirectories($offset = 0, $count = 100) 
    {

        Mage::log(__METHOD__);
		
        //return $this->getDirectoryModel()->exportDirectories($offset, $count);
        // TODO: write me
        return false;
    }

    /**
     * Import directories to storage
     *
     * @param  array $dirs
     * @return Mage_Core_Model_File_Storage_Directory_Database
     */
    public function importDirectories($dirs) 
    {

        Mage::log(__METHOD__);
        
		//no need to do anything here, since s3 doesn't know about directories
        return $this;
    }

    /**
     * Export files list in defined range
     *
     * @param  int $offset
     * @param  int $count
     * @return array|bool
     */
    public function exportFiles($offset = 0, $count = 100)
    {

        Mage::log(__METHOD__);
		
		//TODO: write me
		return false;
		
        $offset = ((int) $offset >= 0) ? (int) $offset : 0;
        $count  = ((int) $count >= 1) ? (int) $count : 1;

        $result = $this->_getResource()->getFiles($offset, $count);
        if (empty($result)) {
            return false;
        }

        return $result;
    }

    /**
     * Import files list
     *
     * @param  array $files
     * @return Mage_Core_Model_File_Storage_Database
     */
    public function importFiles($files)
    {

        Mage::log(__METHOD__);
        if (!is_array($files)) {
            return $this;
        }

        foreach ($files as $file) {
            if (!isset($file['filename']) || !strlen($file['filename']) || !isset($file['content'])) {
                continue;
            }

            try {
                
                $this->saveFile($file['directory'].DS.$file['filename']);
            } catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * Store file into database
     *
     * @param  string $filename
     * @return Mage_Core_Model_File_Storage_Database
     */
    public function saveFile($filename)
    {

		$filename = str_replace(array('//', '\\'), '/', $filename);
        Mage::log(__METHOD__.' '.$filename);
        $fileInfo = $this->collectFileInfo($filename);
        $filePath = $fileInfo['directory'];
		

        $this->_s3->putObject($this->_bucket . DS . $fileInfo['directory'] . DS . $fileInfo['filename'], $fileInfo['content']);

        return $this;
    }

    /**
     * Check whether file exists in DB
     *
     * @param  string $filePath
     * @return bool
     */
    public function fileExists($filePath)
    {

        Mage::log(__METHOD__.' '.$filePath);
        return $this->_s3->isObjectAvailable($this->_bucket.DS.$filePath);
    }

    /**
     * Copy files
     *
     * @param  string $oldFilePath
     * @param  string $newFilePath
     * @return Mage_Core_Model_File_Storage_Database
     */
    public function copyFile($oldFilePath, $newFilePath)
    {

        Mage::log(__METHOD__);
        $_content = $this->_s3->getObject($this->_bucket.DS.$oldFilePath);
		$this->_s3->putObject($this->_bucket.DS.$newFilePath, $_content);

        return $this;
    }

    /**
     * Rename files in database
     *
     * @param  string $oldFilePath
     * @param  string $newFilePath
     * @return Mage_Core_Model_File_Storage_Database
     */
    public function renameFile($oldFilePath, $newFilePath)
    {
    	Mage::log(__METHOD__);
		
        $_content = $this->_s3->getObject($this->_bucket.DS.$oldFilePath);
		$this->_s3->putObject($this->_bucket.DS.$newFilePath, $_content);
		$this->_s3->removeObject($this->_bucket.DS.$oldFilePath);

        return $this;
    }

    /**
     * Return directory listing
     *
     * @param string $directory 
     * @return mixed
     */
    public function getDirectoryFiles($directory)
    {
    	$directory = str_replace(array('//', '\\'), '/', $directory);
        Mage::log(__METHOD__ . ' ' . $directory);
		
        $directory = Mage::helper('core/file_storage_database')->getMediaRelativePath($directory);
		Mage::log(__METHOD__ . ' ' . $directory);
		
		$files = $this->_s3->getObjectsByBucket($this->_bucket, array('prefix'=>$directory));
		
		$return = array();
		foreach($files as $file)
		{
			if(substr($file, -1, 1) == '/') continue;
			
			$return[] = array(
				'filename' => basename($file),
				'directory'=> dirname($file), 
				'content' => $this->_s3->getObject($this->_bucket.DS.$file)
			);
		}
		
        return $return;
    }

    /**
     * Delete file from database
     *
     * @param string $path
     * @return Mage_Core_Model_File_Storage_Database
     */
    public function deleteFile($path)
    {
        Mage::log(__METHOD__ . ' ' . $path);
        $this->_s3->removeObject($this->_bucket.DS.$path);

        return $this;
    }
	
	public function deleteFolder($path) 
	{
		Mage::getModel('core/file_storage_directory_database')->deleteDirectory($path);
	}
}
