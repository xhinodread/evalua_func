<?
if(1){
	App::import('Vendor','Funcionespropias');
	App::import('Vendor','tcpdf');
	$Funcionespropias = new Funcionespropias();
	$tcpdf = new TCPDF();

	$textfont = 'freesans'; // 'arial-narrow'; // 
	$tcpdf->SetCreator(PDF_CREATOR);
	$tcpdf->SetAuthor("GoreCoquimbo");
	$tcpdf->SetTitle("Evaluafuncionarios - Hoja de Calificación $nombreFuncionario");
	$tcpdf->SetSubject("Evaluafuncionarios");
	$tcpdf->SetKeywords("GoreCoquimbo");
	$tcpdf->setPrintHeader(false);
	$tcpdf->setPrintFooter(false);
	$topM = 5;
	//$BottomM =5;
	$tcpdf->SetMargins(PDF_MARGIN_LEFT, $topM, PDF_MARGIN_RIGHT);
	////$tcpdf->SetAutoPageBreak(TRUE, $BottomM);
	$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$tcpdf->SetFont($textfont, "", 10);
	/**************** CABECERA *****************/
	// $tcpdf->SetFontSize(10);
	$tcpdf->AddPage('P', 'Legal');
	//$tcpdf->AddPage('P', 'A3');
	$tcpdf->Image('img/dlogoh_sbordeIzq.jpg', 15, 5, 67, 20);
	$tcpdf->Ln(20);
	/*
	$tcpdf->Cell(0, 0, 'REPÚBLICA DE CHILE', 0, 2);
	//$tcpdf->Ln();
	$tcpdf->Cell(0, 0, 'GOBIERNO REGIONAL', 0, 2);
	//$tcpdf->Ln();
	$tcpdf->Cell(0, 0, 'REGIÓN DE COQUIMBO', 0, 2);
	*/
	//$tcpdf->Ln();
	$tcpdf->Cell(0, 0, 'HOJA DE CALIFICACIÓN.', 0, 1, 'C');
	// $tcpdf->Ln();
	//$tcpdf->Cell(0, 0, 'Periodo de Calificación: Sep 2015 - Ago 2016 ', 0, 2);
	$tcpdf->Cell(0, 0, 'Periodo de Calificación: '.$perNombre, 0, 2);
	//$tcpdf->Ln();
	// $tcpdf->Cell(0, 0, 'Subperiodo: '.$perNombre, 0, 2);
	//$tcpdf->Ln();
	$tcpdf->SetFontSize(11);
	$tcpdf->Cell(0, 0, 'Funcionario: '.$nombreFuncionario, 0, 1);
	//$tcpdf->Cell(0, 0, print_r($listaFirmantes,1), 0);
	
	/**************** TABLA CON LAS NOTAS *****************/
	$altoGral=10;
	$datosTmp = array();
	$NotaFactor = 0;
	$Coeficiente = 0;
	$Puntaje = 0;
	$PuntajeFinal = 0;
	$varDireccion = 0;
	$tcpdf->Ln(10);
	$tcpdf->SetX(20);
	$tcpdf->Cell(100, 0, '', 'B' ,'L');
	$tcpdf->Cell(30, 0, 'Nota SubFactor', 'B' , 0, 'C');
	$tcpdf->Cell(22, 0, 'Nota Factor', 'B' , 0, 'C');
	$tcpdf->Cell(12, 0, 'Coef.', 'B' , 0, 'C');
	$tcpdf->Cell(12, 0, 'Puntaje', 'B' , 0, 'C');
	$tcpdf->Ln();
	foreach($listaFactor as $listado){
		$NotaFactor = $Funcionespropias->sacaPromedioFactor($lstPromedioNotas[$listado['Factor']['id']]);
		$Coeficiente = $listaCoeficientes[$listado['Factor']['id']];
		$Puntaje = ($NotaFactor * ($Coeficiente/100) ) * 10;
		$PuntajeFinal = $PuntajeFinal + $Puntaje;
		$tcpdf->SetFontSize(11);
		$tcpdf->SetFillColor(240);
		$tcpdf->SetX(20);
		$tcpdf->SetFont('', 'B');
		if($listado['Factor']['etiqueta'] == 'DIRECCIÓN') $varDireccion = 1;
		$tcpdf->Cell(100, $altoGral, $listado['Factor']['etiqueta'], 'B' ,'L', '', 1);
		$tcpdf->SetFont('', '');
		$tcpdf->Cell(30, $altoGral, '', 'B' , 0, 'C', 1);
		$tcpdf->Cell(22, $altoGral, $NotaFactor, 'B' , 0, 'C', 1); // NOTA/PROMEDIO POR FACTOR
		$tcpdf->Cell(12, $altoGral, $Coeficiente.' %', 'B' , 0, 'C', 1);
		$tcpdf->Cell(12, $altoGral, $Puntaje, 'B' , 0, 'C', 1);
		$tcpdf->Ln();
		$subFactorJustifica = array();		
		foreach($listado['Subfactor'] as $listadoSub){
			$tcpdf->SetFontSize(10);
			$tcpdf->SetFillColor(252); 
			$tcpdf->SetX(20);
			//$tcpdf->SetFont('', 'BI');
			$tcpdf->SetFont('', '');
			$tcpdf->Cell(100, $altoGral-3, $listadoSub['etiqueta'], 'B' ,'L', '', 1); 
			//$tcpdf->SetFont('', '');
			$tcpdf->Cell(30, $altoGral-3, $lstNotas[$listadoSub['id']], 'B' , 0, 'C' ,1); // PROMEDIO PARCIAL POR SUBFACTOR
			// $tcpdf->Cell(30, $altoGral, '', 0 , 0, 'C', 1);
			$tcpdf->Ln();
			// $tcpdf->SetFontSize(10);
			// $subFactorJustifica = array();
		}
	}
	$tcpdf->SetFontSize(10);
	$tcpdf->Ln();
	$w = 25;
	$h = 0;
	$bordes = 0;
	$ln = 2;
	$align = '';
	$fondo = true;
	$link = '';
	$listaCalificacion = $Funcionespropias->distribucionPuntaje($PuntajeFinal);
	$tcpdf->SetX(150);
	//$tcpdf->Cell(50);
	$tcpdf->Cell($w, $h, 'Puntaje Final: ', $bordes , 0, $align, $fondo, $link);
	$tcpdf->Cell(8);
	$tcpdf->Cell(12, $h, $PuntajeFinal, 'B', 0, 'C');
	$tcpdf->Ln(); $tcpdf->SetX(20);
	$tcpdf->Cell($w, $h, 'Lista de Calificación: ', $bordes , 0, $align, $fondo, $link);
	$tcpdf->Cell(20);
	$tcpdf->Cell(30, $h, $listaCalificacion, 'B', 1, 'C');
	$tcpdf->Ln(); $tcpdf->SetX(110);
	$tcpdf->Cell($w, $h, 'SI', 0, 0, 'C'); $tcpdf->Cell($w, $h, 'NO', 0, 1, 'C');
	$tcpdf->SetX(20);
	$tcpdf->Cell($w, $h, 'Notificación: ', $bordes , 0, $align, $fondo, $link);
	$tcpdf->Cell(50);
	$tcpdf->Cell(15, $h, 'CONFORME', 0, 0, 'R');
	$tcpdf->Cell(5);
	$tcpdf->Cell(15, $h, '', 1, 0, 'C'); $tcpdf->Cell(10);
	$tcpdf->Cell(15, $h, '', 1, 2, 'C');
	$tcpdf->Ln();
	$tcpdf->SetX(90);
	$tcpdf->Cell(15, $h, 'APELARE', 0, 0, 'R');
	$tcpdf->Cell(10);
	$tcpdf->Cell(15, $h, '', 1, 0, 'C'); $tcpdf->Cell(10);
	$tcpdf->Cell(15, $h, '', 1, 2, 'C');
	
	$tcpdf->Ln($Funcionespropias->corrigeSaltoLineaFirma($varDireccion));
	$tcpdf->SetX(16);
	$tcpdf->Cell($w+30, $h, 'Presidente Junta Calificadora', 'T', 0, 'C');
	$tcpdf->Cell(7);
	$tcpdf->Cell($w+30, $h, 'Integrante Junta Calificadora', 'T', 0, 'C');
	$tcpdf->Cell(7);
	$tcpdf->Cell($w+30, $h, 'Integrante Junta Calificadora', 'T' , 1, 'C');
	
	$tcpdf->SetX(14);$tcpdf->SetFontSize(9);
	$tcpdf->Cell($w+30, $h, utf8_encode($nombreFirmantes[$listaFirmantes['slc_presi']]), 0, 0, 'C');
	$tcpdf->Cell(7);
	$tcpdf->Cell($w+30, $h, utf8_encode($nombreFirmantes[$listaFirmantes['slc_integrante1']]), 0, 0, 'C');
	$tcpdf->Cell(7);
	$tcpdf->Cell($w+30, $h, utf8_encode($nombreFirmantes[$listaFirmantes['slc_integrante2']]), 0, 0, 'C');
	$tcpdf->SetFontSize(10);
	
	$tcpdf->Ln($Funcionespropias->corrigeSaltoLineaFirma($varDireccion, 15));
	$tcpdf->SetX(14);
	$tcpdf->Cell($w+30, $h, 'Integrante Junta Calificadora', 'T' , 0, 'C');
	$tcpdf->Cell(7);
	$tcpdf->Cell($w+30, $h, 'Integrante Junta Calificadora', 'T' , 0, 'C');
	$tcpdf->Cell(7);
	$tcpdf->Cell($w+30, $h, 'Representante del Personal', 'T' , 1, 'C');
	
	$tcpdf->SetX(16); $tcpdf->SetFontSize(9);
	$tcpdf->Cell($w+30, $h, utf8_encode($nombreFirmantes[$listaFirmantes['slc_integrante3']]), 0, 0, 'C');
	$tcpdf->Cell(7);
	$tcpdf->Cell($w+30, $h, utf8_encode($nombreFirmantes[$listaFirmantes['slc_integrante4']]), 0, 0, 'C');
	$tcpdf->Cell(7);
	$tcpdf->Cell($w+30, $h,utf8_encode($nombreFirmantes[$listaFirmantes['slc_representante']]), 0, 0, 'C');
	$tcpdf->SetFontSize(10);
	
	$tcpdf->Ln($Funcionespropias->corrigeSaltoLineaFirma($varDireccion, 14));
	$tcpdf->SetX(14);
	$tcpdf->Cell($w+30, $h, 'Secretario(a) Junta Calificadora', 'T' , 0, 'C');
	$tcpdf->Cell(7);
	$tcpdf->Cell($w+30, $h, 'Representante Asociación', 'T' , 2, 'C');
	
	$tcpdf->SetX(16);$tcpdf->SetFontSize(9);
	$tcpdf->Cell($w+30, $h, utf8_encode($nombreFirmantes[$listaFirmantes['slc_secretario']]), 0, 0, 'C');
	$tcpdf->Cell(7);
	$tcpdf->Cell($w+30, $h, utf8_encode($nombreFirmantes[$listaFirmantes['slc_asociacion']]), 0, 0, 'C');
	$tcpdf->SetFontSize(10);
	
	$tcpdf->Ln($Funcionespropias->corrigeSaltoLineaFirma($varDireccion, 11));
	$tcpdf->SetX(30);
	$tcpdf->Cell($w+15, $h, 'FIRMA FUNCIONARIO:', 0, 0, 0);
	$tcpdf->Cell($w+40, $h, '', 'B' , 0, 0);
	$tcpdf->Cell(7);
	$tcpdf->Cell($w-10, $h, 'FECHA: ', 0, 0, 0);
	$tcpdf->Cell($w+10, $h, '', 'B' , 0, 0);
	
	$tcpdf->Output("Hoja de Calificacion $nombreFuncionario.pdf", "I");
}else{
	echo 'debug:<pre>'.print_r(array(), true).'</pre>';
}
?>