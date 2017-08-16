<?
class Calificadore extends AppModel{
	var $useDbConfig = 'dbEvaluaFunc';
	var $useTable = 'calificadores';
	
	
	public $belongsTo = array (
		'Persona' => array(
			'className' => 'Persona',
			'foreignKey' => 'funcionario_id'
		)
	);
	
}
?>