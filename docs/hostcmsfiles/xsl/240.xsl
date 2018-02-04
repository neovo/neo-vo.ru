<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:hostcms="http://www.hostcms.ru/"
	exclude-result-prefixes="hostcms">
	<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>

	<!-- МагазинПоследниеПросмотренные -->
	<xsl:decimal-format name="my" decimal-separator="," grouping-separator=" "/>

	<!--Создадим ключ по идентификатору товара, чтобы оптимизировать выборку товаров при показе из в обратном хронологическом порядке-->
	<xsl:key name="items_id" match="/shop/shop_item" use="@id"/>

	<xsl:template match="/">
		<xsl:apply-templates select="/shop"/>
	</xsl:template>
	
	<xsl:template match="/shop">
		<!-- Есть товары -->
		<xsl:if test="shop_item">
			<div class="hits">
				<h3>Вы недавно смотрели</h3>
				<xsl:choose>

					<!--Если в XML существуют узлы, описывающие порядок показа товаров-->
					<xsl:when test="visit_order/last_item/node()">

						<!--то запустим цикл по этим узлам-->
						<ul>
							<xsl:for-each select="visit_order/last_item">

							<!--и для каждого id товара будем вызывать темплейт item, передавая в него узел соответствующего товара, выбранный с помощью ключа-->
								<xsl:apply-templates select="key('items_id', .)"/>
							</xsl:for-each>
						</ul>
					</xsl:when>

					<!--а в случае рандомного показа просто вызовем темплейт item для всех товаров описанных в XML-->
					<xsl:otherwise>
						<xsl:apply-templates select="shop_item"/>
					</xsl:otherwise>
				</xsl:choose>
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