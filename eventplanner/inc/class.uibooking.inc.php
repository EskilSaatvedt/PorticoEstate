<?php
/**
	 * phpGroupWare - eventplanner: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this booking was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package eventplanner
	 * @subpackage booking
	 * @version $Id: $
	 */
	phpgw::import_class('eventplanner.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('eventplanner', 'booking', 'inc/model/');

	class eventplanner_uibooking extends eventplanner_uicommon
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
			'save_ajax'	=> true
		);

		protected
			$fields,
			$permissions;

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('eventplanner::booking');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('booking');
			$this->bo = createObject('eventplanner.bobooking');
			$this->fields = eventplanner_booking::get_fields();
			$this->permissions = eventplanner_booking::get_instance()->get_permission_array();
		}


		public function index()
		{
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_jquery::load_widget('autocomplete');

			$function_msg = lang('booking');

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'autocomplete',
								'name' => 'application',
								'app' => 'eventplanner',
								'ui' => 'application',
								'label_attr' => 'title',
								'text' => lang('application') . ':',
								'requestGenerator' => 'requestWithApplicationFilter'
							),
							array(
								'type' => 'checkbox',
								'name' => 'filter_active',
								'text' => lang('showall'),
								'value' =>  1,
								'checked'=> 1,
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'eventplanner.uibooking.index',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => 'eventplanner.uibooking.add')),
					'editor_action' => '',
					'field' => parent::_get_fields()
				)
			);

			$parameters = array(
				'parameter' => array(
					array(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'eventplanner.uibooking.view'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'eventplanner.uibooking.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript('eventplanner', 'portico', 'booking.index.js');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}

		/*
		 * Edit the price item with the id given in the http variable 'id'
		 */

		public function edit( $values = array(), $mode = 'edit' )
		{
			$active_tab = !empty($values['active_tab']) ? $values['active_tab'] : phpgw::get_var('active_tab', 'string', 'REQUEST', 'first_tab');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			if (!empty($values['object']))
			{
				$booking = $values['object'];
			}
			else
			{
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$booking = $this->bo->read_single($id);
			}

			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('booking'),
				'link' => '#first_tab',
				'function' => "set_tab('first_tab')"
			);

			$bocommon = CreateObject('property.bocommon');

			$comments = (array)$booking->comments;
			foreach ($comments as $key => &$comment)
			{
				$comment['value_count'] = $key +1;
				$comment['value_date'] = $GLOBALS['phpgw']->common->show_date($comment['time']);
			}

			$comments_def = array(
				array('key' => 'from_', 'label' => lang('From'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'to_', 'label' => lang('To'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'author', 'label' => lang('User'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'comment', 'label' => lang('Note'), 'sortable' => false, 'resizeable' => true)
			);
 
			$datatable_def[] = array(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $comments_def,
				'data' => json_encode($comments),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$data = array(
				'datatable_def' => $datatable_def,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uibooking.save')),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uibooking.index',)),
				'booking' => $booking,
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript('eventplanner', 'portico', 'booking.edit.js');
			self::render_template_xsl(array('booking', 'datatable_inline'), array($mode => $data));
		}
		
		public function save()
		{
			parent::save();
		}
		public function save_ajax()
		{
			return parent::save(true);
		}
	}