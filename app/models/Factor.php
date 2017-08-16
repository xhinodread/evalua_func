<?
class Factor extends AppModel{
	public $useDbConfig ='dbEvaluaFunc';
	public $useTable = 'factores';
	public $displayField = 'etiqueta';
	
	
	public $belongsTo = array(
		'Estado' => array(
			'className' => 'Estado',
			'foreignKey' => 'estado_id'
		)
	);
	
	public $hasMany = array(
		'Subfactor' => array(
			'className' => 'Subfactor',
			'foreignKey' => 'factore_id'
		)/*, 
		'Evaluafuncionario' => array(
			'className' => 'Evaluafuncionario',
			'foreingKey' => 'factore_id'
		)*/
	);
	
}

?>