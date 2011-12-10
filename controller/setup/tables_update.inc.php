<?php

	 /* Update Controller from v 0.1 to 0.1.1 */

	$test[] = '0.1';
	function controller_upgrade0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','procedure_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','revision_no',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','revision_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.1';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.1';
	function controller_upgrade0_1_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_group','order_nr',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.2';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	$test[] = '0.1.2';
	function controller_upgrade0_1_2()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_group_list', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'control_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'control_group_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'order_nr' => array('type' => 'varchar', 'precision' => '3', 'nullable' => false)
				),
				'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);	
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.3';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	$test[] = '0.1.3';
	function controller_upgrade0_1_3()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_group', array(), 'order_nr');
			
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.4';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	/* Update Controller from v 0.1.4 to 0.1.5 */
	
	$test[] = '0.1.4';
	function controller_upgrade0_1_4()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','control_area_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.5';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	/* Update Controller from v 0.1.5 to 0.1.6 */
	
	$test[] = '0.1.5';
	function controller_upgrade0_1_5()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_list', array(), 'check_list_id');
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','planned_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
		));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','completed_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.6';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	/* Update Controller from v 0.1.6 to 0.1.7 */
	
	$test[] = '0.1.6';
	function controller_upgrade0_1_6()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','location_code',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','equipment_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.7';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	/* Update Controller from v 0.1.7 to 0.1.8 */
	
	$test[] = '0.1.7';
	function controller_upgrade0_1_7()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_item', array(), 'status');
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item','status',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true,
			'default' => 0
		));
			
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.8';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	/* Update Controller from v 0.1.8 to 0.1.9 */
	
	$test[] = '0.1.8';
	function controller_upgrade0_1_8()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_location_list', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'control_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'location_code' => array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
				'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);	
			
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.9';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	/* Update Controller from v 0.1.9 to 0.1.10 
	 * Add table for connecting equipment (BIM) and control 
	*/
	
	$test[] = '0.1.9';
	function controller_upgrade0_1_9()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_equipment_list', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'control_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'equipment_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
				'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);	
			
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.10';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	/* Update Controller from v 0.1.10 to 0.1.11
	 * Alter from naming from equipment to more generic component 
	*/
	
	$test[] = '0.1.10';
	function controller_upgrade0_1_10()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_component_list', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'control_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'component_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
				'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);	

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('controller_check_list','equipment_id','component_id');

		$GLOBALS['phpgw_setup']->oProc->DropTable('controller_control_equipment_list');			

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.11';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
