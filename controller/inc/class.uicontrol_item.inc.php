<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('property.boevent');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.socontrol_item');
	phpgw::import_class('controller.socontrol_group');
	phpgw::import_class('controller.socontrol_area');
	
	include_class('controller', 'control', 'inc/model/');

	class controller_uicontrol_item extends controller_uicommon
	{
		private $so;
		private $so_control_item;
		private $so_control_group;
		private $so_control_area;
		
		public $public_functions = array
		(
			'index'	=> true,
			'query'	=>	true,
			'display_control_items'	=> true
		);

		public function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('controller.socontrol');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_area = CreateObject('controller.socontrol_area');
		}
		
		public function index()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::control_item";
			
			self::set_active_menu('controller::control_item');

			if(isset($_POST['save_control_item'])) // The user has pressed the save button
			{
				if(isset($control_item)) // Edit control
				{
					$control_item->set_title(phpgw::get_var('title'));
					$control_item->set_required(phpgw::get_var('required'));
					$control_item->set_what_to_do( phpgw::get_var('what_to_do') );
					$control_item->set_how_to_do( phpgw::get_var('how_to_do') );
					$control_item->set_control_group_id( strtoint( phpgw::get_var('control_group_id') ) );
					$control_item->set_control_area_id( strtoint( phpgw::get_var('control_area_id') ) );
									
					$this->so->add($control_item);
				}
				else // Add new control
				{

					$control_item = new controller_control();
					
					$control_item->set_title(phpgw::get_var('title'));
					$control_item->set_required(phpgw::get_var('required'));
					$control_item->set_what_to_desc( phpgw::get_var('what_to_do') );
					$control_item->set_how_to_desc( phpgw::get_var('how_to_do') );
					$control_item->set_control_group_id( strtoint( phpgw::get_var('control_group_id') ) );
					$control_item->set_control_area_id( strtoint( phpgw::get_var('control_area_id') ) );
									
					$this->so->add($control_item);
				}
			}
			
			$control_area_array = $this->so_control_area->get_control_area_array();
			$control_group_array = $this->so_control_group->get_control_group_array();
			

			if($this->flash_msgs)
			{
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			}

			foreach ($control_area_array as $control_area)
			{
				$control_area_options[] = array
				(
					'id'	=> $control_area->get_id(),
					'name'	=> $control_area->get_title()
					 
				);
			}

			foreach ($control_group_array as $control_group)
			{
				$control_group_options[] = array
				(
					'id'	=> $control_group->get_id(),
					'name'	=> $control_group->get_group_name()
					 
				);
			}

			$data = array
			(
				'value_id'				=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'editable' 				=> true,
				'control_area'			=> array('options' => $control_area_options),
				'control_group'			=> array('options' => $control_group_options),
			);


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Control_item');

/*
			$GLOBALS['phpgw']->richtext->replace_element('what_to_do');
			$GLOBALS['phpgw']->richtext->replace_element('how_to_do');
			$GLOBALS['phpgw']->richtext->generate_script();
*/

//			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'controller.item', 'controller' );

			self::render_template_xsl('control_item', $data);
		}

		public function display_control_items()
		{
			//$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::control_item_list";
			
			self::set_active_menu('controller::control_item::control_item_list');
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->display_control_items_json();
			}
			
			self::add_javascript('controller', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New application'),
								'href' => self::link(array('menuaction' => 'controller.uicontrol_item.index'))
							),
							array('type' => 'filter', 
								'name' => 'status',
                                'text' => lang('Status').':',
                                'list' => array(
                                    array(
                                        'id' => 'none',
                                        'name' => lang('Not selected')
                                    ), 
                                    array(
                                        'id' => 'NEW',
                                        'name' => lang('NEW')
                                    ), 
                                    array(
                                        'id' => 'PENDING',
                                        'name' =>  lang('PENDING')
                                    ), 
                                    array(
                                        'id' => 'REJECTED',
                                        'name' => lang('REJECTED')
                                    ), 
                                    array(
                                        'id' => 'ACCEPTED',
                                        'name' => lang('ACCEPTED')
                                    )
                                )
                            ),
							array('type' => 'filter',
								'name' => 'control_groups',
                                'text' => lang('Control_group').':',
                                'list' => $this->so_control_group->get_control_group_select_array(),
							),
							array('type' => 'filter',
								'name' => 'control_areas',
                                'text' => lang('Control_area').':',
                                'list' => $this->so_control_area->get_control_area_select_array(),
							),
							array('type' => 'text', 
                                'text' => lang('searchfield'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix.'.toggle_show_inactive'))
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicontrol_item.display_control_items', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),						
						array(
							'key' => 'title',
							'label' => lang('Title'),
							'sortable'	=> false
						),
						array(
							'key' => 'required',
							'label' => lang('Required'),
							'sortable'	=> true
						),
						array(
							'key' => 'what_to_do',
							'label' => lang('What to do'),
							'sortable'	=> false
						),
						array(
							'key' => 'how_to_do',
							'label' => lang('How to do'),
							'sortable'	=> true
						),
						array(
							'key' => 'control_group_id',
							'label' => lang('control_group_id'),
							'sortable'	=> true
						),
						array(
							'key' => 'control_area_id',
							'label' => lang('control_area_id'),
							'sortable'	=> true
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);
//_debug_array($data);

			self::render_template_xsl('datatable', $data);
		}

		public function display_control_items_json()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'dir'	=> phpgw::get_var('dir'),
				'filters' => $filters
			);

			$user_rows_per_page = 10;
			
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		= phpgw::get_var('sort');
			if($sort_field == null)
			{
				$sort_field = 'control_item_id';
			}
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			//Create an empty result set
			$records = array();
			
			//Retrieve a contract identifier and load corresponding contract
			$control_item_id = phpgw::get_var('control_item_id');
			if(isset($control_item_id))
			{
				$control_item = rental_socontract::get_instance()->get_single($control_item_id);
			}
			
			$result_objects = controller_socontrol_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
								
			$results = array();
			
			foreach($result_objects as $control_item_obj)
			{
				$results['results'][] = $control_item_obj->serialize();	
			}

			array_walk($results["results"], array($this, "_add_links"), "controller.uicontrol_item.index");

			return $this->yui_results($results);
		}
		
		public function query()
		{
	
		}
		
	}
