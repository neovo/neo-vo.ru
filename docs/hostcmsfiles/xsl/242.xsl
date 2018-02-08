<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>

	<!-- МагазинЛидерыПродаж -->
	<xsl:decimal-format name="my" decimal-separator="," grouping-separator=" "/>

	<xsl:template match="/">
		<xsl:apply-templates select="/shop"/>
	</xsl:template>
	
	<xsl:template match="/shop">
		<!-- Есть товары -->
		<xsl:if test="shop_item">
			<div class="hits">
				<h3>Хиты продаж</h3>
				<!-- Выводим товары магазина -->
				<ul>
					<xsl:apply-templates select="shop_item" />
				</ul>
			</div>
		</xsl:if>
	</xsl:template>
	
	<!-- Шаблон для товара -->
	<xsl:template match="shop_item">
		<li class="item">
			<div class="image"><a href="{url}">
				<xsl:choose>
					<xsl:when test="image_small != ''">
						<img src="{dir}{image_small}" alt="{name}" title="{name}"/>
					</xsl:when>
					<xsl:otherwise>
						<img src="/images/no-image.png" alt="{name}" title="{name}"/>
					</xsl:otherwise>
				</xsl:choose>
			</a></div>
			<a class="name" href="{url}">
				<xsl:value-of select="name" />
			</a>
			<xsl:if test="format-number(price, '### ##0', 'my') != 0">
				<div class="price">
					<xsl:value-of select="format-number(price, '### ##0', 'my')"/><img class="currency" src="/templates/template1/images/rr.png" alt="&#8381;" />
				</div>
			</xsl:if>
			<div class="link">
				<div class="button">
					<a href="{url}">Характеристики и цены</a>
				</div>
			</div>
		</li>
	</xsl:template>
</xsl:stylesheet>