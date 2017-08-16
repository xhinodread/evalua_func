<?
class Item extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'items';
	public $displayField = 'etiqueta';
	
	public $belongsTo = array(
		'Subfactor' => array(
			'className' => 'Subfactor',
			'foreignKey' => 'subfactore_id'
		),
		'Estado' => array(
			'className' => 'Estado',
			'foreignKey' => 'estado_id'
		)
	);
	
	public $hasMany = array(
		'Pregunta' => array(
			'className' => 'Pregunta',
			'foreingKey' => 'item_id',
			'order' => array('pregunta_valor_id'=> 'ASC')
		)
	);
	
//	var $paginate = array('order' => array('pregunta_valor_id'=> 'DESC'));


	public function nroPreguntas(){
		$sql="SELECT  COUNT(*) AS [count] , [factore_id] "
			."FROM [items] AS [Item] "
			."LEFT JOIN [subfactores] AS [Subfactor] ON ([Item].[subfactore_id] = [Subfactor].[id]) "
			."LEFT JOIN [estados] AS [Estado] ON ([Item].[estado_id] = [Estado].[id]) "
			."GROUP BY [factore_id]";
  		return $this->query($sql);
	}

}
?>