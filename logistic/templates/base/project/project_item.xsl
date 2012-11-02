<!-- $Id$ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<xsl:call-template name="yui_phpgw_i18n"/>
<div class="yui-navset yui-navset-top">
	
	<h1>
			<xsl:value-of select="php:function('lang', 'Project')" />
	</h1>
	
	<div id="project_details" class="content-wrp">
		<div id="details">
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uiproject.save')" />
			</xsl:variable>
			<form action="{$action_url}" method="post">
				<input type="hidden" name="id" value = "{value_id}">
				</input>
				<dl class="proplist-col">
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang','Project title')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="project/error_msg_array/name != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/name" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line">Angi navn for prosjektet</div>
							<input type="text" name="name" id="name" value="{project/name}" size="100"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/name" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="project_type"><xsl:value-of select="php:function('lang','Project_type')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="project/error_msg_array/project_type_id != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/project_type_id" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line">Angi prosjekttype</div>
							<select id="project_type_id" name="project_type_id">
								<xsl:apply-templates select="options"/>
							</select>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/project_type_label" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="project/error_msg_array/description != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/description" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line">Gi en beskrivelse av prosjektet</div>
							<textarea id="description" name="description" rows="5" cols="60"><xsl:value-of select="project/description" disable-output-escaping="yes"/></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/description" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="start_date">Startdato</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="project/error_msg_array/start_date != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/start_date" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<input class="date" id="start_date" name="start_date" type="text">
						    	<xsl:if test="project/start_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(project/start_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>
					    	<span class="help_text line">Angi startdato for prosjektet</span>
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(project/start_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<dt>
						<label for="end_date">Sluttdato</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="project/error_msg_array/end_date != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/end_date" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<input class="date" id="end_date" name="end_date" type="text">
						    	<xsl:if test="project/end_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(project/end_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>
					    	<span class="help_text line">Angi sluttdato for prosjektet</span>
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(project/end_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				</dl>

				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save_project" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel_project" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit_project" value="{$lang_edit}" title = "{$lang_edit}" />
							
							<xsl:variable name="add_new_project_params">
								<xsl:text>menuaction:logistic.uiproject.edit, project_id:</xsl:text>
								<xsl:value-of select="project/id" />
							</xsl:variable>
							<xsl:variable name="add_new_project_url">
								<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $add_new_project_params )" />
							</xsl:variable>
							<a class="btn non-focus" href="{$add_new_project_url}"><xsl:value-of select="php:function('lang', 'Add project')" /></a>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</form>
		</div>
	</div>
</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
