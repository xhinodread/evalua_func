<?php
//App::uses('AppModel', 'Model');
class Plantilla extends AppModel {
	public $useDbConfig ='dbAcuerdos';
	public $primaryKey = 'plan_id';
	public $useTable = 'plantillas';
	public $displayField = 'plan_nombre';
	/*
	public $belongsTo = array(
		'TipoPlantilla' => array(
			'className' => 'TipoPlantilla',
			'foreignKey' => 'tipl_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	*/
	public $validate = array(
		'tipl_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Debe seleccionar tipo de plantilla',
				'alowempty' => false,
				'required' => true
			)
		),
		'plan_nombre' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Debe ingresar nombre de la plantilla',
				'alowEmpty' => false,
				'required' => true
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'El nombre de la plantilla ya se encuentra en uso.',
				'alowempty' => false,
				'required' => true
			)
		),
		'plan_contenido' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Debe ingresar contenido de la plantilla',
				'alowempty' => false,
				'required' => true
			)
		)
	);
}
