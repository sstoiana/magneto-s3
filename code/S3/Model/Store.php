<?php

Mage::log('called !!!');

class Magneto_S3_Model_Store extends Mage_Core_Model_Store
{
	/**
     * Script name, which returns all the images
     */
    const MEDIA_REWRITE_SCRIPT          = 's3get.php/';
	
	/**
     * Gets URL for media catalog.
     * If we use Database file storage and server doesn't support rewrites (.htaccess in media folder)
     * we have to put name of fetching media script exactly into URL
     *
     * @param null|boolean $secure
     * @param string $type
     * @return string
     */
    protected function _updateMediaPathUseRewrites($secure=null, $type = self::URL_TYPE_MEDIA)
    {
        $secure = is_null($secure) ? $this->isCurrentlySecure() : (bool)$secure;
        $secureStringFlag = $secure ? 'secure' : 'unsecure';
        $url = $this->getConfig('web/' . $secureStringFlag .  '/base_' . $type . '_url');
        if (!$this->getConfig(self::XML_PATH_USE_REWRITES)
            && Mage::helper('core/file_storage_database')->checkDbUsage()) {

            $urlStart = $this->getConfig('web/' . $secureStringFlag .  '/base_url');
            $url = str_replace($urlStart, $urlStart . self::MEDIA_REWRITE_SCRIPT, $url);
        }
        return $url;
    }
}
