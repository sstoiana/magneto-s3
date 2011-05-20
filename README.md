# Magento Media Storage using Amazon S3

This extension stores Magento's media files on Amazon S3. It is installed as a Magento module without hacking Magento's core. Works with Magento Community 1.5.1.0

*Highly experimental at the moment!*

## INSTALLATION 

### Via Modman
 - Modman required: <http://code.google.com/p/module-manager/>
<pre>
curl http://module-manager.googlecode.com/files/modman-1.1.5 > modman
chmod +x modman
sudo mv modman /usr/bin
</pre>
 - set "Allow Symlinks" under System / Configuration > Advanced / Developer > Template Settings in Magento 
 - Install via modman (for details consult modman website):
<pre>
cd <magento root folder>
modman init
modman magneto-debug clone https://github.com/sstoiana/magneto-s3.git
</pre>
 - Make sure you've cleaned Magento's cache to enable the new module
 - The extension configuration is under System / Configuration > Advanced / System > Storage Configuration for Media
 - Change "Media Storage" to S3, enter your Amazon S3 credentials

### Via Magento Connect
It's not there yet...

## FEATURES 
 - store Magento's media files on Amazon S3 
 - works with category images, catalog products' images, thumbnails, CMS images, concatenated JS and CSS files

## KNOWN ISSUES
We're working to correct these:

 - Changing File Storage back to "Filesystem" doesn't work yet
 - Not completely tested
 - Right now, the "Amazon S3" storage overrides "Database" storage