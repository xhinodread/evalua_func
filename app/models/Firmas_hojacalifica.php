<?php
class FirmasHojacalifica extends AppModel {
	public $useDbConfig = 'dbEvaluaFunc';
 	var $useTable = 'firmas_hojacalifica';
	
	public $validate  = array(
		'funcionario_id' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'No hay funcionario asociado',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				),
				'unique' => array('rule' => 'isUnique' ,'message' => 'ya existe funcionario')
		)
		,'slc_integrante1' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Debe seleccionar un integrante',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
		)
		,'slc_integrante2' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Debe seleccionar un integrante',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
		)
		,'slc_integrante3' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Debe seleccionar un integrante',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
		)
		,'slc_integrante4' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Debe seleccionar un integrante',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
		)
		,'slc_presi' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Debe seleccionar un presidente',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
		)
		,'slc_representante' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Debe seleccionar un representante',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
		)
		,'slc_secretario' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Debe seleccionar un secretario',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
		)
	);
	
	
}
?>