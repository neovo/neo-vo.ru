<?php
defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
* Some useful helpers
*
* @package HostCMS 6\Core
* @version 6.x
* @author James V. Kotoff
* @copyright 2013
*
* path to file: /modules/core/utils.php
*/
class Core_Utils
{
   /**
    * Core_Utils::getCanonicalUrl() - вычисляет канонический адрес страницы для link rel="canonical"
    * 
    * @param mixed $iSiteId
    * @return string
    */
   static public function getCanonicalUrl($iSiteId = CURRENT_SITE)
   {
      $sUri = strval(Core_Array::get($_SERVER, 'REQUEST_URI', ''));
      $sQuery = strval(Core_Array::get($_SERVER, 'QUERY_STRING', ''));
      $sLink = str_replace('?' . $sQuery, '', $sUri);
      $oSite_Alias = Core_Entity::factory('Site', intval($iSiteId))->getCurrentAlias();
      if(!is_null($oSite_Alias)) {
         return 'http://' . $oSite_Alias->name . $sLink;
      } else {
         return $sLink;
      }
   }
}