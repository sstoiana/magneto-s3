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
 * Abstract database storage model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magneto_S3_Model_File_Storage_S3_Abstract extends Mage_Core_Model_File_Storage_Abstract
{
	
	    /**
     * Amazon service singleton
     *
     * @var Zend_Service_Amazon_S3
     */
    static protected $_s3 = null;
	
	protected $_bucket = null;
	
	
    /**
     * Class construct
     *
     * @param string $databaseConnection
     */
    public function __construct($params = array())
    {
        $connectionName = (isset($params['connection'])) ? $params['connection'] : null;
        if (empty($connectionName)) {
            $connectionName = $this->getConfigConnectionName();
        }

        $this->setConnectionName($connectionName);
		
		$_amazonKey = (string) Mage::app()->getConfig()
            ->getNode('default/system/media_storage_configuration/media_s3_access_key_id' );
			
		$_amazonSecret = (string) Mage::app()->getConfig()
            ->getNode('default/system/media_storage_configuration/media_s3_secret_access_key' );

		$this->_bucket = $this->getConnectionName();

		if(!$this->_s3)
		$this->_s3 = new Zend_Service_Amazon_S3($_amazonKey, $_amazonSecret);
		// $this->_s3->registerStreamWrapper("s3");
    }

    /**
     * Retrieve connection name saved at config
     *
     * @return string
     */
    public function getConfigConnectionName()
    {
        $connectionName = (string) Mage::app()->getConfig()
            ->getNode(Mage_Core_Model_File_Storage::XML_PATH_STORAGE_MEDIA_DATABASE);
        if (empty($connectionName)) {
            $connectionName = 'default_setup';
        }

        return $connectionName;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _getResource()
    {
        $resource = parent::_getResource();
        $resource->setConnectionName($this->getConnectionName());

        return $resource;
    }

    /**
     * Specify connection name
     *
     * @param  $connectionName
     * @return Mage_Core_Model_File_Storage_Database
     */
    public function setConnectionName($connectionName)
    {
        if (!empty($connectionName)) {
            $this->setData('connection_name', $connectionName);
            $this->_getResource()->setConnectionName($connectionName);
        }

        return $this;
    }
}
