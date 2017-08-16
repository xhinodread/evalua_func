<?
class Precalificadore extends AppModel{
	var $useDbConfig = 'dbEvaluaFunc';
	var $useTable = 'precalificadores';
	
	
	public $belongsTo = array (
		'Persona' => array(
			'className' => 'Persona',
			'foreignKey' => 'funcionario_id'
		)
	);
	
}
?>