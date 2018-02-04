<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 *
 * @package HostCMS
 * @subpackage Cecutient
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2016 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Cecutient_Controller_Show extends Core_Servant_Properties
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'types',
		'fontsizes',
		'backgrounds',
	);

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->fontsizes = array(
			'small',
			'middle',
			'large'
		);

		$this->types = array(
			'regular' => 'Обычная версия',
			'cecutient' => 'Версия для слабовидящих'
		);

		$this->backgrounds = array(
			'regular' => 'Обычный',
			'contrast' => 'Черный'
		);
	}

	/**
	 * Show built data
	 * @return self
	 */
	public function show()
	{
		?><div class="cecutient-block">
			<div class="cecutient-block-image">
				<i class="fa fa-eye fa-3x"></i>
			</div>
			<div class="cecutient-block-content margin-top-15">
				<div class="cecutient-block-content-link">
					<?php
					$aTypes = $this->types;
					reset($aTypes);
					$cecutientType = Core_Array::get($_COOKIE, 'cecutient-type', key($aTypes));

					foreach ($aTypes as $key => $value)
					{
						?><span data-type="<?php echo $key?>" class="<?php echo $key == $cecutientType ? 'cecutient-hidden' : ''?>">
							<?php echo htmlspecialchars($value)?>
						</span><?php
					}
					?>
				</div>
				<?php
					$ext_class = $cecutientType == 'regular' ? 'cecutient-hidden' : '';
				?>
				<div class="cecutient-block-content-fontsize <?php echo $ext_class?>">
					<?php
					$aFontsizes = $this->fontsizes;
					$cecutientFontsize = Core_Array::get($_COOKIE, 'fontsize', reset($aFontsizes));

					foreach ($this->fontsizes as $value)
					{
						?><span class="size-<?php echo $value?><?php echo $cecutientFontsize == $value ? ' fontsize-current' : ''?>" data-size="<?php echo $value?>">A</span><?php
					}
					?>
				</div>
				<div class="cecutient-block-content-background <?php echo $ext_class?>">
					<?php
					$aBackgrounds = $this->backgrounds;
					reset($aBackgrounds);
					$cecutientBackground = Core_Array::get($_COOKIE, 'background', key($aBackgrounds));

					foreach ($this->backgrounds as $key => $value)
					{
						?><span class="background-<?php echo $key?><?php echo $key == $cecutientBackground ? ' background-current' : ''?>" data-background="<?php echo $key?>">
							<?php echo htmlspecialchars($value)?>
						</span><?php
					}
					?>
				</div>
			</div>
		</div>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$.extend({
				applyCecutientOptions: function()
				{
					var currentType = $("div.cecutient-block-content-link > span:not('.cecutient-hidden')").data('type');

					$("div.cecutient-block-content-link > span").each(function(index){
					  var className = $(this).data('type');
					  if (className != currentType)
					  {
						  $('body').addClass(className);
					  }
					});
					$('body').removeClass(currentType);

					var currentFontSize = $("div.cecutient-block-content-fontsize > span.fontsize-current").data('size');
					$("div.cecutient-block-content-fontsize > span").each(function(index){
					  var className = 'fontsize-' + $(this).data('size');
					  if (className != currentFontSize)
					  {
						  $('body').removeClass(className);
					  }
					});
					$('body').addClass('fontsize-' + currentFontSize);

					var currentBackground = $("div.cecutient-block-content-background > span.background-current").data('background');

					$("div.cecutient-block-content-background > span").each(function( index ) {
					  var className = 'background-' + $(this).data('background');
					  if (className != currentBackground)
					  {
						  $('body').removeClass(className);
					  }
					});
					$('body').addClass('background-' + currentBackground);
				}
			});

			$("div.cecutient-block-content-link > span").on('click', function(){
				var type = $(this).data('type');
				document.cookie = "cecutient-type=" + type;
				$(this).parent().children().removeClass('cecutient-hidden');
				$(this).addClass('cecutient-hidden');

				// Change visibility
				$("div.cecutient-block-content-fontsize").toggleClass('cecutient-hidden');
				$("div.cecutient-block-content-background").toggleClass('cecutient-hidden');
				$("div.cecutient-block-content").toggleClass('margin-top-15');

				$.applyCecutientOptions();
			});
			$("div.cecutient-block-content-fontsize > span").on('click', function(){
				var size = $(this).data('size');
				document.cookie = "fontsize=" + size;
				$(this).parent().children().removeClass('fontsize-current');
				$(this).addClass('fontsize-current');

				$.applyCecutientOptions();
			});
			$("div.cecutient-block-content-background > span").on('click', function(){
				var background = $(this).data('background');
				document.cookie = "background=" + background;
				$(this).parent().children().removeClass('background-current');
				$(this).addClass('background-current');

				$.applyCecutientOptions();
			});

			$.applyCecutientOptions();
		});
		</script><?

		return $this;
	}
}