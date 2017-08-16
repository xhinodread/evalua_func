<?php
class JuntaEvaluadore extends AppModel {
	public $useDbConfig = 'dbEvaluaFunc';
	var $useTable = 'junta_evaluadores';
	
	public $validate  = array(
		'funcionario_id' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Debe ingresar una clave',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
		)
	);
	
	
}
?>