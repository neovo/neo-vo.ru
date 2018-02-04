<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:hostcms="http://www.hostcms.ru/"
exclude-result-prefixes="hostcms">
<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>
<!-- МагазинТовар -->
<xsl:decimal-format name="my" decimal-separator="," grouping-separator=" "/>
<xsl:template match="/shop">
<xsl:apply-templates select="shop_item"/>
</xsl:template>

<xsl:template match="property_value" mode="file_tz">
	<xsl:if test="value/node() and value != '' or file/node() and file != ''">
		<div class="file_tz">
			<a href="{../dir}{file}" target="_blank" title="{file_description}"><xsl:value-of select="file_name"/></a>
		</div>
	</xsl:if>	
</xsl:template>

<xsl:template match="shop_item">
<!-- Получаем ID родительской группы и записываем в переменную $group -->
<xsl:variable name="group" select="/shop/group"/>
<div class="detailItem">
						
						<h1 class="title"><xsl:value-of disable-output-escaping="yes" select="name"/></h1>

						
		<xsl:if test="count(associated/shop_item) &gt; 0">
	<div>
<div class="rekitems" style="position:absolute;margin-left: 590px;">
<p>Рекомендованные товары</p>
	<ul class="erty">
				<xsl:apply-templates select="associated/shop_item"/>
			</ul>
</div>  
</div>
</xsl:if>
                        
						<div class="galery clearfix">
						
							<div class="bigImgs">
								<ul>
								<xsl:if test="image_small != ''">
									<li class="itemImg1 active">
										<div><img src="{dir}{image_large}" alt="{name}" /></div>
									</li>
									</xsl:if>
<xsl:if test="count(property_value[file != ''])">
<xsl:apply-templates select="property_value[file != '']" mode="fds"/>
</xsl:if>
								</ul>
							</div>
							
							<div class="prewImgs">
							<div class="jcarousel">
								<ul><xsl:if test="image_small != ''">
									<li class="active">
										<a href="#" rel="1"><img src="{dir}{image_small}" alt="{name}" width="109" /></a>
									</li>
									</xsl:if>
<xsl:if test="count(property_value[file != ''])">
<xsl:apply-templates select="property_value[file != '']" mode="fds2"/>
</xsl:if>
								</ul>
								</div>
								<xsl:if test="count(property_value[file != ''])">
								<a href="#" class="jcarousel-control-prev">Prev</a>
                <a href="#" class="jcarousel-control-next">Next</a>
				</xsl:if>
							</div>
						
						</div>
						<div style="clear:both;"></div>
						<div class="props clearfix">
							<xsl:if test="format-number(price, '### ##0', 'my') !=0">
							<div class="detailPrice clearfix">
								<div class="propTitle">Цена:</div>
								<div class="propValue">
								<span><xsl:value-of select="format-number(price, '### ##0', 'my')"/></span><xsl:text> </xsl:text><xsl:value-of select="currency"/></div>
							</div>
							</xsl:if>

							<xsl:if test="count(property_value[tag_name='file_tz']) &gt; 0">
								<xsl:apply-templates select="property_value[tag_name='file_tz']" mode="file_tz"/>
							</xsl:if>

							<div class="detailDescr clearfix">
								<div class="propTitle">Описание:</div>
								<div class="propValue"><xsl:value-of disable-output-escaping="yes" select="text"/></div>
							</div>
						</div>
						
						<div class="wrapLink">
							<a href="#callBack" class="link formUp">Отправить заявку</a>
						</div>
						
					</div>

</xsl:template>

<!-- Вывод строки со значением свойства -->
<xsl:template match="property_value" mode="fds">
<xsl:if test="value/node() and value != '' or file/node() and file != ''">
<xsl:variable name="property_id" select="property_id" />
<xsl:variable name="property" select="/shop/shop_item_properties//property[@id=$property_id]" />
<xsl:choose>
<xsl:when test="$property/type = 2">

<xsl:choose>
<xsl:when test="position() = 1">
<li class="itemImg2"><img src="{../dir}{file}" alt="" width="434" /></li>
</xsl:when>
<xsl:when test="position() = 2">
<li class="itemImg3"><img src="{../dir}{file}" alt="" width="434" /></li>
</xsl:when>
<xsl:when test="position() = 3">
<li class="itemImg4"><img src="{../dir}{file}" alt="" width="434" /></li>
</xsl:when>
<xsl:when test="position() = 4">
<li class="itemImg5"><img src="{../dir}{file}" alt="" width="434" /></li>
</xsl:when>
<xsl:when test="position() = 5">
<li class="itemImg6"><img src="{../dir}{file}" alt="" width="434" /></li>
</xsl:when>
<xsl:when test="position() = 6">
<li class="itemImg7"><img src="{../dir}{file}" alt="" width="434" /></li>
</xsl:when>
<xsl:when test="position() = 7">
<li class="itemImg8"><img src="{../dir}{file}" alt="" width="434" /></li>
</xsl:when>
<xsl:otherwise>
</xsl:otherwise>
</xsl:choose>

</xsl:when>
<xsl:otherwise>
</xsl:otherwise>
</xsl:choose>
</xsl:if>
</xsl:template>

<!-- Вывод строки со значением свойства -->
<xsl:template match="property_value" mode="fds2">
<xsl:if test="value/node() and value != '' or file/node() and file != ''">
<xsl:variable name="property_id" select="property_id" />
<xsl:variable name="property" select="/shop/shop_item_properties//property[@id=$property_id]" />
<xsl:choose>
<xsl:when test="$property/type = 2">

<xsl:choose>
<xsl:when test="position() = 1">
<li><a href="#" rel="2"><img src="{../dir}{file_small}" alt="" width="109" /></a></li>
</xsl:when>
<xsl:when test="position() = 2">
<li><a href="#" rel="3"><img src="{../dir}{file_small}" alt="" width="109" /></a></li>
</xsl:when>
<xsl:when test="position() = 3">
<li><a href="#" rel="4"><img src="{../dir}{file_small}" alt="" width="109" /></a></li>
</xsl:when>
<xsl:when test="position() = 4">
<li><a href="#" rel="5"><img src="{../dir}{file_small}" alt="" width="109" /></a></li>
</xsl:when>
<xsl:when test="position() = 5">
<li><a href="#" rel="6"><img src="{../dir}{file_small}" alt="" width="109" /></a></li></xsl:when>
<xsl:when test="position() = 6">
<li><a href="#" rel="7"><img src="{../dir}{file_small}" alt="" width="109" /></a></li></xsl:when>
<xsl:when test="position() = 7">
<li><a href="#" rel="8"><img src="{../dir}{file_small}" alt="" width="109" /></a></li></xsl:when>
<xsl:otherwise>
</xsl:otherwise>
</xsl:choose>

</xsl:when>
<xsl:otherwise>
</xsl:otherwise>
</xsl:choose>
</xsl:if>
</xsl:template>

	<!-- Шаблон для сопутствующих товаров -->
	<xsl:template match="associated/shop_item">
		<li class="clearfix">
			<xsl:if test="image_small != ''">
				<img class="icon" src="{dir}{image_small}" alt="{name}" />
			</xsl:if>
			<a href="{url}#here"><xsl:value-of disable-output-escaping="yes" select="name"/></a>
			<xsl:if test="format-number(price, '### ##0', 'my') !=0">
				<div class="clearfix"></div>				
				<div class="price">
					<xsl:if test="image_small != ''">
						<a href="{url}#here"><img class="priceImg" src="{dir}{image_small}" alt="{name}" width="109" /></a>
					</xsl:if>
					<span>
						<xsl:value-of select="format-number(price, '### ##0', 'my')"/><img class="priceRub" src="/templates/template1/images/rr.png" width="16" alt="&#8381;" />
					</span>
				</div>
			</xsl:if>			
		</li>
	</xsl:template>
</xsl:stylesheet>