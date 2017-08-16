<?
class Anotacione extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'anotaciones';
	var $name = 'Anotacione';
	
	var $validate = array(
		'funcionario_id' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'No existe Funcionario asociado'
			),
		'anotacion' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Ingrese anotación'
			),
		'solicita_id' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Seleccione una persona solicitante'
			)
	);
}
