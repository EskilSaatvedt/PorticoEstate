
<!-- $Id: price_item.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view" />
		</xsl:when>
	</xsl:choose>

</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
		<xsl:text> H:i</xsl:text>
	</xsl:variable>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<xsl:variable name="mode">
		<xsl:value-of select="mode"/>
	</xsl:variable>

	<div>
		<script type="text/javascript">
			var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required', 'customer')"/>;
		</script>
		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
				<div id="first_tab">
					<xsl:if test="booking/id > 0">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'id')"/>
							</label>
							<input type="hidden" name="id" value="{booking/id}"/>
							<input type="hidden" name="application_id" value="{booking/application_id}"/>
							<xsl:value-of select="booking/id"/>
						</div>
					</xsl:if>
					<fieldset>
						<legend>
							<xsl:value-of select="php:function('lang', 'vendor')"/>
						</legend>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'vendor')"/>
							</label>
							<xsl:value-of select="application/vendor_name"/>
						</div>
						<div class="pure-control-group">
							<label>
								<a href="{application_url}" target="_blank">
									<xsl:value-of select="php:function('lang', 'application')"/>
								</a>

							</label>
							<xsl:value-of select="booking/application_name"/>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'remark')"/>
							</label>
							<xsl:value-of select="application/remark"/>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact name')"/>
							</label>
							<xsl:value-of select="application/contact_name"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact email')"/>
							</label>
							<xsl:value-of select="application/contact_email"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact phone')"/>
							</label>
							<xsl:value-of select="application/contact_phone"/>

						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'type')"/>
							</label>
							<div class="pure-custom">
								<table class="pure-table pure-table-bordered" border="0" cellspacing="2" cellpadding="2">
									<thead>
										<tr>
											<th>
												<xsl:value-of select="php:function('lang', 'select')"/>
											</th>
											<th>
												<xsl:value-of select="php:function('lang', 'type')"/>
											</th>
										</tr>
									</thead>
									<tbody>
										<xsl:for-each select="application_type_list">
											<tr>
												<td>
													<xsl:if test="selected = 1">
														<xsl:text>X</xsl:text>
													</xsl:if>
												</td>
												<td>
													<xsl:value-of disable-output-escaping="yes" select="name"/>
												</td>
											</tr>
										</xsl:for-each>
									</tbody>
								</table>
							</div>

						</div>




					</fieldset>

					<fieldset>

						<legend>
							<xsl:value-of select="php:function('lang', 'customer')"/>
						</legend>

						<div class="pure-control-group">
							<xsl:variable name="lang_customer">
								<xsl:value-of select="php:function('lang', 'customer')"/>
							</xsl:variable>
							<label>
								<xsl:choose>
									<xsl:when test="booking/customer_id > 0">
										<div id="customer_url">
											<a href="{customer_url}" target="_blank">
												<xsl:value-of select="$lang_customer"/>
											</a>
										</div>
									</xsl:when>
									<xsl:otherwise>
										<div id="customer_url">
											<xsl:value-of select="$lang_customer"/>
										</div>
									</xsl:otherwise>
								</xsl:choose>
							</label>
							<input type="hidden" id="customer_id" name="customer_id"  value="{booking/customer_id}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_customer"/>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_customer"/>
								</xsl:attribute>
							</input>
							<input type="text" id="customer_name" name="customer_name" value="{booking/customer_name}">
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_customer"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
							</input>
							<xsl:text> </xsl:text>
							<a href="{new_customer_url}" target="_blank">
								<xsl:value-of select="php:function('lang', 'new')"/>
							</a>
							<div id="customer_container"/>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact name')"/>
							</label>
							<input type="text" id="customer_contact_name"  name="customer_contact_name" value="{booking/customer_contact_name}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'contact_name')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'email')"/>
							</label>
							<input type="text" id="customer_contact_email" name="customer_contact_email"  value="{booking/customer_contact_email}">
								<xsl:attribute name="data-validation">
									<xsl:text>email</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'email')"/>
								</xsl:attribute>
							</input>
							<xsl:choose>
								<xsl:when test="valid_email = 1">
									<xsl:text> </xsl:text>
									<a href="{link_create_user}">
										<xsl:value-of select="php:function('lang', 'create_user_based_on_email_link')"/>
									</a>
								</xsl:when>
							</xsl:choose>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact phone')"/>
							</label>
							<input type="text" id="customer_contact_phone"  name="customer_contact_phone" value="{booking/customer_contact_phone}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'contact_phone')"/>
								</xsl:attribute>
							</input>
						</div>
					</fieldset>

					<fieldset>

						<legend>
							<xsl:value-of select="php:function('lang', 'booking')"/>
						</legend>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'active')"/>
							</label>
							<input type="checkbox" name="active" id="active" value="1" readonly="readonly">
								<xsl:if test="booking/active = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'completed')"/>
							</label>
							<input type="checkbox" name="completed" id="completed" value="1">
								<xsl:if test="booking/completed = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
						</div>

						<div class="pure-control-group">
							<xsl:variable name="lang_from">
								<xsl:value-of select="php:function('lang', 'from')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_from"/>
							</label>
							<input type="text" id="from_" name="from_" size="16" readonly="readonly">
								<xsl:if test="booking/from_ != 0 and booking/from_ != ''">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('show_date', number(booking/from_), $date_format)"/>
									</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_from"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'timespan')"/>
							</label>
							<xsl:value-of select="application/timespan"/>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'to')"/>
							</label>
							<xsl:value-of select="php:function('show_date', number(booking/to_), $date_format)"/>
						</div>
						
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'remark')"/>
							</label>
							<textarea cols="47" rows="7" name="remark">
								<xsl:value-of select="booking/remark"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'comment')"/>
							</label>
							<textarea cols="47" rows="7" name="comment">
								<xsl:value-of select="booking/comment"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'details')"/>
							</label>
							<div class="pure-custom">
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_0'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl'/>
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
											<xsl:with-param name="data" select ='data'/>
											<xsl:with-param name="config" select ='config'/>
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:attribute>
				</input>
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>


<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="showing">

				</div>
			</div>
			<div class="proplist-col">
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
</xsl:template>
