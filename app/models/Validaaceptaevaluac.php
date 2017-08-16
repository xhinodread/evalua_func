<?
class Validaaceptaevaluac extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'validaaceptaevaluacs';
	
	var $validate = array(
	/*
		'aceptar' => array(
			'rule' => array('boolean'),
              'required' => true,
              'message' => 'Debe aceptar la Notificación'
		),
		'aceptarR' => array(
			'notEmpty' => array(
				'rule' => array('comparison', '!=', 0),
				  'required' => true,
				  'message' => 'Debe aceptar la Notificación'
			)
		)
		,
		'texto' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Ingrese texto',
				'allowEmpty' => false,
				'required' => true,
			)
		)
		*/
	);
}


/* 
		'notEmpty' => array(
			'rule' => array('notEmpty'),
			'message' => 'Debe aceptar la Notificación',
			'allowEmpty' => false,
			'required' => true,
		)
*/
?>