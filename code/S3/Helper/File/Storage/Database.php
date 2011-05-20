<?php 

class Magneto_S3_Helper_File_Storage_Database extends Mage_Core_Helper_File_Storage_Database
{
	public function checkDbUsage()
	{
		Mage::log(__METHOD__.' '.$this->_useDb);
		return parent::checkDbUsage();
	}
	
	/**
     * Deletes from DB files, which belong to one folder
     *
     * @param string $folderName
     */
    public function deleteFolder($folderName)
    {
        if ($this->checkDbUsage()) {
            $this->getStorageDatabaseModel()->deleteFolder($this->_removeAbsPathFromFileName($folderName));
        }
    }
}

