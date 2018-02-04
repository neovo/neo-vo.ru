<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Discount_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
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
			$object->shop_id = Core_Array::getGet('shop_id');
		}

		parent::setObject($object);

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');

		$oMainTab
			->add($oMainRow1 = Admin_Form_Entity::factory('Div')->class('row'))
			->add($oMainRow2 = Admin_Form_Entity::factory('Div')->class('row'))
			->add($oMainRow3 = Admin_Form_Entity::factory('Div')->class('row'))
			->add($oMainRow4 = Admin_Form_Entity::factory('Div')->class('row'))
		;

		$this->getField('description')->wysiwyg(TRUE);
		$oMainTab->move($this->getField('description')->divAttr(array('class' => 'form-group col-lg-12 col-md-12 col-sm-12')), $oMainRow2);
		$oMainTab->move($this->getField('start_datetime')->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6')), $oMainRow3);
		$oMainTab->move($this->getField('end_datetime')->divAttr(array('class' => 'form-group col-lg-6 col-md-6 col-sm-6')), $oMainRow3);

		$oMainTab->move($this->getField('active')->divAttr(array('class' => 'form-group col-lg-12 col-md-12 col-sm-12')), $oMainRow4);

		$oMainTab->delete($this->getField('type'));

		$oTypeSelectField = Admin_Form_Entity::factory('Select');

		$oTypeSelectField
			->name('type')
			->divAttr(array('class' => 'form-group col-lg-2 col-md-2 col-sm-2'))
			->caption(Core::_('Shop_Discount.type'))
			->options(array(
				Core::_('Shop_Discount.form_edit_affiliate_values_type_percent'),
				Core::_('Shop_Discount.form_edit_affiliate_values_type_summ'))
			)
			->value($this->_object->type);

		$oMainRow1->add($oTypeSelectField);
		$oMainTab->move($this->getField('value')->divAttr(array('class' => 'form-group col-lg-2 col-md-2 col-sm-2')), $oMainRow1);

		$title = $this->_object->id
			? Core::_('Shop_Discount.item_discount_edit_form_title')
			: Core::_('Shop_Discount.item_discount_add_form_title');

		$this->title($title);

		return $this;
	}
}