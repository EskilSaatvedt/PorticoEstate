<?php
  /**************************************************************************\
  * phpGroupWare - administration                                            *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: class.uiapplications.inc.php,v 1.15 2006/11/27 08:39:41 sigurdne Exp $ */

	class uiapplications
	{
		var $public_functions = array
		(
			'get_list'	=> true,
			'add'		=> true,
			'edit'		=> true,
			'delete'	=> true,
			'register_all_hooks' => true
		);

		var $bo;
		var $nextmatchs;

		function uiapplications()
		{
			$this->bo =& CreateObject('admin.boapplications');
			$this->nextmatchs =& CreateObject('phpgwapi.nextmatchs');
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
		}

		function get_list()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$GLOBALS['phpgw']->template->set_file(array('applications' => 'applications.tpl'));
			$GLOBALS['phpgw']->template->set_block('applications','list','list');
			$GLOBALS['phpgw']->template->set_block('applications','row','row');

			$start = get_var('start',array('POST'));
			$sort = get_var('sort',array('GET'));
			$order = get_var('order',array('GET'));
			$offset = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			$apps = $this->bo->get_list();
			$total = count($apps);

			$sort = $sort ? $sort : 'ASC';

			if($sort == 'ASC')
			{
				ksort($apps);
			}
			else
			{
				krsort($apps);
			}

			if ($start && $offset)
			{
				$limit = $start + $offset;
			}
			elseif ($start && !$offset)
			{
				$limit = $start;
			}
			elseif(!$start && !$offset)
			{
				$limit = $total;
			}
			else
			{
				$start = 0;
				$limit = $offset;
			}

			if ($limit > $total)
			{
				$limit = $total;
			}

			$i = 0;
			$applications = array();
			while(list($app,$data) = @each($apps))
			{
				if($i >= $start && $i<= $limit)
				{
					$applications[$app] = $data;
				}
				$i++;
			}

			$GLOBALS['phpgw']->template->set_var('lang_installed',lang('Installed applications'));

			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($sort,'title','title','/index.php',lang('Title'),'&menuaction=admin.uiapplications.get_list'));
			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($total,$start));
			$GLOBALS['phpgw']->template->set_var('left',$this->nextmatchs->left('/index.php',$start,$total,'menuaction=admin.uiapplications.get_list'));
			$GLOBALS['phpgw']->template->set_var('right',$this->nextmatchs->right('index.php',$start,$total,'menuaction=admin.uiapplications.get_list'));

			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));
			$GLOBALS['phpgw']->template->set_var('lang_enabled',lang('Enabled'));

			$GLOBALS['phpgw']->template->set_var('new_action',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiapplications.add')));
			$GLOBALS['phpgw']->template->set_var('lang_note',lang('(To install new applications use<br><a href="setup/" target="setup">Setup</a> [Manage Applications] !!!)'));
			$GLOBALS['phpgw']->template->set_var('lang_add',lang('add'));

			$tr_color = '';
			foreach ( $applications as $key => $app )
			{
				$tr_color = $this->nextmatchs->alternate_row_class($tr_color);

				if($app['title'])
				{
					$name = $app['title'];
				}
				elseif($app['name'])
				{
					$name = $app['name'];
				}
				else
				{
					$name = '&nbsp;';
				}

				$GLOBALS['phpgw']->template->set_var('tr_color',$tr_color);
				$GLOBALS['phpgw']->template->set_var('name',$name);

				$GLOBALS['phpgw']->template->set_var('edit','<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiapplications.edit','app_name'=>urlencode($app['name']))) . '"> ' . lang('Edit') . ' </a>');
				$GLOBALS['phpgw']->template->set_var('delete','<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiapplications.delete','app_name'=>urlencode($app['name']))) . '"> ' . lang('Delete') . ' </a>');

				if ($app['status'])
				{
					$status = lang('Yes');
				}
				else
				{
					$status = '<b>' . lang('No') . '</b>';
				}
				$GLOBALS['phpgw']->template->set_var('status',$status);

				$GLOBALS['phpgw']->template->parse('rows','row',True);
			}

			$GLOBALS['phpgw']->template->pparse('out','list');
		}

		function display_row($label, $value)
		{
			$GLOBALS['phpgw']->template->set_var('tr_color',$this->nextmatchs->alternate_row_class());
			$GLOBALS['phpgw']->template->set_var('label',$label);
			$GLOBALS['phpgw']->template->set_var('value',$value);
			$GLOBALS['phpgw']->template->parse('rows','row',True);
		}

		function add()
		{
			$GLOBALS['phpgw']->template->set_file(array('application' => 'application_form.tpl'));
			$GLOBALS['phpgw']->template->set_block('application','form','form');
			$GLOBALS['phpgw']->template->set_block('application','row','row');

			if ( isset($_POST['submit']) && $_POST['submit'] )
			{
				$totalerrors = 0;

				$app_order    = isset($_POST['app_order']) ? (int) $_POST['app_order'] : 0;
				$n_app_name   = chop($_POST['n_app_name']);
				$n_app_title  = chop($_POST['n_app_title']);
				$n_app_status = $_POST['n_app_status'];

				if ($this->bo->exists($n_app_name))
				{
					$error[$totalerrors++] = lang('That application name already exists.');
				}
				if (preg_match("/\D/",$app_order))
				{
					$error[$totalerrors++] = lang('That application order must be a number.');
				}
				if (!$n_app_name)
				{
					$error[$totalerrors++] = lang('You must enter an application name.');
				}

				if (!$totalerrors)
				{
					$this->bo->add(array(
						'n_app_name'   => $n_app_name,
						'n_app_status' => $n_app_status,
						'app_order'    => $app_order
					));

					$GLOBALS['phpgw_info']['flags']['nodisplay'] = True;
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiapplications.get_list') );
					exit;
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('error','<p><center>' . $GLOBALS['phpgw']->common->error_list($error) . '</center><br>');
				}
			}
			else
			{	// else submit
				$GLOBALS['phpgw']->template->set_var('error','');
			}

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$GLOBALS['phpgw']->template->set_var('lang_header',lang('Add new application'));

			$GLOBALS['phpgw']->template->set_var('hidden_vars','');
			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiapplications.add')));

			$this->display_row(lang('application name'),'<input name="n_app_name" value="' . $n_app_name . '">');

			if(!isset($n_app_status))
			{
				$n_app_status = 1;
			}

			$selected[$n_app_status] = ' selected';
			$status_html = '<option value="0"' . $selected[0] . '>' . lang('Disabled') . '</option>'
				. '<option value="1"' . $selected[1] . '>' . lang('Enabled') . '</option>'
				. '<option value="2"' . $selected[2] . '>' . lang('Enabled - Hidden from navbar') . '</option>';
			$this->display_row(lang('Status'),'<select name="n_app_status">' . $status_html . '</select>');

			if (!$app_order)
			{
				$app_order = $this->bo->app_order();
			}

			$this->display_row(lang('Select which location this app should appear on the navbar, lowest (left) to highest (right)'),'<input name="app_order" value="' . $app_order . '">');

			$GLOBALS['phpgw']->template->set_var('lang_submit_button',lang('add'));
			$GLOBALS['phpgw']->template->pparse('out','form');
		}

		function edit()
		{
			$app_name = $_GET['app_name'];

			$GLOBALS['phpgw']->template->set_file(array('application' => 'application_form.tpl'));
			$GLOBALS['phpgw']->template->set_block('application','form','form');
			$GLOBALS['phpgw']->template->set_block('application','row','row');

			if ( isset($_POST['submit']) && $_POST['submit'] )
			{
				$totalerrors = 0;

				$old_app_name = $_POST['old_app_name'];
				$app_order    = isset($_POST['app_order']) ? (int) $_POST['app_order'] : 0;
				$n_app_name   = chop($_POST['n_app_name']);
				$n_app_status = $_POST['n_app_status'];

				if (! $n_app_name)
				{
					$error[$totalerrors++] = lang('You must enter an application name.');
				}

				if ($old_app_name != $n_app_name)
				{
					if ($this->bo->exists($n_app_name))
					{
						$error[$totalerrors++] = lang('That application name already exists.');
					}
				}
 
				if (! $totalerrors)
				{
					$this->bo->save(array(
						'n_app_name'   => $n_app_name,
						'n_app_status' => $n_app_status,
						'app_order'    => $app_order,
						'old_app_name' => $old_app_name
					));

					$GLOBALS['phpgw_info']['flags']['nodisplay'] = True;
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiapplications.get_list') );
					exit;
				}
			}

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			if (isset($totalerrors) && $totalerrors)
			{
				$GLOBALS['phpgw']->template->set_var('error','<p><center>' . $GLOBALS['phpgw']->common->error_list($error) . '</center><br>');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('error','');
				list($n_app_name,$n_app_title,$n_app_status,$old_app_name,$app_order) = $this->bo->read($app_name);
			}

			$GLOBALS['phpgw']->template->set_var('lang_header',lang('Edit application'));
			$GLOBALS['phpgw']->template->set_var('hidden_vars','<input type="hidden" name="old_app_name" value="' . $old_app_name . '">');
			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiapplications.edit')));

			$this->display_row(lang('application name'),'<input name="n_app_name" value="' . $n_app_name . '">');

			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_submit_button',lang('edit'));

			$selected[$n_app_status] = ' selected';
			$status_html = '<option value="0"' . (isset($selected[0])?$selected[0]:'') . '>' . lang('Disabled') . '</option>'
				. '<option value="1"' . (isset($selected[1])?$selected[1]:'') . '>' . lang('Enabled') . '</option>'
				. '<option value="2"' . (isset($selected[2])?$selected[2]:'') . '>' . lang('Enabled - Hidden from navbar') . '</option>';

			$this->display_row(lang("Status"),'<select name="n_app_status">' . $status_html . '</select>');
			$this->display_row(lang("Select which location this app should appear on the navbar, lowest (left) to highest (right)"),'<input name="app_order" value="' . $app_order . '">');

			$GLOBALS['phpgw']->template->set_var('select_status',$status_html);
			$GLOBALS['phpgw']->template->pparse('out','form');
		}

		function delete()
		{
			$app_name = $_GET['app_name'];

			if (!$app_name)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiapplications.get_list') );
			}

			$GLOBALS['phpgw']->template->set_file(array('body' => 'delete_common.tpl'));

			if ( isset($_GET['confirm']) && $_GET['confirm'] )
			{
				$this->bo->delete($app_name);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiapplications.get_list') );
				$GLOBALS['phpgw_info']['flags']['nodisplay'] = True;
				exit;
			}

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$GLOBALS['phpgw']->template->set_var('messages',lang('Are you sure you want to delete this application ?'));
			$GLOBALS['phpgw']->template->set_var('no','<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiapplications.get_list')) . '">' . lang('No') . '</a>');
			$GLOBALS['phpgw']->template->set_var('yes','<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiapplications.delete','app_name'=> urlencode($app_name), 'confirm'=>'True')) . '">' . lang('Yes') . '</a>');
			$GLOBALS['phpgw']->template->pparse('out','body');
		}
		
		function register_all_hooks()
		{
			if ( !isset($GLOBALS['phpgw']->hooks) && !is_object($GLOBALS['phpgw']->hooks) )
			{
				$GLOBALS['phpgw']->hooks = CreateObject('phpgwapi.hooks');
			}
			$GLOBALS['phpgw']->hooks->register_all_hooks();
			
			$GLOBALS['phpgw']->redirect_link('/admin/index.php');
		}
	}
?>
