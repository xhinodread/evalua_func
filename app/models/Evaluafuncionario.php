<?
class Evaluafuncionario extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'evaluafuncionarios';
	// funcionario_id, factore_id , subperiodo_id

	var $validate = array(
		'funcionario_id' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Debe seleccionar a lo menos un funcionario',
				'allowEmpty' => false,
				'required' => true,
			)
		),
		'subperiodo_id' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Debe seleccionar un periodo',
				'allowEmpty' => false,
				'required' => true,
			)
		)
	);

	public $belongsTo = array(
		'Factor' => array(
			'className' => 'Factor',
			'foreignKey' => 'factore_id'
		),
		'Subperiodo' => array(
			'className' => 'Subperiodo',
			'foreignKey' => 'subperiodo_id'
		)	
	);
	
	public function buscarPrecalificador($idPrecalificador, $vector){
		$varRtn='';
		foreach($vector as $listaVector){
			if($listaVector['Persona']['ID_PER'] == $idPrecalificador){
				$varRtn=utf8_encode($listaVector['Persona']['NOMBRES'].' '.$listaVector['Persona']['AP_PAT'].' '.$listaVector['Persona']['AP_MAT']);
				break;
			}
		}
		return $varRtn;
	}
	
	public function cntFactorAsignado($idFunc, $idSubper){
		$sql="SELECT  COUNT(*) AS [count] "
			."FROM [items] AS [Item] "
			."LEFT JOIN [subfactores] AS [Subfactor] ON ([Item].[subfactore_id] = [Subfactor].[id]) "
			."LEFT JOIN [estados] AS [Estado] ON ([Item].[estado_id] = [Estado].[id]) "
			."WHERE [factore_id] in (select factore_id from evaluafuncionarios where funcionario_id = $idFunc  and subperiodo_id = $idSubper)";
		return $this->query($sql);
	}
	
	public function nroPreguntasItem(){
		$sql="SELECT COUNT(S.factore_id) as nroPreg, S.factore_id as factore_id "
			."FROM items I "
			."	INNER JOIN subfactores S "
			."		ON I.subfactore_id = S.id "
			."GROUP BY  S.factore_id";
		return $this->query($sql);
	}
	
	public function nroPreguntasFuncSubfactJust($idFunc, $idSubPer){
		$sql="SELECT count(*) as nroRespJust "
			."FROM evaluafuncionarios "
			."INNER JOIN subfactores "
			."	ON evaluafuncionarios.factore_id = subfactores.factore_id "
			."WHERE subperiodo_id = $idSubPer "
			."AND funcionario_id = $idFunc ";
		return $this->query($sql);
	}
	
	public function nroRespuestas($idFunc, $idSubPer){
		$sql="SELECT COUNT(*) as nroResp, S.factore_id "
			."FROM calificafuncionarios CF "
			."	INNER JOIN items I "
			."		ON CF.item_id = I.id "
			."	INNER JOIN subfactores S "
			."		ON I.subfactore_id = S.id "
			." WHERE CF.funcionario_id = $idFunc "
			." AND CF.subperiodo_id = $idSubPer "
			." GROUP BY S.factore_id";
		return $this->query($sql);
	}
	
	public function nroRespuestasJustificacion($idFunc, $idSubPer){
		$sql="SELECT COUNT(factore_id) AS NroJustif, factore_id "
			."FROM justificafuncionarios "
			."	INNER JOIN subfactores "
			."		ON justificafuncionarios.subfactore_id = subfactores.id "
			." WHERE subperiodo_id = $idSubPer "
			." AND funcionario_id = $idFunc "
			." GROUP BY factore_id";
		return $this->query($sql);
	}
	
	public function nroRespuestasSubfactores($idFunc, $idPer){
		$sql="SELECT count(subfactores.factore_id) as nroResp, subfactores.factore_id "
			."FROM notasubfactores "
			."INNER JOIN subfactores "
			."	ON notasubfactores.subfactore_id = subfactores.id "
			."WHERE periodo_id = $idPer "
			."AND funcionario_id = $idFunc "
			."GROUP BY subfactores.factore_id";
		return $this->query($sql);	
	}

	public function tieneAsignadoDireccion($idFunc, $idPeriodo){
		$varResultado = 0;
		$sql="SELECT COUNT(ID) as 'cuenta' "
			."FROM notasubfactores "
			."WHERE funcionario_id = $idFunc "
			."AND periodo_id = $idPeriodo "
			."AND subfactore_id IN (SELECT ID "
			."					FROM subfactores "
			."					WHERE factore_id IN (SELECT ID "
			."										FROM factores "
			."										WHERE etiqueta like '%direcc%' ) "
			."					) ";
		$varResultado = $this->query($sql);
		return ($varResultado[0][0]['cuenta'] > 0 ? true : false);
	}
	public function promedioPrecalificacion($idFunc, $idPeriodo){
		$varResultado = 0;
		$sql="SELECT sum(nota) sumaNota , SubF.factore_id, (SELECT count(factore_id) FROM subfactores WHERE factore_id = SubF.factore_id ) as nroNotas "
			."FROM notasubfactores "
			."INNER JOIN subfactores as SubF ON (notasubfactores.subfactore_id = SubF.id) "
			."WHERE funcionario_id = $idFunc "
			."AND periodo_id = $idPeriodo "
			."GROUP BY SubF.factore_id ";
		$varResultado = $this->query($sql);
		return $varResultado;	
	}	
	public function promedioCalificacion($idFunc, $idPeriodo){
		$varResultado = 0;
		$sql="SELECT sum(nota) sumaNota , SubF.factore_id, (SELECT count(factore_id) FROM subfactores WHERE factore_id = SubF.factore_id ) as nroNotas "
			."FROM Calificacionfuncionarios AS Cali "
			."INNER JOIN subfactores as SubF ON (Cali.subfactore_id = SubF.id) "
			."WHERE Cali.funcionario_id = $idFunc "
			."AND Cali.periodo_id = $idPeriodo "
			."GROUP BY SubF.factore_id ";
		$varResultado = $this->query($sql);
		return $varResultado;	
	}
	
	public function traePrecalificacionDelPeriodo($idFunc, $idPeriodo, $idSubPeriodo){
		$varResultado = 0;
		/*
		$sql="SELECT periodo_id= ".$idPeriodo.", 'subfactore_id' = S.id, funcionario_id, 'nota'=0, factore_id = S.factore_id  "
			."FROM dbo.evaluafuncionarios AS EF "
			."INNER JOIN subfactores AS S "
			."	ON	(EF.factore_id = S.factore_id) "
			."WHERE funcionario_id = ".$idFunc." "
			."AND subperiodo_id = ".$idSubPeriodo;
		*/	
		/*$sql="SELECT periodo_id = ".$idPeriodo.", subfactore_id, funcionario_id, factore_id, (SUM(preguntasValores)/ COUNT(preguntasValores))AS 'nota' "*/
		
		$sql="SELECT periodo_id = ".$idPeriodo.", subfactore_id, funcionario_id, factore_id, ('')AS 'nota' "
			 ."	FROM ( "
			 ."		SELECT I.subfactore_id, A.funcionario_id "
			 ."		,(SELECT factore_id FROM subfactores WHERE id = I.subfactore_id) AS 'factore_id'"
			 ."		,(SELECT valor from pregunta_valores WHERE id = (SELECT pregunta_valor_id FROM preguntas WHERE id = A.pregunta_id) )as 'preguntasValores' "
			 ."		FROM dbo.calificafuncionarios as A "
			 ."		INNER JOIN items AS I "
			 ."			ON (A.item_id = I.id) "
			 ."		WHERE subperiodo_id = ".$idSubPeriodo." "
			 ."		AND A.funcionario_id = ".$idFunc." "
			 ."	) AS T "
			 ."	GROUP BY subfactore_id, funcionario_id, factore_id";
		$varResultado = $this->query($sql);
		return $varResultado;	
	}

}
