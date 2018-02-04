<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="no" method="html" omit-xml-declaration="no" version="1.0" media-type="text/html"/>
<xsl:template match="/site">
<div class="topLine">
	<div class="hCenter">
		<ul class="topLineMenu">
			<li class="logo"><a href="/" class=""><img src="/templates/template1/images/logo.png" alt="" /></a></li>
			<xsl:apply-templates select="structure[show=1]" />
		</ul>
	</div>
</div>
<script type="text/javascript">
	function process_about() {
		var sli = $('#sli');
		var height = Math.round(370/1900*sli.outerWidth());
		sli.css('height', height);
		sli.closest('.sli').css('height', height);
	}
	$(function() {
		$(window).on('resize', process_about);
		process_about();
	});
</script>
</xsl:template>
<xsl:template match="structure">
<!-- Запишем в константу ID структуры, данные для которой будут выводиться пользователю -->
<xsl:variable name="current_structure_id" select="/site/current_structure_id"/>
<li>
<!-- Определяем адрес ссылки -->
<xsl:variable name="link">
<xsl:choose>
<!-- Если внешняя ссылка -->
<xsl:when test="url != ''">
<xsl:value-of disable-output-escaping="yes" select="url"/>
</xsl:when>
<!-- Иначе если внутренняя ссылка -->
<xsl:otherwise>
<xsl:value-of disable-output-escaping="yes" select="link"/>
</xsl:otherwise>
</xsl:choose>
</xsl:variable>
<xsl:choose>
<!-- Выделяем текущую страницу жирным (если это текущая страница, либо у нее есть ребенок с ID, равным текущей) -->
<xsl:when test="$current_structure_id = @id or count(.//structure[@id=$current_structure_id]) = 1">
<a href="{$link}#here"><xsl:value-of disable-output-escaping="yes" select="name"/></a>
</xsl:when>
<!-- Иначе обычный вывод с пустым стилем -->
<xsl:otherwise>
<a href="{$link}#here"><xsl:value-of disable-output-escaping="yes" select="name"/></a>
</xsl:otherwise>
</xsl:choose>
</li>
</xsl:template>
</xsl:stylesheet>