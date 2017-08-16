<?
class Subperiodo extends AppModel{
	public $useDbConfig = 'dbEvaluaFunc';
	public $useTable = 'subperiodos';
	public $displayField = 'etiqueta';
	
	public $belongsTo = array(
		'Periodo' => array(
			'className' => 'Periodo',
			'foreingKey' => 'periodo_id'
		)
	);
	
	public function traeAsignacionSubPerAnterior($idSubPer){
		$losSubPeriodos = array();
		$subPeriodoAnterior = $this->query('SELECT TOP 1 * '
											.'FROM subperiodos '
											.'WHERE meshasta <= (	SELECT mesdesde '
																.'FROM subperiodos '
																.'WHERE id = '.$idSubPer.' ) '
											.'ORDER BY mesdesde desc;');
		$idSubPeriodoAnterior = $subPeriodoAnterior[0][0]['id'];
		$sql = 'SELECT '
				/* ."(nombres + ' '+ap_pat + ' ' +ap_mat) as nombre, " */
				.'EF.funcionario_id, EF.factore_id, EF.subperiodo_id, EF.precalificadore_id '
				.'FROM  evaluafuncionarios as EF '
				.' INNER JOIN personal..persona as P '
				.'	ON (EF.funcionario_id = P.id_per) '
				.'WHERE EF.subperiodo_id = '.$idSubPeriodoAnterior. ' '
				.'ORDER BY nombres, ap_pat, ap_mat';
		// $losSubPeriodos['sql'] = $sql;
		$evaluaFuncionarios = $this->query($sql);
		// $losSubPeriodos['evaluaFuncionarios'] = $evaluaFuncionarios;
		foreach($evaluaFuncionarios as $lista)
			$losSubPeriodos['evaluaFuncionarios'][] = $lista[0];
		
		// $losSubPeriodos['idSubPeriodoAnterior'] = $idSubPeriodoAnterior;
		return $losSubPeriodos;
	}
}
?>