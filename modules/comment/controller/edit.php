<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Comments.
 *
 * @package HostCMS 6\Comment
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Comment_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		if (!$object->id)
		{
			$object->parent_id = intval(Core_Array::getGet('parent_id'));
		}

		parent::setObject($object);

		$this->title(
			$this->_object->id
				? Core::_('Comment.edit_title')
				: Core::_('Comment.add_title')
			);

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');

		$oMainTab
			->add($oMainRow1 = Admin_Form_Entity::factory('Div')->class('row'))
			->add($oMainRow2 = Admin_Form_Entity::factory('Div')->class('row'))
			->add($oMainRow3 = Admin_Form_Entity::factory('Div')->class('row'))
			->add($oMainRow4 = Admin_Form_Entity::factory('Div')->class('row'))
		;

		$this->getField('text')->wysiwyg(TRUE);
		$oMainTab->move($this->getField('text')
			->divAttr(array('class' => 'form-group col-lg-12 col-md-12 col-sm-12')), $oMainRow1);

		$oMainTab->move($this->getField('author')
			->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4')), $oMainRow2);

		$oAdditionalTab->move($this->getField('siteuser_id')->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4 col-xs-6')), $oMainRow2);
			
		if ($this->_object->siteuser_id && Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = $this->_object->Siteuser;

			$oSiteuserLink = Admin_Form_Entity::factory('Link');
			$oSiteuserLink
				->divAttr(array('class' => 'large-link checkbox-margin-top form-group col-lg-3 col-md-3 col-sm-3 col-xs-6'))
				->a
					->class('btn btn-labeled btn-sky')
					->href($this->_Admin_Form_Controller->getAdminActionLoadHref('/admin/siteuser/siteuser/index.php', 'edit', NULL, 0, $oSiteuser->id))
					->onclick("$.openWindowAddTaskbar({path: '/admin/siteuser/siteuser/index.php', additionalParams: 'hostcms[checked][0][{$oSiteuser->id}]=1&hostcms[action]=edit', shortcutImg: '" . '/modules/skin/' . Core_Skin::instance()->getSkinName() . '/images/module/siteuser.png' . "', shortcutTitle: 'undefined', Minimize: true}); return false")
					->value($oSiteuser->login)
					->target('_blank');
			$oSiteuserLink
				->icon
					->class('btn-label fa fa-user');

			$oMainRow2->add($oSiteuserLink);
		}
			
		$oMainTab->move($this->getField('email')->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4')), $oMainRow3);
		$oMainTab->move($this->getField('phone')->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4')), $oMainRow3);
		$oMainTab->move($this->getField('active')->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4 margin-top-21')), $oMainRow3);
		$oMainTab->move($this->getField('ip')->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4')), $oMainRow4);
		$oMainTab->move($this->getField('datetime')->divAttr(array('class' => 'form-group col-lg-4 col-md-4 col-sm-4')), $oMainRow4);

		$oMainTab
			->delete($this->getField('grade'));

		$oMainRow4->add(
			Admin_Form_Entity::factory('Stars')
				->name('grade')
				->id('grade')
				->caption(Core::_('Comment.grade'))
				->value($this->_object->grade)
				->divAttr(array('class' => 'form-group stars col-lg-4 col-md-4 col-sm-4'))
		);

		return $this;
	}
}