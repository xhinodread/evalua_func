<?
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/PHPExcel.php'));
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("GoreCoquimbo")
                             ->setLastModifiedBy("GoreCoquimbo")
                             ->setTitle("XLSX CALIFICACION")
                             ->setSubject("XLSX CALIFICACION")
                             ->setDescription("XLSX CALIFICACION ".strtoupper($nombreEscalafon) )
                             ->setKeywords("XLSX CALIFICACION ".strtoupper($nombreEscalafon) )
                             ->setCategory("XLSX CALIFICACION");


$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', strtoupper($nombreEscalafon));
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'CALIFICACIÓN / Periodo: '.$perNombre);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Subperiodo: '.$subPerNombre );

$labelsFactores = array();
foreach($factores as $pnt => $listaFactores){ 
	// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($pnt+6), $listaFactores['Factor']['etiqueta'] );
	// $objPHPExcel->setActiveSheetIndex(0)->setCellValue( chr(66+$pnt).'4' , $listaFactores['Factor']['etiqueta'] );
	//$objPHPExcel->setActiveSheetIndex(0)->setCellValue( chr(66+$pnt).'5' , $listaFactores['Factor']['etiqueta'] );
	$labelsFactores[] = $listaFactores['Factor']['etiqueta'];
	//$objPHPExcel->setActiveSheetIndex(0)->setCellValue( chr(66+$pnt).'5' , chr(66+$pnt) );
} 

/*
*** Subfactores ***
*/
$celdasParaCombinarCentrar = array();
$valChr = 65;
$filaSubItem = '6';
foreach($factores as $pnt0 => $listaFactores){
	foreach($listaFactores['Subfactor'] as $pnt => $listaSubfactores){
		$valChr++;
		$celdasParaCombinarCentrar[$pnt0][] = chr($valChr).'5';
		/// $celdasSunItems[$pnt0][] = chr($valChr).'8';
		$idSubfactor[] = $listaSubfactores['id']; // .', '.$listaSubfactores['etiqueta'];
		// $nuevoTexto = str_replace(' ', chr(13), $listaSubfactores['etiqueta']);
		// $nuevoTexto = $listaSubfactores['id'].chr(13).$listaSubfactores['etiqueta'];
		$nuevoTexto = $listaSubfactores['etiqueta'];
		
		$rangoCelda = chr($valChr).$filaSubItem.':'.chr($valChr).$filaSubItem;
		$laCelda = chr($valChr).$filaSubItem;
		$celdasSubItems[$pnt0][] = $laCelda;
		
		// $objPHPExcel->setActiveSheetIndex(0)->setCellValue( chr($valChr).'6' , $listaSubfactores['etiqueta'].chr(13).'....');
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(chr($valChr))->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getRowDimension($filaSubItem)->setRowHeight(50);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($laCelda)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($rangoCelda)->getAlignment()->setWrapText(true);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $laCelda , $nuevoTexto);
		
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($rangoCelda)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($rangoCelda)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($rangoCelda)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($rangoCelda)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    }
}
$siguienteCeldaParaCombinarCentrar = chr($valChr+1).'5';

/*
$PRIMERO = array_shift($celdasParaCombinarCentrar[3]);
$ULTIMO = array_pop($celdasParaCombinarCentrar[3]);
// $objPHPExcel->getActiveSheet()->mergeCells($celdasParaCombinarCentrar[0][0].':'.$celdasParaCombinarCentrar[0][3]);
$objPHPExcel->getActiveSheet()->mergeCells($PRIMERO.':'.$ULTIMO);
*/

/*
*** Factores ***
*/
$PRIMERO = '';
$ULTIMO = '';
foreach($celdasParaCombinarCentrar as $pnt => $lista){
	//$laLista = print_r($lista, 1);
	$PRIMERO = array_shift($lista);
	$ULTIMO = array_pop($lista);
	
	$letraPrimero = substr($PRIMERO, 0, 1);
	
	$objPHPExcel->getActiveSheet()->mergeCells($PRIMERO.':'.$ULTIMO);
	// $objPHPExcel->setActiveSheetIndex(0)->setCellValue( $PRIMERO , $pnt.') '.$PRIMERO.':'.$ULTIMO.'; '.$laLista );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $PRIMERO , $labelsFactores[$pnt] );
	//$objPHPExcel->setActiveSheetIndex(0)->getStyle($PRIMERO)->getAlignment()->setWrapText(true);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle($PRIMERO)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle($PRIMERO.':'.$ULTIMO)->getAlignment()->setWrapText(true);
	
	$objPHPExcel->setActiveSheetIndex(0)->getStyle($PRIMERO.':'.$ULTIMO)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle($PRIMERO.':'.$ULTIMO)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle($PRIMERO.':'.$ULTIMO)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle($PRIMERO.':'.$ULTIMO)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	//$objPHPExcel->getActiveSheet()->getColumnDimension($letraPrimero)->setWidth(12);
	// $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($letraPrimero)->setWidth(10);
	// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
}
$rangoCelda = $siguienteCeldaParaCombinarCentrar.':'.$siguienteCeldaParaCombinarCentrar;
$objPHPExcel->setActiveSheetIndex(0)->getRowDimension('5')->setRowHeight(30);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $siguienteCeldaParaCombinarCentrar , 'CALIFICADO' );
$objPHPExcel->setActiveSheetIndex(0)->getStyle($rangoCelda)->getAlignment()->setWrapText(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(substr($siguienteCeldaParaCombinarCentrar, 0, 1))->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getStyle($rangoCelda)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->setActiveSheetIndex(0)->getStyle($rangoCelda)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->setActiveSheetIndex(0)->getStyle($rangoCelda)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->setActiveSheetIndex(0)->getStyle($rangoCelda)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// $objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'A11' , $PRIMERO.', '.$ULTIMO );
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'A12' , print_r($celdasParaCombinarCentrar, 1) );

/*
*** LISTADO CON LOS NOMBRES DE LOS CALIFICADOS ***
*/
$arrayPosicionCeldas = array();
foreach($celdasSubItems as $listaCeldas){
	foreach($listaCeldas as $celda){
		$arrayPosicionCeldas[] = $celda;
	}
}
/*** NOMBRES ***/
$checkedEvLid = '';
$lineaLista = (string)(((int)$filaSubItem) + 1);
$arrayFuncionarioCalificado = array();
foreach($listaPersona as $listaFunc){
	$idFuncionario = $listaFunc['Persona']['id_per'];
	$nmbreFunct = utf8_encode($listaFunc['Persona']['NOMBRES'].' '.$listaFunc['Persona']['AP_PAT'].' '.$listaFunc['Persona']['AP_MAT']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'A'.$lineaLista , $idFuncionario.' '.$nmbreFunct );
	foreach($arrayPosicionCeldas as $pnt => $listaCeldas){
		$posicionCelda = $listaCeldas;
		$letraCelda = substr($posicionCelda, 0, 1);
		$nroCelda = substr($posicionCelda, 1);
		$nuevPosicionCelda = $letraCelda.$lineaLista;
		// $objPHPExcel->setActiveSheetIndex(0)->setCellValue( $nuevPosicionCelda, $idSubfactor[$pnt].chr(13).$idFuncionario );
		
		$varTmp = 7;
		
		/*** NOTAS ***/
		foreach($notasFuncionarios as $pnt1 => $listaNotas){
			// $checkedEvLid = 'X';
			if( $listaNotas['Notasubfactor']['funcionario_id'] == $idFuncionario 
			&&  $listaNotas['Notasubfactor']['subfactore_id'] == $idSubfactor[$pnt] ){
				$laNota = $listaNotas['Notasubfactor']['nota'];
				// $objPHPExcel->setActiveSheetIndex(0)->setCellValue( $nuevPosicionCelda, $idSubfactor[$pnt].chr(13).$idFuncionario.': '.chr().' - '.$laNota );
				$elPeriodoId = $perId;
				$notaFinalMostrar = $laNota;
				if( $listaCalifFunc[$elPeriodoId][$idSubfactor[$pnt]][$idFuncionario] ){
					$notaFinalMostrar = $listaCalifFunc[$elPeriodoId][$idSubfactor[$pnt]][$idFuncionario];
				}
				$checkedEvLid = 'X';
				if(in_array($idFuncionario, $arrayChkNotas))$checkedEvLid = '√';
				$arrayFuncionarioCalificado[$idFuncionario] = $checkedEvLid;
				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $nuevPosicionCelda, $notaFinalMostrar );
				$objPHPExcel->setActiveSheetIndex(0)->getStyle($nuevPosicionCelda)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle($nuevPosicionCelda)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle($nuevPosicionCelda)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle($nuevPosicionCelda)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle($nuevPosicionCelda)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
				// $objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'I'.$lineaLista, $checkedEvLid );
				// $objPHPExcel->setActiveSheetIndex(0)->getStyle('I'.$lineaLista)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}else{
				//$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'K'.$varTmp, '->'.print_r($listaNotas, 1) );
				$varTmp++;
			}
		}
		$celdaCalificado = chr( ((int)ord($letraCelda)) + 1 );
		// $objPHPExcel->setActiveSheetIndex(0)->setCellValue( $celdaCalificado.$lineaLista, $idFuncionario.', '.$checkedEvLid.', '.$arrayFuncionarioCalificado[$idFuncionario] );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $celdaCalificado.$lineaLista, $arrayFuncionarioCalificado[$idFuncionario] );
		$objPHPExcel->setActiveSheetIndex(0)->getStyle($celdaCalificado.$lineaLista)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
	$lineaLista++;
}


/*** NOTAS *** /
$arrayPosicionCeldas = array();
foreach($celdasSubItems as $listaCeldas){
	foreach($listaCeldas as $celda){
		$arrayPosicionCeldas[] = $celda;
	}
}
foreach($arrayPosicionCeldas as $pnt => $listaCeldas){
	$posicionCelda = $listaCeldas;
	$letraCelda = substr($posicionCelda, 0, 1);
	$nroCelda = substr($posicionCelda, 1);
	$nuevPosicionCelda = $letraCelda.($nroCelda+1);
	// $objPHPExcel->setActiveSheetIndex(0)->setCellValue( $listaCeldas, $idSubfactor[$pnt].', '.$letraCelda.'. '.$nroCelda );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $nuevPosicionCelda, $idSubfactor[$pnt] );
}
*****/

if(0):

$valChr = 65;
foreach($factores as $pnt0 => $listaFactores){
	$idFactor = $listaFactores['Factor']['id'];
	$nombreFactor = $listaFactores['Factor']['etiqueta'];
	
	$valChr++;
	$laCelda = chr($valChr).'7';
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $laCelda , $idFactor.',, '.$nombreFactor );
	
	foreach($celdasSubItems as $listaCeldas){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $listaCeldas, $idFactor.',, '.$nombreFactor );
	}
	if(0):
	foreach($celdasSubItems as $listaCeldas){
		foreach($listaCeldas as $listaPosicionCeldas){
			/*
			$posicionCelda = $listaPosicionCeldas;
			$letraCelda = substr($posicionCelda, 0, 1);
			$nroCelda = substr($posicionCelda, 1, 0);
			$nuevPosicionCelda = substr($posicionCelda, 1, 1);
			*/
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $listaPosicionCeldas, $idFactor.',, '.$nombreFactor );
		}
/*		$posicionCelda = $listaCeldas;
		$letraCelda = substr($posicionCelda, 0, 1);
		$nuevPosicionCelda = substr($posicionCelda, 1, 1);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue( $listaCeldas[($listaFactores['Factor']['id']+1)] 
															, $listaFactores['Factor']['id'].', '.$listaFactores['Factor']['etiqueta'] );
*/
	}
	endif;
	
	// $factor_id = $listaFactores['Persona']['NOMBRES'].' '.$listaFunc['Persona']['AP_PAT'].' '.$listaFunc['Persona']['AP_MAT'];
}


endif;

// $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A7')->setWidth(40);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('A7:A7')->getAlignment()->setWrapText(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);



/*
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'B14' , print_r($idSubfactor, 1) );
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'B15' , print_r($celdasSubItems, 1) );
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'B15' , print_r($arrayPosicionCeldas, 1) );
$objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'K7' , print_r($notasFuncionarios, 1) );
*/

/* 
*** FIN CUERPO ***
*/

$objPHPExcel->getActiveSheet()->setTitle(strtoupper($nombreEscalafon));
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$nombreEscalafon.'_'.$subPerNombre.$perNombre.'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>