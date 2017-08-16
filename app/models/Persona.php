<?php
//App::uses('AppModel', 'Model');

class Persona extends AppModel {
	public $useDbConfig = 'msSqlPersonas';
	public $name = 'Persona';
	public $useTable = 'persona';
	public $primaryKey = 'id_per';
	public $displayField = 'nombres';
	
	
	public $belongsTo = array(
		'PersonaEstado' => array(
				'className' => 'PersonaEstado',
				'foreignKey' => 'id_per'
		)
	);
	
	public $hasMany = array (
		'Evaluafuncionario' => array(
			'className' => 'Evaluafuncionario',
			'foreignKey' => 'funcionario_id'
		),
		'Justificacionsubperiodo' => array(
			'className' => 'Justificacionsubperiodo',
			'foreignKey' => 'funcionario_id'
		),
		'Notasubfactor' => array(
			'className' => 'Notasubfactor',
			'foreignKey' => 'funcionario_id'
		),
		'Calificafuncionario' => array(
			'className' => 'Calificafuncionario',
			'foreignKey' => 'funcionario_id'
		),
		'Historia' => array(
			'className' => 'Historia',
			'foreignKey' => 'id_per',
			'order'      => array('Historia.FEC_DESDE DESC', 'COD_THIS', 'ID_DOC', 'COD_GRADO')
		)
	);	
	
	public $hasOne = array(
		'Precalificadore' => array(
				'className' => 'Precalificadore',
				'foreignKey' => 'funcionario_id'
			),
		'Calificadore' => array(
				'className' => 'Calificadore',
				'foreignKey' => 'funcionario_id'
			),
		'Personaimagen' => array(
				'className' => 'Personaimagen',
				'foreignKey' => 'id_per'
			)
	);
	
 	public function PrecalFuncArrayUnico($arrayDatos = null){
		$laLista = array();
		foreach($arrayDatos as $pnt => $lista){
			$indiceArray = $lista['Subperiodo']['periodo_id'].$lista['Subperiodo']['etiqueta'].$lista['Evaluafuncionario']['precalificadore_id'];
			$laLista[$indiceArray] = array( $lista['Subperiodo']['periodo_id'], $lista['Subperiodo']['etiqueta'], $lista['Evaluafuncionario']['precalificadore_id'] );
		}
		return $laLista;
	}
	
	
	function armarfactores($arrayFactores = null, $arraySubFactores = null){
		$resultado = array();
		$idFactor = 0;
		foreach($arrayFactores as $lista){
			$idFactor = $lista['Factor']['id'];
			$nombreFactor = $lista['Factor']['etiqueta'];
			foreach($lista['Subfactor'] as $listaDos){
				if( in_array( $listaDos['id'], $arraySubFactores) ){
					// $resultado[$idFactor][$listaDos['id']] = $listaDos;
					$resultado[$nombreFactor][$listaDos['id']] = $listaDos;
				}
			}
		}
		return $resultado;		
	}
	
	
	
}