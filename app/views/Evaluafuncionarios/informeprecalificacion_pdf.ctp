<?
if(1){
	App::import('Vendor','Funcionespropias');
	App::import('Vendor','tcpdf');
	$Funcionespropias = new Funcionespropias();
	$tcpdf = new TCPDF();

	$textfont = 'arial-narrow'; // 'freesans'; // 
	$tcpdf->SetCreator(PDF_CREATOR);
	$tcpdf->SetAuthor("GoreCoquimbo");
	$tcpdf->SetTitle("Evaluafuncionarios");
	$tcpdf->SetSubject("Evaluafuncionarios");
	$tcpdf->SetKeywords("GoreCoquimbo");
	$tcpdf->setPrintHeader(false);
	$tcpdf->setPrintFooter(false);
	$topM =5;
	//$BottomM =5;
	$tcpdf->SetMargins(PDF_MARGIN_LEFT, $topM, PDF_MARGIN_RIGHT);
	////$tcpdf->SetAutoPageBreak(TRUE, $BottomM);
	$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$tcpdf->SetFont($textfont, "", 10);
	/**************** CABECERA *****************/
	// $tcpdf->SetFontSize(10);
	$tcpdf->AddPage('P', 'letter');
	$tcpdf->Image('img/dlogoh_sbordeIzq.jpg', 15, 5, 67, 20);
	$tcpdf->Ln(20);
	$tcpdf->Cell(0, 0, 'REPÚBLICA DE CHILE', 0, 'L');
	$tcpdf->Ln();
	$tcpdf->Cell(0, 0, 'GOBIERNO REGIONAL', 0, 'L');
	$tcpdf->Ln();
	$tcpdf->Cell(0, 0, 'REGIÓN DE COQUIMBO', 0, 'L');
	$tcpdf->Ln();
	$tcpdf->Cell(0, 0, 'INFORME DE PRECALIFICACIÓN', 0, 0, 'C');
	$tcpdf->Ln();
	$tcpdf->Cell(0, 0, 'Periodo de Calificación: Sep 2015 - Ago 2016 ', 0, 'L');
	$tcpdf->Ln();
	$tcpdf->Cell(0, 0, 'Subperiodo: '.$perNombre, 0, 'L');
	$tcpdf->Ln();
	$tcpdf->SetFontSize(11);
	$tcpdf->Cell(0, 0, $nombreFuncionario, 0 ,'L');
	/**************** TABLA CON LAS NOTAS *****************/
	$altoGral=10;
	$datosTmp = array();
	$tcpdf->Ln(10);
	$tcpdf->SetX(20);
	$tcpdf->Cell(100, 0, '', 'B' ,'L');
	$tcpdf->Cell(30, 0, 'Nota SubFactor', 'B' , 0, 'C');
	$tcpdf->Cell(40, 0, 'Nota Factor', 'B' , 0, 'C');
	$tcpdf->Ln();
	foreach($listaFactor as $listado){
		$tcpdf->SetFillColor(240);
		$tcpdf->SetX(20);
		$tcpdf->SetFont('', 'B');
		$tcpdf->Cell(100, $altoGral, $listado['Factor']['etiqueta'], 'B' ,'L', '', 1);
		$tcpdf->SetFont('', '');
		$tcpdf->Cell(30, $altoGral, '', 'B' , 0, 'C', 1);
		// LAS LINEAS QUE SIGUEN A CONTINUACION, 84, 85, SE PUEDEN ELIMINAR SI NO HAY PROBLEMA EN LA PERFORMANCE DEL CODIGO - 30/08/2016
		// echo sacaPromedio($lstPromedioNotas[$listado['Factor']['id']]);
		// round($lstPromedioNotas[$listado['Factor']['id']]['sumaNota'] / $lstPromedioNotas[$listado['Factor']['id']]['nroNotas'])
		$tcpdf->Cell(40, $altoGral, $Funcionespropias->sacaPromedioFactor($lstPromedioNotas[$listado['Factor']['id']]), 'B' , 0, 'C', 1); // PROMEDIO POR FACTOR
		$tcpdf->Ln();
		$subFactorJustifica = array();
		foreach($listado['Subfactor'] as $listadoSub){
			$tcpdf->SetFillColor(252); 
			$tcpdf->SetX(20);
			$tcpdf->SetFont('', 'BI');
			$tcpdf->Cell(100, $altoGral, $listadoSub['etiqueta'], 'B' ,'L', '', 1); 
			$tcpdf->SetFont('', '');
			$tcpdf->Cell(30, $altoGral, $lstNotas[$listadoSub['id']], 'B' , 0, 'C' ,1); // PROMEDIO PARCIAL POR SUBFACTOR
			$tcpdf->Cell(30, $altoGral, '', 0 , 0, 'C', 1);
			$tcpdf->Ln();
			$datosJustificacion = $Funcionespropias->obtenerJustificacion($listaJustFuncTmp, $listadoSub['id']);
			
			//echo '<pre>'.print_r($datosJustificacion,1 ).'</pre>';
			
			$subFactorJustificaTmp = $Funcionespropias->listarJustificacion($datosJustificacion[$listadoSub['id']]);
			//echo '<pre>'.print_r($subFactorJustificaTmp,1 ).'</pre>';
			
			$subFactorJustifica = array_merge($subFactorJustifica, $subFactorJustificaTmp);
			// echo '<pre>'.print_r($subFactorJustifica,1).'</pre>';
			$tcpdf->SetX(20);
			$tcpdf->Cell(160, $altoGral, 'Justificaciones:', 0 , 0, 1);
			$tcpdf->Ln(8);
			$tcpdf->SetFontSize(9);
			foreach($subFactorJustifica as $pnt => $lista){
				$tcpdf->Ln(4);
				$tcpdf->SetX(20);
				$posGuion = strpos($lista, ' ');
				$nroSubperiodo = substr($lista, 0, $posGuion);
				$tcpdf->MultiCell(170, 20, '- '.$arraySubperiodos[$nroSubperiodo].', '.substr($lista, $posGuion), 0 , 'L', 0, 0);
				$tcpdf->Ln(3);
			}
			$tcpdf->Ln(5);
			$tcpdf->SetFontSize(10);
			$subFactorJustifica = array();
		}
		/*
		$tcpdf->SetX(20);
		$tcpdf->Cell(160, $altoGral, 'Justificaciones:', 0 , 0, 1);
		$tcpdf->Ln(8);
		$tcpdf->SetFontSize(9);
		foreach($subFactorJustifica as $lista){
			$tcpdf->Ln(4);
			$tcpdf->SetX(20);
			$tcpdf->MultiCell(170, 20, $lista, 0 , 'L', 0, 0);
			$tcpdf->Ln(3);
		}
		$tcpdf->Ln(5);
		$tcpdf->SetFontSize(10);
		*/
	}
	$tcpdf->Output("InformePrecalificacion_$nombreFuncionario.pdf", "I");
}else{
	echo 'debug:<pre>'.print_r(array(), true).'</pre>';
}
?>