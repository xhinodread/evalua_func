<?
class Justificacionsubperiodo extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'justificafuncionarios';

	var $belongsTo = array(
		'Subperiodo' => array(
			'className' => 'Subperiodo',
			'foreignKey' => 'subperiodo_id'
		),
		'Subfactor' => array(
			'className' => 'Subfactor',
			'foreignKey' => 'subfactore_id'
		),
		'Persona' => array(
			'className' => 'Persona',
			'foreignKey' => 'id_per'
		)
	);	
	
	
}
?>