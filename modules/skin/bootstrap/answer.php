<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Skin.
 *
 * @package HostCMS 6\Skin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Skin_Bootstrap_Answer extends Admin_Answer
{
	/**
	 * Send header and HTML answer
	 * @return self
	 */
	protected function _sendHtml()
	{
		$this->_showHeader();

		//echo $this->message;
		
		?><div id="id_content"><?php echo $this->content?></div><?php

		$this
			->_afterLoad()
			->_showFooter();

		return $this;
	}
	
	/**
	 * Execute afterload logic
	 * @return self
	 */
	protected function _afterLoad()
	{
		parent::_afterLoad();
		?><script type="text/javascript">$.currentMenu('<?php echo Core_Str::escapeJavascriptVariable($this->module)?>');</script><?php

		return $this;
	}
}