<?
class PreguntaValor extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'pregunta_valores';
	public $displayField = 'etiqueta';
	
	/*
	public $belongsTo = array(
		'Pregunta' => array(
			'className' => 'Pregunta',
			'foreignKey' => 'pregunta_id'
		)/*,
		'Estado' => array(
			'className' => 'Estado',
			'foreignKey' => 'estado_id'
		)* /
	);
	*/	
}
?>