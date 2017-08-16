<?
class Subfactor extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'subfactores';
	public $displayField = 'etiqueta';
	
	public $belongsTo = array(
		'Factor' => array(
			'className' => 'Factor',
			'foreignKey' => 'factore_id'
		),
		'Estado' => array(
			'className' => 'Estado',
			'foreignKey' => 'estado_id'
		)
	);
	
	public $hasMany = array(
		'Item' => array(
			'className' => 'Item',
			'foreignKey' => 'subfactore_id'
		)
	);
	
}
?>