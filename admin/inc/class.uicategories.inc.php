<?php
	/**************************************************************************\
	* phpGroupWare - Admin - Global categories                                 *
	* http://www.phpgroupware.org                                              *
	* Written by Bettina Gille [ceb@phpgroupware.org]                          *
	* -----------------------------------------------                          *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: class.uicategories.inc.php,v 1.47 2006/11/27 21:37:14 sigurdne Exp $ */
	/* $Source: /sources/phpgroupware/admin/inc/class.uicategories.inc.php,v $ */

	class uicategories
	{
		var $bo;
		var $nextmatchs;
		var $xslttpl;

		var $start;
		var $query;
		var $sort;
		var $order;
		var $cat_id;
		var $debug = False;

		var $public_functions = array
		(
			'index'  => True,
			'edit'   => True,
			'delete' => True
		);

		function uicategories()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;

			$this->bo			= CreateObject('admin.bocategories');
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->cat_id		= $this->bo->cat_id;
			if($this->debug) { $this->_debug_sqsof(); }
		}

		function _debug_sqsof()
		{
			$data = array(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id
			);
			echo '<br>UI:<br>';
			_debug_array($data);
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'	=> $this->start,
				'query'	=> $this->query,
				'sort'	=> $this->sort,
				'order'	=> $this->order
			);

			if(isset($this->cat_id))
			{
				$data['cat_id'] = $this->cat_id;
			}
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			$global_cats  = get_var('global_cats',array('POST','GET'));

			$GLOBALS['phpgw']->xslttpl->add_file('cats');

			$link_data = array
			(
				'menuaction'  => 'admin.uicategories.index',
				'appname'     => (isset($_REQUEST['appname'])?$_REQUEST['appname']:''),
				'global_cats' => $global_cats
			);

			if ( isset($_POST['add']) && $_POST['add'] )
			{
				$link_data['menuaction'] = 'admin.uicategories.edit';
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			if ( isset($_POST['done']) && $_POST['done'] )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uimainscreen.mainscreen') );
			}

			if ( isset($_REQUEST['appname']) && $_REQUEST['appname'])
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang($_REQUEST['appname']) . '&nbsp;' . lang('global categories') . ': ' . lang('category list');
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('global categories') . ': ' . lang('category list');
			}

			if (!$global_cats)
			{
				$global_cats = False;
			}

			$categories = $this->bo->get_list($global_cats);

			$cat_header[] = array
			(
				'sort_name'				=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'cat_name',
											'order'	=> $this->order,
											'extra'	=> $link_data
										)),
				'lang_add_sub'			=> lang('add sub'),
				'lang_name'				=> lang('name'),
				'lang_descr'			=> lang('description'),
				'lang_edit'				=> lang('edit'),
				'lang_delete'			=> lang('delete'),
				'lang_sort_statustext'	=> lang('sort the entries'),
				'sort_descr'			=> $this->nextmatchs->show_sort_order(array
											(
												'sort'	=> $this->sort,
												'var'	=> 'cat_description',
												'order'	=> $this->order,
												'extra'	=> $link_data
											))
			);

			$content = array();
			while (is_array($categories) && list(,$cat) = each($categories))
			{
				$level		= $cat['level'];
				$cat_name	= $GLOBALS['phpgw']->strip_html($cat['name']);

				$main = 'yes';
				if ($level > 0)
				{
					$space = ' . ';
					$spaceset = str_repeat($space,$level);
					$cat_name = $spaceset . $cat_name;
					$main = 'no';
				}

				$descr = $GLOBALS['phpgw']->strip_html($cat['description']);

				if ($_REQUEST['appname'] && $cat['app_name'] == 'phpgw')
				{
					$appendix = '&nbsp;[' . lang('Global') . ']';
				}
				else
				{
					$appendix = '';
				}

				if ($_REQUEST['appname'] && $cat['app_name'] == $_REQUEST['appname'])
				{
					$show_edit_del = True;
				}
				elseif(!$_REQUEST['appname'] && $cat['app_name'] == 'phpgw')
				{
					$show_edit_del = True;
				}
				else
				{
					$show_edit_del = False;
				}

				if ($show_edit_del)
				{
					$link_data['cat_id']		= $cat['id'];
					$link_data['menuaction']	= 'admin.uicategories.edit';
					$edit_url			= $GLOBALS['phpgw']->link('/index.php',$link_data);
					$lang_edit			= lang('edit');

					$link_data['menuaction']	= 'admin.uicategories.delete';
					$delete_url			= $GLOBALS['phpgw']->link('/index.php',$link_data);
					$lang_delete			= lang('delete');
				}
				else
				{
					$edit_url					= '';
					$lang_edit					= '';
					$delete_url					= '';
					$lang_delete				= '';
				}

				$link_data['menuaction'] = 'admin.uicategories.edit';
				$link_data['parent'] = $cat['id'];
				unset($link_data['cat_id']);
				$add_sub_url = $GLOBALS['phpgw']->link('/index.php',$link_data);

				$content[] = array
				(
					'name'				=> $cat_name . $appendix,
					'descr'				=> $descr,
					'main'				=> $main,
					'add_sub_url'			=> $add_sub_url,
					'edit_url'			=> $edit_url,
					'delete_url'			=> $delete_url,
					'lang_add_sub_statustext'	=> lang('add a subcategory'),
					'lang_edit_statustext'		=> lang('edit this category'),
					'lang_delete_statustext'	=> lang('delete this category'),
					'lang_add_sub'			=> lang('add sub'),
					'lang_edit'			=> $lang_edit,
					'lang_delete'			=> $lang_delete
				);
			}

			$link_data['menuaction'] = 'admin.uicategories.index';
			$link_data['parent'] = '';

			$cat_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a category'),
				'action_url'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_done'				=> lang('done'),
				'lang_done_statustext'	=> lang('return to admin mainscreen')
			);

			$link_data['menuaction'] = 'admin.uicategories.index';

			$nm = array
			(
				'start_record'	=> $this->start,
 				'num_records'	=> count($categories),
 				'all_records'	=> $this->bo->cats->total_records,
				'link_data'		=> $link_data
			);

			$data = array
			(
				'nm_data'		=> $this->nextmatchs->xslt_nm($nm),
				'search_data'	=> $this->nextmatchs->xslt_search(array('query' => $this->query,'link_data' => $link_data)),
				'cat_header'	=> $cat_header,
				'cat_data'		=> $content,
				'cat_add'		=> $cat_add 
			);

			$this->save_sessiondata();
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('cat_list' => $data));
		}

		function edit()
		{
			$global_cats	= get_var('global_cats',array('POST','GET'));
			$parent		= get_var('parent',array('GET'));
			$values		= get_var('values',array('POST'));

			$message = '';
			$link_data = array
			(
				'menuaction'  => 'admin.uicategories.index',
				'appname'     => $_REQUEST['appname'],
				'global_cats' => $global_cats
			);

			if ( isset($values['cancel']) && $values['cancel'] )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ( (isset($values['save']) && $values['save'] )
				|| (isset($values['apply']) && $values['apply']) )
			{
				$values['cat_id'] = $this->cat_id;
				$values['access'] = 'public';

				$error = $this->bo->check_values($values);
				if (is_array($error))
				{
					$message = $GLOBALS['phpgw']->common->error_list($error);
				}
				else
				{
					$this->cat_id = $this->bo->save_cat($values);
					if ( isset($values['apply']) && $values['apply'] )
					{
						$message = lang('Category %1 has been saved !',$values['name']);
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
					}
				}
			}

			if ($this->cat_id)
			{
				$cats = $this->bo->cats->return_single($this->cat_id);
			}
			else
			{
				$cats = array(array
				(
					'id'			=> 0,
					'name'			=> '',
					'description'	=> '',
					'parent'		=> 0
				));
			}
			$parent = $cats[0]['parent'];

			if ($_REQUEST['appname'])
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang($_REQUEST['appname']) . '&nbsp;' . lang('global categories') . ': ' . ($this->cat_id?lang('edit category'):lang('add category'));
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('global categories') . ': ' . (isset($function)?$function:'');
			}

			$GLOBALS['phpgw']->xslttpl->add_file('cats');

			if ($_REQUEST['appname'])
			{
				$GLOBALS['phpgw']->template->set_var('title_categories',lang('Edit global category for %1',lang($_REQUEST['appname'])));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('title_categories',lang('Edit global category'));
			}

			$data = array
			(
				'img_color_selector'	=> $GLOBALS['phpgw']->common->image('phpgwapi', 'color_selector'),
				'lang_name'				=> lang('name'),
				'lang_color'			=> lang('color'),
				'lang_color_selector'	=> lang('color selector'),
				'lang_descr'			=> lang('description'),
				'lang_parent'			=> lang('parent category'),
				'old_parent'			=> $parent,
				'lang_save'				=> lang('save'),
				'lang_apply'			=> lang('apply'),
				'lang_cancel'			=> lang('cancel'),
				'value_name'			=> $GLOBALS['phpgw']->strip_html($cats[0]['name']),
				'value_descr'			=> $GLOBALS['phpgw']->strip_html($cats[0]['description']),
				'message'				=> $message,
				'lang_content_statustext'	=> lang('enter a description for the category'),
				'lang_cancel_statustext'	=> lang('leave the category untouched and return back to the list'),
				'lang_save_statustext'		=> lang('save the category and return back to the list'),
				'lang_apply_statustext'		=> lang('save the category'),
				'cat_select'			=> $this->bo->cats->formatted_xslt_list(array('select_name' => 'values[parent]', 'selected' => $parent,'self' => $this->cat_id,'globals' => $global_cats))
			);

			$link_data['menuaction'] = 'admin.uicategories.edit';
			if ($this->cat_id)
			{
				$link_data['cat_id']	= $this->cat_id;
			}
			$data['edit_url'] = $GLOBALS['phpgw']->link('/index.php',$link_data);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('cat_edit' => $data));
		}

		function delete()
		{
			$global_cats  = $_REQUEST['global_cats'];

			$link_data = array
			(
				'menuaction'  => 'admin.uicategories.index',
				'appname'     => $_REQUEST['appname'],
				'global_cats' => $global_cats
			);

			if ( (isset($_POST['cancel']) && $_POST['cancel']) || !$this->cat_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ( isset($_POST['confirm']) && $_POST['confirm'] )
			{
				if ( isset($_POST['subs']) && $_POST['susbs'] )
				{
					switch ($_POST['subs'])
					{
						case 'move':
							$this->bo->delete($this->cat_id, false, true);
							$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
							break;
						case 'drop':
							$this->bo->delete($this->cat_id, true);
							$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
							break;
						default:
							$error		= array('Please choose one of the methods to handle the subcategories');
							$msgbox_error	= $GLOBALS['phpgw']->common->error_list($error);
							break;
					}
				}
				else
				{
					$this->bo->delete($this->cat_id);
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = ($_REQUEST['appname']?lang($_REQUEST['appname']) . '&nbsp;':'') . lang('global categories') . ': ' . lang('delete category');

			$type = ($_REQUEST['appname']?'noglobalapp':'noglobal');

			$apps_cats = $this->bo->exists(array
			(
				'type'		=> $type,
				'cat_name'	=> '',
				'cat_id'	=> $this->cat_id
			));

			//Initialize our variables
			$msgbox_error = '';
			$show_done = '';
			$subs = '';
			$lang_sub_select_move = '';
			$lang_sub_select_drop = '';

			if ($apps_cats)
			{
				$error = array('This category is currently being used by applications as a parent category',
								'You will need to reassign these subcategories before you can delete this category');

				$msgbox_error	= $GLOBALS['phpgw']->common->error_list($error);
				$show_done		= 'yes';
			}
			else
			{
				$confirm_msg = lang('Are you sure you want to delete this global category ?');

				$exists = $this->bo->exists(array
				(
					'type'     => 'subs',
					'cat_name' => '',
					'cat_id'   => $this->cat_id
				));

				if ($exists)
				{
					$subs					= 'yes';
					$lang_sub_select_move	= lang('Do you want to move all global subcategories one level down ?');
					$lang_sub_select_drop	= lang('Do you want to delete all global subcategories ?');
				}
			}

			$link_data['menuaction']	= 'admin.uicategories.delete';
			$link_data['cat_id']		= $this->cat_id;

			$data = array
			(
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'show_done'				=> $show_done,
				'msgbox_data'			=> $msgbox_error,
				'lang_delete'			=> lang('delete'),
				'subs'					=> $subs,
				'lang_confirm_msg'		=> $confirm_msg,
				'lang_sub_select_move'	=> $lang_sub_select_move,
				'lang_sub_select_drop'	=> $lang_sub_select_drop,
				'lang_done_statustext'	=> lang('back to the list'),
				'lang_no'				=> lang('no'),
				'lang_no_statustext'	=> lang('do NOT delete the category and return back to the list'),
				'lang_yes'				=> lang('yes'),
				'lang_yes_statustext'	=> lang('delete the category')
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
?>
