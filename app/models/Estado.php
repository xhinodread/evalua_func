<?
class Estado extends AppModel{
	public $useDbConfig ='dbEvaluaFunc';
	public $useTable = 'estados';
	public $displayField = 'descripcion';	
	
	/*
	public $belongsTo = array(
		'Factor' => array(
			'className' => 'Factor',
			/*'foreignKey' => 'estado_id',* /
			'foreignKey' => false,
			'condition' => array('Estado.valor = Factor.estado_id')
		)
	);
	*/
}
?>