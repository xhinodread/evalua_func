<?
class Cargo extends AppModel{
	public $useDbConfig = 'msSqlPersonas';
	public $primaryKey = 'cod_cargo'; //'COD_CARGO';
	public $useTable = 'CARGO';
	public $name = 'Cargo';
	
	
	public $belongsTo = array(
		'Historia' => array(
				'className' => 'Historia'
				// ,'foreignKey' => 'cod_cargo' //'COD_CARGO'
				,'foreignKey' => false
				,'conditions' => array('cod_cargo = Historia.cod_cargo')
			)		
	);
	
	
}
?>