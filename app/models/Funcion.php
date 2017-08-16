<?php
//App::uses('AppModel', 'Model');
class Funcion extends AppModel {
	public $useDbConfig ='dbAcuerdos';
	public $primaryKey = 'func_id';
	public $useTable = 'funciones';
	public $displayField = 'func_nombre_corto';
	
	public $validate = array(
		'func_nombre' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Debe ingresar nombre de la función',
				'alowempty' => false,
				'required' => true
			)
		),
		'func_nombre_corto' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Debe ingresar nombre corto de la función',
				'alowempty' => false,
				'required' => true
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'El nombre de la función ya se encuentra en uso.',
				'alowempty' => false,
				'required' => true
			)
		),
		'func_parametros' => array(
			'parametros' => array(
				'rule' => '/((\$[a-z]),?)+/',
				'message' => 'Los parametros deben contener solo letras y deben comenzar por $',
				'allowEmpty' => true,
				'required' => false,
			)
		),
		'func_descripcion' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Debe ingresar descripción de la función',
				'alowempty' => false,
				'required' => true
			)
		),
		'func_codigo' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Debe ingresar el código de la función',
				'alowempty' => false,
				'required' => true
			)
		)
	);
}
