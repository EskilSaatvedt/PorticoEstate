<?php
	/**
	* Template header
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/


	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	// we hack the template root here as this is the template set of last resort
	$tpl = CreateObject('phpgwapi.Template', dirname(__FILE__), "remove");
	$tpl->set_file(array('head' => 'head.tpl'));
	$tpl->set_block('head', 'theme_stylesheet', 'theme_stylesheets');

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$stylesheets = array();
	$stylesheets[] = "/phpgwapi/templates/pure/css/global.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-extension.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-min.css";
    $stylesheets[] = "/phpgwapi/js/DataTables/extensions/Responsive/css/responsive.dataTables.min.css";
	if( !isset($GLOBALS['phpgw_info']['flags']['noframework']) )
	{

		$javascripts = array
		(
			"/phpgwapi/js/json/json.js",
			"/phpgwapi/templates/portico/js/base.js"

		);

		$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/simple/css/base.css";
	}

	if(file_exists(PHPGW_SERVER_ROOT . '/phpgwapi/templates/simple/css/' . $GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] . '.css'))
	{
		$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/simple/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}
	else
	{
		$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/simple/css/simple.css";
		$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] = 'simple';
	}

	if(file_exists(PHPGW_SERVER_ROOT . "/{$app}/templates/base/css/base.css"))
	{
		$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/base/css/base.css";
	}

	if(file_exists(PHPGW_SERVER_ROOT . "/{$app}/templates/simple/css/base.css"))
	{
		$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/simple/css/base.css";
	}

	if(file_exists(PHPGW_SERVER_ROOT . "/{$app}/templates/simple/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css"))
	{
		$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/simple/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}

	foreach ( $stylesheets as $style )
	{
		$tpl->set_var('theme_style', $style);
		$tpl->parse('theme_stylesheets', 'theme_stylesheet', true);
	}

	$app = $app ? ' ['.(isset($GLOBALS['phpgw_info']['apps'][$app]) ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : lang($app)).']':'';

	$tpl->set_var(array
	(
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'      => PHPGW_IMAGES_DIR . '/favicon.ico',
		'img_shortcut'  => PHPGW_IMAGES_DIR . '/favicon.ico',
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),		
		'website_title'	=> $GLOBALS['phpgw_info']['server']['site_title'] . $app,
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
	));

	$tpl->pfp('out','head');
	unset($tpl);

