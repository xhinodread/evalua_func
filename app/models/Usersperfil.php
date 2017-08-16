<?
class Usersperfil extends appModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'usersperfiles';
	//public $primaryKey = 'id';
	public $displayField = 'etiqueta';
	
	/*
	var $hasMany = array(
		'User' => array(
			'className'=>'User',
			/*'foreignKey'=>'id'* /
			'foreingKey' => false,
			'conditions' => array('Usersperfil.id = User.usersperfil_id ')
		)
	);
	*/
	/*
	public $belongsTo = array(
		'Usersperfils' => array(
			'className' => 'Usersperfils',
			'foreingKey' => 'userperfil_id'
		)
	);
	*/
	
}
?>