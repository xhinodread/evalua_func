<?
class Calificacionfuncionario extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'calificacionfuncionarios';
	
	var $belongsTo = array(
		'Periodo' => array(
			'className'=>'Periodo',
			'foreignKey'=>'periodo_id'
		),
		'Subfactor' => array(
			'className' => 'Subfactor',
			'foreignKey' => 'subfactore_id'
		)/*,
		'Persona' => array(
			'className' => 'Persona',
			'foreignKey' => 'id_per'
		)*/
	);
}
?>