<? //='listaFactor:<pre>'.print_r($listaFactor,1).'</pre>';?>
<? //='subPerId:<pre>'.print_r($subPerId,1).'</pre>';?>
<? //='listaJustFuncTmp:<pre>'.print_r($listaJustFuncTmp,1).'</pre>';?>
<?
//App::import('Vendor','Funcionespropias');
$Funcionespropias = new Funcionespropias();
$arrayNotas = array();
$arrayPromedios = array();
?>
<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <fieldset>
        <legend class="lbl01" ><?='PARCIAL HOJA DE PRECALIFICACIÓN / Periodo: '.$periodoNombre.'<br />Subperiodo: '.$subPeriodoNombre?></legend>
        <h1><?=$datosUserFuncionario['Persona']['NOMBRES'].' '.$datosUserFuncionario['Persona']['AP_PAT'].' '.$datosUserFuncionario['Persona']['AP_MAT'];?></h1>        <table>
        	<tr>
            	<td style="border-bottom:#000 solid 2px;"></td>
                <td style="text-align:center; font-weight:bold; border-bottom:#000 solid 2px; " >Nota SubFactor</td>
                <td style="text-align:center; font-weight:bold; border-bottom:#000 solid 2px; " >Nota Factor</td>
            </tr>
            
        <? foreach($listaFactor as $listado){ ?>
        	<tr>
            	<th><?=$listado['Factor']['etiqueta']?></th>
                <th></th>
                <td style="text-align:center;" >
					<?=$this->Form->input('nF_'.$listado['Factor']['id'], array('div'=>false
																				, 'label'=>false
																				, 'readonly'=>true
																				, 'default'=>''
																				, 'class'=>'txtCentro')
										);
					?>
				</td>
            </tr>
			<? $subFactorJustifica = array(); ?>
            <? foreach($listado['Subfactor'] as $listadoSub){ ?>
        	<tr>
            	<td><?=$listadoSub['etiqueta']?></td>
                <td style="text-align:center;" >
					<?=$this->Form->input('nSf_'.$listadoSub['id'], array('div'=>false
																		, 'label'=>false
																		, 'readonly'=>true
																		, 'default'=>$lstNotas[$listadoSub['id']]
																		, 'class'=>'txtCentro')
										);
						$arrayNotas[$listado['Factor']['id']][] = $lstNotas[$listadoSub['id']]; 
						//echo '- '.($listaJustFuncTmp).', '.$listadoSub['id'];
						$datosJustificacion = $Funcionespropias->obtenerJustificacion($listaJustFuncTmp, $listadoSub['id']);
						// echo '<br />- datosJustificacion: '.print_r($datosJustificacion, 1);
						$subFactorJustificaTmp = $Funcionespropias->listarJustificacion($datosJustificacion[$listadoSub['id']]);
						//echo '<br />- subFactorJustificaTmp: '.print_r($subFactorJustificaTmp, 1);
						$subFactorJustifica = array_merge($subFactorJustifica, $subFactorJustificaTmp);
						// echo '<br />- subFactorJustifica: '.print_r($subFactorJustifica, 1);
						// $subFactorJustifica = array();
					?>
                    
                </td>
            </tr>
            <tr>
                <td colspan="2" >
                <?
					//echo '<br />- subFactorJustifica: '.print_r($subFactorJustifica, 1);
					
					foreach($subFactorJustifica as $lista){
						$posGuion = strpos($lista, ' ');
						$nroSubperiodo = substr($lista, 0, $posGuion);
						echo '- '.$arraySubperiodosNombres[$nroSubperiodo].', '.substr($lista, $posGuion).'<br />';
						// $tcpdf->MultiCell(170, 20, '- '.$arraySubperiodos[$nroSubperiodo].', '.substr($lista, $posGuion), 0 , 'L', 0, 0);
						// $tcpdf->Ln(3);
					}
					$subFactorJustifica = array();
					?>
                </td>
            </tr>
            <? } ?>
             <tr>
            	<td colspan="3" >
					<? // ='- subFactorJustifica: <pre>'.print_r($subFactorJustifica, 1).'</pre>';?>
                    <? //='<b>Justificaciones:</b> <br />'.implode('<br />', $subFactorJustifica);?>
                </td>
            </tr>
            <tr>
            	<td colspan="3" style="border-bottom:#000 solid 2px;"></td>
            </tr>
            
        <? } ?> 
        </table>
    </fieldset>
    <? //='<pre>'.print_r($arrayNotas, 1).'</pre>'?>
</div>
<?
/*** POSIBLE NUEVA FUNCION sumarNotas() ***/
$laNota = 0;
foreach($arrayNotas as $pnt => $lista){
	$laNota = 0;
	foreach($lista as $listaNotas){
		if( is_numeric($listaNotas) && $listaNotas != '' ){
			$laNota += $listaNotas;
		}
	}
	$arrayPromedios[$pnt] = array($laNota, count($lista));
}
?>
<?
/*** POSIBLE NUEVA FUNCION ponerPromedios() ***/
$promedio = 0;
foreach($arrayPromedios as $pnt => $lista){
	//echo 'nF_'.$pnt.' <br />';
	$promedio = round(($lista[0] / $lista[1]));
	if($promedio > 0 ){
		echo "<script>var textInput = document.getElementById('nF_".$pnt."');textInput.value = '".$promedio."';</script>";
	}
}
?>


