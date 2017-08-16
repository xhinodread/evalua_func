<?
class Pregunta extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'preguntas';
	
	public $belongsTo = array(
		'Item' => array(
			'className' => 'Item',
			'foreignKey' => 'item_id'
		),
		'PreguntaValor' => array(
			'className' => 'PreguntaValor',
			'foreignKey' => 'pregunta_valor_id'
		),
		'Estado' => array(
			'className' => 'Estado',
			'foreignKey' => 'estado_id'
		)
	);	
}
?>