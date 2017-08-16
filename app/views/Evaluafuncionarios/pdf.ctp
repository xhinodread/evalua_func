<?
function marcaNota($subPer, $idPreg, $array){
	if(is_array($array)){
		foreach($array as $pnt => $listaEval){
			//return $idPreg.' == '.$listaEval['Calificafuncionario']['pregunta_id'];
			if( $idPreg == $listaEval['Calificafuncionario']['pregunta_id'] ){
				//return $subPer.' == '.$listaEval['Calificafuncionario']['subperiodo_id'];
				if( $subPer == $listaEval['Calificafuncionario']['subperiodo_id'] ){
					//unset($array[$pnt]);
					return 'X';
					break;
				//}else{
				//	return '';
				}
			}
		}
	}else{ return -1; }
}

if(1){
App::import('Vendor','tcpdf');
$tcpdf = new TCPDF();
$textfont = 'arial-narrow'; //'freesans';
$tcpdf->SetCreator(PDF_CREATOR);
$tcpdf->SetAuthor("GoreCoquimbo");
$tcpdf->SetTitle("Evaluafuncionarios");
$tcpdf->SetSubject("Evaluafuncionarios");
$tcpdf->SetKeywords("GoreCoquimbo");
$tcpdf->setPrintHeader(false);
$tcpdf->setPrintFooter(false);
$topM =5;
$BottomM =5;
//$tcpdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$tcpdf->SetMargins(PDF_MARGIN_LEFT, $topM, PDF_MARGIN_RIGHT);
//$tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
////$tcpdf->SetAutoPageBreak(TRUE, $BottomM);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$tcpdf->SetFont($textfont, "", 20);
if(0){
	$tcpdf->SetFontSize(10);
	$tcpdf->AddPage();
	///$tcpdf->writeHTML('listaJustFunc:<pre>'.print_r($listaJustFunc, true).'</pre><br />', 0, 0, 0, 0, 'L');
	$tcpdf->writeHTML('elFuncionario:<pre>'.print_r($elFuncionario, true).'</pre><br />', 0, 0, 0, 0, 'L');
}

$vecHoja = array('P', 'mm', array(210, 350));
$vecHoja = array('P', 'A1');

$page_format = array(
    'MediaBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 210, 'ury' => 297),
    'CropBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 210, 'ury' => 297),
    'BleedBox' => array ('llx' => 5, 'lly' => 5, 'urx' => 205, 'ury' => 292),
    'TrimBox' => array ('llx' => 10, 'lly' => 10, 'urx' => 200, 'ury' => 287),
    'ArtBox' => array ('llx' => 15, 'lly' => 15, 'urx' => 195, 'ury' => 282),
    'Dur' => 3,
    'trans' => array(
        'D' => 1.5,
        'S' => 'Split',
        'Dm' => 'V',
        'M' => 'O'
    ),
    'Rotate' => 0,
    'PZ' => 1,
);

$nombreFuncionario = trim($elFuncionario['Persona']['NOMBRES']).' '.trim($elFuncionario['Persona']['AP_PAT']).' '.trim($elFuncionario['Persona']['AP_MAT']);
/******************************************************/
/******************************************************/
/**************** LISTADO DE FACTORES *****************/
$tcpdf->SetFontSize(9);
foreach($listaFactores as $losFactores){
	////$tcpdf->AddPage('P', 'Legal');
	$tcpdf->AddPage('P', 'mm', array(210, 370));
	////$tcpdf->AddPage('P', $page_format, false, false);
	///////$tcpdf->Multicell(0, 0, 'Funcionario Id: '.$funcionario_id ,0 ,'L');
	/*** NOMBRE FUNCIONARIO *********** [AP_PAT] */
	$tcpdf->writeHTML($nombreFuncionario, 1, 0, 0, 0, 'L');
	/*** FACTORES ***********/
	$tcpdf->Multicell(0, 0, 'FACTOR '.$losFactores['Factor']['etiqueta'] ,0 ,'C');
	//$tcpdf->Cell(0, 10, $losFactores['Factor']['etiqueta'] ,1 ,1 ,'C');
	$tcpdf->Ln();
	$tcpdf->Multicell(0, 0, $losFactores['Factor']['descripcion'] ,0 ,'L');
	$tcpdf->Ln();
	/*** SUBFACTORES ***********/
	$cntPag=1;
	$nroSubfactores = count($losFactores['Subfactor']);
	$cntSubfactores=1;
	foreach($losFactores['Subfactor'] as $losSubfactores ){
		////$tcpdf->Cell(0, 10, print_r($losSubfactores, true) ,1 ,1 ,'L');
		//$tcpdf->Write(0, $losSubfactores['id'].') SubFactor de '.$losSubfactores['etiqueta'], 0, 0, 'L');
		if($cntPag > 2){
			//$tcpdf->AddPage();
			$cntPag=1;
		}
		/// .$cntPag.', '
		// $losSubfactores['id'].')'.
		$lblSubFactor = '<u>'.'a) SubFactor de '.$losSubfactores['etiqueta'].':</u> ';
		$descrSubFactor = $losSubfactores['descripcion'];
		$tcpdf->writeHTML($lblSubFactor.$descrSubFactor, 0, 0, 0, 0, 'L');
		$tcpdf->Ln(8);
		/*** ITEMS ***********/
		//$tcpdf->writeHTML('losSubfactores:'.print_r($losSubfactores, true).'<br />', 0, 0, 0, 0, 'L');		
		foreach($losSubfactores as $ind => $losItems){
			//$tcpdf->writeHTML($ind.', losSubfactoresInd:'.print_r($losSubfactores[$ind], true).'<br />', 0, 0, 0, 0, 'L');
			//$tcpdf->writeHTML($ind.':<br />', 0, 0, 0, 0, 'L');
			if($ind == 'Item'){
				//$tcpdf->writeHTML('losItems:<pre>'.print_r($losItems, true).'</pre><br />', 0, 0, 0, 0, 'L');
				//$tcpdf->writeHTML('CNT losItems:'.count($losItems).'<br /><br />', 0, 0, 0, 0, 'L');
				//$tcpdf->writeHTML('losItems:'.print_r($losItems, true).'<br />', 0, 0, 0, 0, 'L');
				////$tcpdf->Multicell(140, 8, $losItems[0]['id'].') '.$losItems[0]['etiqueta'], 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'B', false );
				////$tcpdf->Ln();
				$nroItems = count($losItems);
				$cntItems=1;
				$altoCelItems=8;
				foreach($losItems as $ind2 => $listaItems){
					$tcpdf->SetFont($textfont, "B" );
					/// $cntPag.', '.
					$test='';
					$valLn = 0;
					if($ind2 >=3){
						//$tcpdf->Ln(10);
						$test=' XXX '; $valLn = 1;
						$tcpdf->Multicell(0, $altoCelItems, '', 0, 'C', false, 1, '', '', true, 0, false, true, $altoCelItems, 'M', false );
					}
					//if($ind2 >= 3)$tcpdf->Ln(50);
					$tcpdf->Multicell(150, $altoCelItems, $listaItems['etiqueta'], 1, 'C', false, 0, '', '', true, 0, false, true, $altoCelItems, 'M', false );
					$tcpdf->SetFontSize(8);
					$tcpdf->Multicell(30, $altoCelItems, 'Periodo '.$listaPeriodos['Periodo']['etiqueta'], 1, 'C', false, 1, '', '', true, 0, false, true, $altoCelItems, 'M', false );
					$tcpdf->SetFont($textfont, "" );
					$tcpdf->Multicell(30, $altoCelItems, 'CONCEPTO', 1, 'C', false, 0, '', '', true, 0, false, true, $altoCelItems, 'M', false );
					$tcpdf->Multicell(120, $altoCelItems, 'VALORACION', 1, 'C', false, 0, '', '', true, 0, false, true, $altoCelItems, 'M', false );
					$cntSubPeriodos = count($listaPeriodos['Subperiodo']);
					if($cntSubPeriodos > 0){
						foreach($listaPeriodos['Subperiodo'] as $losSubperiodos){
							$tcpdf->Multicell((30/$cntSubPeriodos), $altoCelItems, $losSubperiodos['etiqueta'], 1, 'C', false, 0, '', '', true, 0, false, true, $altoCelItems, 'M', false );
						}
					}else{ 
						$tcpdf->Multicell(30, $altoCelItems, 'Sin SubPeriodos', 1, 'C', false, 1, '', '', true, 0, false, true, 0, 'B', false );
					}
					//if($ind2 >= 3)$tcpdf->Ln(50);
					$tcpdf->Ln();
					$cntPag++;
					/*** PREGUNTAS ***********/
					foreach($listaItems as $ind3 => $lasPreguntas){
						if($ind3 == 'Pregunta'){
							//$tcpdf->SetFontSize(8);
							$altoCel=10;
							//$tcpdf->writeHTML($ind3.'; lasPreguntas:<pre>'.print_r($lasPreguntas, true).'</pre><br />', 0, 0, 0, 0, 'L');
							foreach($lasPreguntas as $laPregunta){
								$nombreValor = $vecPreguntaValor[$laPregunta['pregunta_valor_id']];
								$tst = ', '.$altoCel;
								//$idPregunta = $laPregunta['id'];		
								$idPreguntaB = $laPregunta['id'];
								///$tcpdf->SetFillColor(255,255,128);
								$tcpdf->SetFillColor($colorConcepto[array_search($nombreValor, $preguntaValor)]['r'], $colorConcepto[array_search($nombreValor, $preguntaValor)]['g'], $colorConcepto[array_search($nombreValor, $preguntaValor)]['b']);
								// CONCEPTO ***
								$lngTxt = strlen(trim($laPregunta['descripcion']));
								$auxAlto = 0;
								if($lngTxt > 240)$auxAlto = 5;
								$tcpdf->Multicell(30, $altoCel+$auxAlto, $nombreValor, 1, 'C', true, 0, '', '', true, 0, false, true, $altoCel+$auxAlto, 'M', false );
								$tcpdf->SetFillColor(255,255,255);
								// VALORACION ***
								if($lngTxt >= 231){$tcpdf->SetFontSize(7); }else{ $tcpdf->SetFontSize(8); }
								$tcpdf->Multicell(120, $altoCel+$auxAlto, trim($laPregunta['descripcion']), 1, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', false );
								//$tcpdf->CellFitScale(120, $altoCel+$auxAlto, $laPregunta['descripcion'], 1, 0, 'L');
								      //CellFitScale($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
								
								if($cntSubPeriodos > 0){
									foreach($listaPeriodos['Subperiodo'] as $losSubperiodos){
										$idPregunta = $laPregunta['id'];
										$valorPregunta = array_search($nombreValor, $preguntaValor);
										$nomSubPeri = $losSubperiodos['etiqueta'];
										$idSubPeri = $losSubperiodos['id'];
										//$respu =0;
										$respu = marcaNota($idSubPeri, $idPregunta, $listaCalificafuncionario);
										$tcpdf->SetFontSize(10); //// DEBUG // $tcpdf->SetFontSize(8);
										// SUB PERIODOS ***
										$tcpdf->Multicell((30/$cntSubPeriodos), $altoCel+$auxAlto, $respu, 1, 'C', false, 0, '', '', true, 0, false, true, $altoCel, 'M', false );
										$tcpdf->SetFontSize(8);
									}
								}else{ 
									$tcpdf->Multicell(30, $altoCel, 'Sin SubPeriodos', 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'B', false );
								}
								$tcpdf->Ln();
							}
							//// if($ind2 >= 3)$tcpdf->Ln(50);
						}
						//// if($ind2 >= 3)$tcpdf->Ln(50);
					}
					if($cntItems != $nroItems){
						if($nroItems > 1)$tcpdf->Ln();
						////if($ind2 >=3){ $tcpdf->Ln(); $test=' XXX '; }
					}
					$cntItems++;
				}
			}
		}
		$cntSubfactores++;
		/*** TEXTO JUSTIFICACION EVALUACIONES ***/		
		$idSubFac = $losSubfactores['id'];
		$txtObsJust='';
		foreach($listaPeriodos['Subperiodo'] as $losSubperiodos){
			$idSubPer = $losSubperiodos['id'];
			$txtObsJust=''; 
			foreach($listaJustFunc as $pntLsJF => $listaDatos){
				if( ($listaDatos['subperiodo_id']==$idSubPer) && ($listaDatos['subfactore_id']==$idSubFac) ){
					$txtObsJust = $listaDatos['texto'];
					//unset($listaJustFunc[$pntLsJF]);
					break;
				}
			}
			////$tcpdf->Multicell(0, 5, 'JUSTIFICACION '.strtoupper($losSubperiodos['etiqueta']).': '.ucfirst($txtObsJust), 1, 'L', false, 1, '', '', true, 0, false, true, 0, 'M', false );
			$tcpdf->Multicell(0, 5, 'JUSTIFICACION '.strtoupper($losSubperiodos['etiqueta']).': '.ucfirst(mb_strtolower($txtObsJust, 'UTF-8')), 1, 'L', false, 1, '', '', true, 0, false, true, 0, 'M', false );
			//$tcpdf->Multicell(0, 5, $idSubPer.', '.$idSubFac.' ||| '.print_r($listaJustFunc, true), 1, 'L', false, 1, '', '', true, 0, false, true, 0, 'M', false );
		}
		$txt ='<pre>'.print_r($preguntaValornota, true).'</pre> '.'<pre>'.print_r($notasSubfac, true).'</pre>, '.$idSubFac.', '.$notasSubfac[$idSubFac];
		$tcpdf->Multicell(0, 5, 'NOTA SUBFACTOR '.strtoupper($losSubfactores['etiqueta']).': '.$preguntaValornota[$notasSubfac[$idSubFac]], 1, 'L', false, 1, '', '', true, 0, false, true, 0, 'M', false );
		/********/
		if($nroItems > 1)$tcpdf->Ln();
		if($cntPag > 2){
			if($cntSubfactores <= $nroSubfactores){
				$cntPag=1;
				////$tcpdf->AddPage($vecHoja);
				$tcpdf->AddPage('P', 'mm', array(210, 370));
				////$tcpdf->AddPage('P', $page_format, false, false);
				$tcpdf->writeHTML($nombreFuncionario, 1, 0, 0, 0, 'L');
				////$tcpdf->Ln();
			}
		}
		
		$tcpdf->SetFontSize(9);
		$tcpdf->Ln();
	}
}

/*** PRUEBA, PARA CUANDO SE VACIAN LOS VECTORES ***/
//$tcpdf->writeHTML('listaJustFunc:<pre>'.print_r($listaJustFunc, true).'</pre><br />', 0, 0, 0, 0, 'L');

$tcpdf->Output("ejemplo.pdf", "I");
}else{echo '<pre>'.print_r($listaFactores, true).'</pre>';}
?>