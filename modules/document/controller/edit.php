<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Documents.
 *
 * @package HostCMS 6\Document
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2015 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Document_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		$modelName = $object->getModelName();

		switch ($modelName)
		{
			case 'document':
				if (!$object->id)
				{
					$object->document_dir_id = Core_Array::getGet('document_dir_id');
				}
			break;
			case 'document_dir':
				if (!$object->id)
				{
					$object->parent_id = Core_Array::getGet('document_dir_id');
				}
			break;
		}
		return parent::setObject($object);
	}

	/**
	 * Prepare backend item's edit form
	 *
	 * @return self
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();

		$modelName = $this->_object->getModelName();

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');

		$oSelect_Dirs = Admin_Form_Entity::factory('Select');

		$oMainTab
			->add($oMainRow1 = Admin_Form_Entity::factory('Div')->class('row'))
			->add($oMainRow2 = Admin_Form_Entity::factory('Div')->class('row'));

		switch ($modelName)
		{
			case 'document':
				$title = $this->_object->id
					? Core::_('Document.edit')
					: Core::_('Document.add');

				$oMainTab
					->move($this->getField('name'), $oMainRow1);

				$oDocument_Version_Current = $this->_object->Document_Versions->getCurrent(FALSE);

				$oTextarea_Document = Admin_Form_Entity::factory('Textarea')
					->value(
						!is_null($oDocument_Version_Current)
							? $oDocument_Version_Current->loadFile()
							: ''
					)
					->rows(15)
					->caption(Core::_('Document_Version.text'))
					->name('text')
					->wysiwyg(TRUE)
					->template_id(!is_null($oDocument_Version_Current)
						? $oDocument_Version_Current->template_id
						: 0);

				$oMainRow2->add($oTextarea_Document);

				if (Core::moduleIsActive('typograph'))
				{
					$oTextarea_Document->value(
						Typograph_Controller::instance()->eraseOpticalAlignment($oTextarea_Document->value)
					);

					$oUseTypograph = Admin_Form_Entity::factory('Checkbox')
						->name("use_typograph")
						->caption(Core::_('Document.use_typograph'))
						->value(1)
						->divAttr(array('class' => 'form-group col-sm-12 col-md-6 col-lg-6'));

					$oUseTrailingPunctuation = Admin_Form_Entity::factory('Checkbox')
						->name("use_trailing_punctuation")
						->caption(Core::_('Document.use_trailing_punctuation'))
						->value(1)
						->divAttr(array('class' => 'form-group col-sm-12 col-md-6 col-lg-6'));

					$oMainTab
						->add($oMainRow3 = Admin_Form_Entity::factory('Div')->class('row'));

					$oMainRow3
						->add($oUseTypograph)
						->add($oUseTrailingPunctuation);
				}

				// Attr
				$oAttrTab = Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Document.tab_1'))
					->name('tab_1');

				$this->addTabAfter($oAttrTab, $oMainTab);

				$oAttrTab
					->add($oAttrRow1 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oAttrRow2 = Admin_Form_Entity::factory('Div')->class('row'))
					->add($oAttrRow3 = Admin_Form_Entity::factory('Div')->class('row'));

				$oAdditionalTab->delete($this->getField('document_dir_id'));

				$oSelect_Dirs
					->options(
						array(' … ') + $this->fillDocumentDir(CURRENT_SITE, 0)
					)
					->name('document_dir_id')
					->value($this->_object->document_dir_id)
					->caption(Core::_('Document.document_dir_id'))
					->divAttr(array('class' => 'form-group col-sm-12 col-md-6 col-lg-6'));

				// Выбор макета
				$Template_Controller_Edit = new Template_Controller_Edit($this->_Admin_Form_Action);

				$aTemplateOptions = $Template_Controller_Edit->fillTemplateList($this->_object->site_id);

				$oSelect_Template_Id = Admin_Form_Entity::factory('Select')
					->options(
						count($aTemplateOptions) ? $aTemplateOptions : array(' … ')
					)
					->name('template_id')
					->value(
						!is_null($oDocument_Version_Current)
						? $oDocument_Version_Current->template_id
						: 0
					)
					->caption(Core::_('Document_Version.template_id'))
					->divAttr(array('class' => 'form-group col-sm-12 col-md-6 col-lg-6'));

				$oAttrRow1
					->add($oSelect_Dirs)
					->add($oSelect_Template_Id);

				// Статус документа
				$oAdditionalTab
					->delete($this->getField('document_status_id'));

				$Document_Status_Controller_Edit = new Document_Status_Controller_Edit($this->_Admin_Form_Action);

				$oSelect_Statuses = Admin_Form_Entity::factory('Select')
					->options(
						array(' … ') + $Document_Status_Controller_Edit->fillDocumentStatus(CURRENT_SITE)
					)
					->name('document_status_id')
					->value($this->_object->document_status_id)
					->caption(Core::_('Document.document_status_id'))
					->divAttr(array('class' => 'form-group col-sm-12 col-md-6 col-lg-6'));

				// Текушая версия
				$oInput_Current = Admin_Form_Entity::factory('Checkbox')
					->name('current')
					->value(1)
					->caption(Core::_('Document_Version.current'))
					->divAttr(array('class' => 'form-group col-sm-12 col-md-6 col-lg-6 checkbox-margin-top'));

				if (is_null($oDocument_Version_Current) || $oDocument_Version_Current->current)
				{
					$oInput_Current->checked('checked');
				}

				$oAttrRow2
					->add($oSelect_Statuses)
					->add($oInput_Current);

				$oTextarea_Description = Admin_Form_Entity::factory('Textarea')
					->value(
						!is_null($oDocument_Version_Current)
							? $oDocument_Version_Current->description
							: ''
					)
					->rows(15)
					->caption(Core::_('Document_Version.description'))
					->name('description')
					->divAttr(array('class' => 'form-group col-lg-12'));

				$oAttrRow3
					->add($oTextarea_Description);

			break;
			case 'document_dir':
			default:
				$title = $this->_object->id
						? Core::_('Document_Dir.edit_title')
						: Core::_('Document_Dir.add_title');

				// Удаляем стандартный <input>
				$oAdditionalTab->delete($this->getField('parent_id'));

				$oSelect_Dirs
					->options(
						array(' … ') + $this->fillDocumentDir(CURRENT_SITE, 0, $this->_object->id)
					)
					->name('parent_id')
					->value($this->_object->parent_id)
					->caption(Core::_('Document_Dir.parent_id'))
					->divAttr(array('class' => 'form-group col-lg-12'));

				$this->getField('name')
					->divAttr(array('class' => 'form-group col-lg-12'));

				$oMainTab->move($this->getField('name'), $oMainRow1);

				$oMainRow1->add($oSelect_Dirs);
			break;
		}

		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Document_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		parent::_applyObjectProperty();

		$modelName = $this->_object->getModelName();

		switch ($modelName)
		{
			case 'document':
				$text = Core_Array::getPost('text');

				if (Core::moduleIsActive('typograph') && Core_Array::getPost('use_typograph'))
				{
					$text = Typograph_Controller::instance()->process($text, Core_Array::getPost('use_trailing_punctuation'));
				}

				$oNewDocument_Version = Core_Entity::factory('Document_Version');
				$oNewDocument_Version->description = Core_Array::getPost('description');
				$oNewDocument_Version->template_id = intval(Core_Array::getPost('template_id'));
				$oNewDocument_Version->current = Core_Array::getPost('current');
				$oNewDocument_Version->saveFile($text);
				$this->_object->add($oNewDocument_Version);

				if ($oNewDocument_Version->current)
				{
					$oNewDocument_Version->setCurrent();
				}
			break;
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}

	/**
	 * Create visual tree of the document directories
	 * @param int $iSiteId site ID
	 * @param int $iDocumentDirParentId initial directory
	 * @param boolean $bExclude exclude group ID
	 * @param int $iLevel current nesting level
	 * @return array
	 */
	public function fillDocumentDir($iSiteId, $iDocumentDirParentId = 0, $bExclude = FALSE, $iLevel = 0)
	{
		$iSiteId = intval($iSiteId);
		$iDocumentDirParentId = intval($iDocumentDirParentId);
		$iLevel = intval($iLevel);

		$oDocument_Dir = Core_Entity::factory('Document_Dir', $iDocumentDirParentId);

		$aReturn = array();

		// Дочерние разделы
		$childrenDirs = $oDocument_Dir->Document_Dirs->getBySiteId($iSiteId);

		if (count($childrenDirs))
		{
			foreach ($childrenDirs as $childrenDir)
			{
				if ($bExclude != $childrenDir->id)
				{
					$aReturn[$childrenDir->id] = str_repeat('  ', $iLevel) . $childrenDir->name;
					$aReturn += $this->fillDocumentDir($iSiteId, $childrenDir->id, $bExclude, $iLevel+1);
				}
			}
		}

		return $aReturn;
	}
}