<?
class Anotademerito extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'anotademeritos';
	var $name = 'Anotademerito';
	
	var $validate = array(
		'funcionario_id' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'No existe Funcionario asociado.'
			),
		'anotacion' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Ingrese anotación.'
			),
		'solicita_id' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Seleccione una persona solicitante.'
			)
		,'archivo_nombre' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Falta agregar el archivo de respaldo.'
			)
	);
}
