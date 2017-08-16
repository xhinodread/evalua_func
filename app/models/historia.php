<?php
//App::uses('AppModel', 'Model');

class Historia extends AppModel {
	public $useDbConfig = 'msSqlPersonas';
	public $primaryKey = 'id_per';
	public $useTable = 'historia';
	public $name = 'Historia';
	
	
	/*
	public $validate = array(
		'per_rut' => array(
			'notEmpty'=> array('rule'=>'notEmpty'),
			'numeric'=> array('rule'=>'numeric', 'message'=>'Ingrese solo números'),
			'unique'=> array('rule'=>'isUnique', 'message'=>'Este rut ya esxiste!!!, Verifique.')
		)
	);
	*/
	public $belongsTo = array(
		'Persona' => array(
				'className' => 'Persona',
				'foreignKey' => 'id_per'
			)		
	);
	
	/*
	public $hasMany = array(
		'Cargo' => array(
			'className' => 'Cargo'
			//,'foreignKey' => 'cod_cargo'
			,'foreignKey' => false
			,'conditions' => array('Cargo.COD_CARGO = Historia.cod_cargo')
			//,'dependent'    => false
			)
	);
	*/
	/*
	public $hasOne = array(
		'persona' => array(
			'className' => 'Persona',
			'foreignKey' => 'id_per',
			'conditions' => '',
			'dependent'    => false
			)
		);
	*/
/*	
	var $hasOne = array(
        'Perfil' => array(
            'className'    => 'Perfil',
            'conditions'   => array('Perfil.publicado' => '1'),
            'dependent'    => true
        )
    );
*/

	public function traeFuncionariosCalificacion($idPer, $codCargo){
		$sql = "Select * "
			." FROM [Personal].dbo.Historia as H"
			." WHERE cod_cargo in($codCargo)"
			." and ID_PER in ($idPer)"
			." and cod_this = 1"
			." and id_doc = ( Select max(id_doc) from [Personal].dbo.Historia where ID_PER in (H.ID_PER) and cod_this = 1 )"
			." order by ID_PER, id_doc desc";
		//echo $sql.'<br />';
		return $this->query($sql);
	}
	
	public function traeFuncionariosHistoria($idPer){
		$sql = "Select * "
			.", calidadJuridica = (SELECT texto FROM CALIDADJURIDICA WHERE calJurCod = H.COD_CAL) "
			.", grado = (SELECT GLOSA_GRADO FROM GRADO WHERE COD_GRADO = H.COD_GRADO) "
			.", lugardesem = (SELECT GLOSA_DEST FROM DESTINACION WHERE COD_DEST = H.COD_DEST ) "
			.", jefeDirecto =(SELECT NOMJF FROM DESTINACION WHERE COD_DEST = H.COD_DEST ) "
			.", rutJefeDirecto =(SELECT RUTJF FROM DESTINACION WHERE COD_DEST = H.COD_DEST ) "
			.",idJefeDirecto = (SELECT id_per FROM persona WHERE rut = (SELECT  Substring(RUTJF, 0, Charindex('-',RUTJF)) FROM DESTINACION WHERE COD_DEST = H.COD_DEST )) "
			.", grupo = (SELECT GLOSA_GRUPO FROM GRUPO WHERE COD_GRUPO = (SELECT GRUPO FROM DESTINACION WHERE COD_DEST = H.COD_DEST) )"
			." FROM [Personal].dbo.Historia as H"
			." WHERE ID_PER in ($idPer)"
			." and cod_this = 1"
			." and id_doc = ( Select max(id_doc) from [Personal].dbo.Historia where ID_PER in (H.ID_PER) and cod_this = 1 )"
			." order by ID_PER, id_doc desc";
		//echo 'traeFuncionariosHistoria: '.$sql.'<br />';
		return $this->query($sql);
		
		//SELECT id_per FROM persona WHERE rut = (SELECT  Substring(RUTJF, 0, Charindex('-',RUTJF)) FROM DESTINACION WHERE COD_DEST = 51)
		
	}

}
/*
per_rut
per_estado
cod_grupo
funcional
id_per
id_per_yomi
usuario
honorario
calidadJuridica
*/