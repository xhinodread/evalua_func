<style>
	ol li { list-style-type: lower-alpha; font-weight:bold; }
	.flotaDerecha{ float:right; }
	/*.centroMedio{ text-align:center; alignment-adjust:middle;}*/
	.izqMedio{ text-align:left; alignment-adjust:middle }
	.XXXcssTextarea{ resize: none; font-size:10px;  }
</style>
<? 
	$optionsConClassCss=array('div' => false, 'type'=>'submit', 'class'=>'botnVolver');
	$optionsStyle=array('div' => false, 'type'=>'submit', 
        'style'=>'background:transparent;border:hidden;color:#003d4c;text-decoration:underline;border-radius:0;cursor:pointer;font-weight:bold;'); 
	$elSubfactor = $listaPreguntas['Subfactor'];
	$optionsOption = array();
	$attributesOption = array('legend' => false, 'label' => false, 'value'=>'no' );

	function getEvaluacion($listaEvaluacion, $idItem){
		$lanota=0;
		foreach($listaEvaluacion as $nota){if($nota['item_id'] == $idItem)$lanota=$nota['pregunta_id'];}
		return $lanota;
	}
	function getEvaluacionOp($listaEvaluacion, $idItem, $idSubperiodo){
		$lanota=0;
		foreach($listaEvaluacion as $nota){
				if($nota['item_id'] == $idItem && $nota['subperiodo_id'] == $idSubperiodo){
					$lanota=$nota['pregunta_id'];
				}
		}
		return $lanota;
	}	
?>
<div class="divPrinc" >
    <? //='ponenota: '.$ponenota.'<br />';?>
    <nav class="flotaDerecha" >
    	<? $urlIdFunc = $elIdFuncionario; $urlIdPeriodo = $elPeriodo; ?>
        <?=$this->Form->create(null, array('url' => array('controller'=>'Evaluafuncionarios', 'action'=>'FactorfuncionarioTodos') ));?>
        <?=$this->Form->hidden('funcionario_id', array('default'=>$urlIdFunc) );?>
        <?=$this->Form->hidden('elPeriodo', array('default'=>$urlIdPeriodo) );?>
        <?=$this->Form->submit('Volver', $optionsStyle );?>
        <?=$this->Form->end();?>
	</nav>
    <br><br>
    <fieldset>
        <legend class="lbl01" >FACTOR <?=$listaPreguntas['Factor']['etiqueta']?></legend>
        <label style="font-size:13px;"  ><?=$listaPreguntas['Factor']['descripcion'];?></label>
        <h4><?=utf8_encode($elFuncionario['Persona']['NOMBRES'].' '.$elFuncionario['Persona']['AP_PAT'].' '.$elFuncionario['Persona']['AP_MAT'])?></h4>
        <ol type="A">
        	<?=$this->Form->create('Evaluar', array('url' => array( 'controller'=>'Evaluafuncionarios', 'action'=>'Evaluacionfuncionario' )) );?>
        	<? foreach($elSubfactor as $SubFactValor){ ?>
            	<li style="font-size:13px;" >
                    <u>Subfactor <?=(($SubFactValor['etiqueta']))?></u>:
                    <?=($SubFactValor['descripcion'])?>
                    <? //='<br />Subfactor Id:'.(($SubFactValor['id']))?>
                </li>
                <? $notaValor = 0; $nroNotas = 0; ?>
                <? foreach($SubFactValor['Item'] as $elItem){ ?>
	                <? $SubfactorId= $elItem['id']; ?>
                    <table cellpadding="0" cellspacing="0" border="0" class="tablaFactor" >
                     <tr >
                        <th colspan="2" style="text-align:center; font-size:16px;" ><u><?=$elItem['etiqueta']?>:</u>
                        <? // .', '.$elItem['id']; ='<pre>'.print_r($subPeriodos, true).'</pre>' elSubfactor?>
                        </th>
                        <th width="383" colspan="<?=count($periodo)?>" style="text-align:center" >
                        	Período<br>
							<label style="font-size:13px;"><?=$periodo[0]['Periodo']['etiqueta']; /*.', '.$periodo[0]['Periodo']['id']*/?></label>
                            <label style="font-size:10px;">(Seleccione una opción)</label>
                        </th>
                     </tr>
                     
                     <tr>
                        <td width="65" scope="col" class="centroMedio" style="font-size:12px; font-weight:bold;" >CONCEPTO</td>
                        <td width="747" scope="col" class="centroMedio" style="font-size:12px; font-weight:bold;" >VALORACION
                        	<? $idNota = getEvaluacion($listaRespEval2, $elItem['id']); ?>
                        </td>
                        <? foreach($periodo as $subPer){ ?>
                        <td scope="col" class="centroMedio" style="font-size:12px; font-weight:bold;" ><?=$subPer['Subperiodo']['etiqueta']?></td>
                        <? } ?>
                     </tr>
                    <? foreach($elItem['Pregunta'] as $laPregunta){ ?>
                     <tr>
                        <td scope="col" style="background-color:<?=$colorConcepto[$laPregunta['PreguntaValor']['valor']]?>; font-size:16px;" class="centroMedio" >
							<b><?=$laPregunta['PreguntaValor']['etiqueta'];?></b>
                        </td>
                        <td scope="col" style="font-size:15px;"><?=$laPregunta['descripcion'];?></td>
                        <!--****************************************-->
                        <? foreach($periodo as $subPer){ ?>
                        	<td scope="col" class="centroMedio" >
                             <? if($subPer['Subperiodo']['periodo_id'] == $periodo[0]['Periodo']['id']): ?>
                            	<? if($subPer['Subperiodo']['id'] == $elPeriodo){ ?>
                                        <? $optionsOption = array($laPregunta['id'] => ''); ?>
                                        <?=$this->Form->hidden('Item'.$laPregunta['item_id'], array('default'=>$laPregunta['item_id']));?>
                                        
                                        	<? $attributesOption = array('legend' => false, 'label' => false, 'fieldset' => false, 'value'=>$idNota ); ?>
                                            <? //=$this->Form->radio('NotaItem'.$laPregunta['item_id'], $optionsOption, $attributesOption);?>
                                        
                                        <? if($laPregunta['id'] == $idNota){$notaValor+=$laPregunta['PreguntaValor']['valor']; $nroNotas++; }?>
                                        
                                        <? if($laPregunta['id'] == $idNota){echo 'X'; $notaValor+=$laPregunta['PreguntaValor']['valor']; $nroNotas++; }else{ echo '';}?>
                                        
                                <? }else{ ?>
                                		<? $idNotaOp = getEvaluacionOp($listaRespEvalOp2, $elItem['id'], $subPer['Subperiodo']['id']); /*echo $idNotaOp;*/?>
                                        <? if($laPregunta['id'] == $idNotaOp){echo 'X'; $notaValor+=$laPregunta['PreguntaValor']['valor']; $nroNotas++; }else{ echo '';}?>
                                <? } ?>
                             <? endif; ?>
                            </td>
                            
                        <? } ?>
                        <!--****************************************-->
                     </tr>
                    <? } ?>
                    </table>
                	<!-- <br><br> -->
                <? } ?>
                <? //='NotaValor: '.$notaValor.'/'.$nroNotas.'<br />'?>
            <table cellpadding="0" cellspacing="0" border="0" class="tablaLimpia" >
				<? foreach($periodo as $subPer){ ?>
					<? if($subPer['Subperiodo']['id'] == $subperiodoId){ ?>
                    <tr>
                        <td class="izqMedio" valign="top" >
                            JUSTIFICACION<br />
                            <?=strtoupper($subPer['Subperiodo']['etiqueta'])?>
                            <?
                                $txtObsJust ='';
                                $swtxtObJs=0;
                                $cnt=0;
                                foreach($listaJustFunc as $listaDatos){
                                    if( ($listaDatos['subperiodo_id']==$subPer['Subperiodo']['id']) && ($listaDatos['subfactore_id']==$SubFactValor['id']) ){
                                        $cnt++;
                                        $swtxtObJs=1;
                                        $txtObsJust = $listaDatos['texto'];
                                        break;
                                    }
                                }
                            ?>
                        </td>
                        <td class="izqMedio" >
                            <?=$txtObsJust;?>
                        </td>
                    </tr>
                    <? } // endif *** ?>
                <? } // foreach *** ?>
             	<?
				 if(1):
					$arrayOtrasJustificaciones = array();
					foreach($listaJustFunc as $listaDatos){
						if( ($listaDatos['subperiodo_id'] != $subperiodoId) && ($listaDatos['subfactore_id'] == $SubFactValor['id']) ){
							$arrayOtrasJustificaciones[] = array('nomSubPeriodo' => strtoupper($arraySubPeriodos[$listaDatos['subperiodo_id']])
																, 'texto' => $listaDatos['texto']);
						}
					}
				?>

                <? foreach($arrayOtrasJustificaciones as $listado){ ?>             
                <tr>
                    <td class="izqMedio" valign="top" ><?=$listado['nomSubPeriodo']?></td>
                    <td class="izqMedio" ><?=$listado['texto']?></td>
                </tr>
                <? } endif; ?>    
            </table>
            <?
				/*** NOTA ITEM ***/
				$arrSearch=0;
				if(isset($notasSubfac) && count($notasSubfac)>0){
					//echo '* '.$SubFactValor['id'].'<br />';
					$valorSubFact =(int)trim($SubFactValor['id']);
					//echo '*'.print_r($notasSubfac, true).'<br />';
					//echo '* '.$valorSubFact.'<br />';
					$arrSearch = get_value_by_key($notasSubfac, $valorSubFact);
					//echo 'arrSearch: '.$arrSearch.'<br />';
					if($arrSearch > 0)$arrSearch = $notasSubfac[$valorSubFact];
				}
				/*** NOTA PROPUESTA ***/
				//echo '- '.$notaValor.', '.$nroNotas.'<br />';
				$NotaSubpProp = $preguntaValornota[1];
				$promNotaSubp =0;
				if($notaValor > 0 && $nroNotas > 0){
					$vecNotaImpar = array(1, 3, 5, 7, 9, 11, 13);
					$promNotaSubp = (int)($notaValor/$nroNotas);
					//echo '$vecNotaImpar: '.print_r($preguntaValornota, true).', '.$promNotaSubp.'<br />';
					////$pntVecNotaImpar = array_search($promNotaSubp, $vecNotaImpar);
					$pntVecNotaImpar = get_value_by_key($preguntaValornota, $promNotaSubp);
					//echo '$pntVecNotaImpar: '.$pntVecNotaImpar .'<br />';
					/***** NOTA PROMEDIO MENOS UNO *****/
					//if($vecNotaImpar[$pntVecNotaImpar] >= $promNotaSubp){$promNotaSubp = $vecNotaImpar[$pntVecNotaImpar] -1;}
					if($preguntaValornota[$pntVecNotaImpar] >= $promNotaSubp){$promNotaSubp = $preguntaValornota[$pntVecNotaImpar] -1;} 
					////$NotaSubpProp = $preguntaValor[2];
					//echo '* '.$promNotaSubp.', '.$preguntaValor[$promNotaSubp].'<br />';
					////if($promNotaSubp > 0){$NotaSubpProp = $preguntaValor[$promNotaSubp];}
					if($promNotaSubp > 0){$NotaSubpProp = $preguntaValornota[$promNotaSubp];}
				}
			?>
            <table cellpadding="0" cellspacing="0" border="0" class="tablaLimpia" >
            	<tr>
                	<td>
                    	<? if($ponenota >= 1){ ?>
                            NOTA SUBFACTOR <?=strtoupper($SubFactValor['etiqueta'])?>
                      		<label style="font-size:12px; font-weight:bold; "><?='Nota Propuesta: '.$NotaSubpProp.'';?></label>
                        <? } ?>
                    </td>
                    <td style="text-align:left" >
						<? $nombreValor = 'txtNota'.$periodo[0]['Periodo']['id'].'_'.$SubFactValor['id']; /*$nombreValorX ='';*/ ?>
                        <? //='<br />arrSearch->'.$arrSearch.'<br />';?>
                        <? //'p: '.$ponenota.'<br />';
						if($ponenota >= 1){ 
							echo $this->Form->select($nombreValor, $preguntaValornota, $arrSearch, array('empty' => 'Seleccione nota') );
						}else{
							echo $this->Form->select($nombreValor, $preguntaValornota, $arrSearch, array('empty' => 'Seleccione nota', 'class' => 'oculto') );
						}
						?>
                    </td>
                </tr>
           </table>
           <hr class="hr01" />
		  <? } ?>
        </ol>
    </fieldset>
    <nav>
        <? $styleLink = 'background:transparent;border:hidden;color:#003d4c;text-decoration:underline;border-radius:0;cursor:pointer;font-weight:bold; font-size:20px; '; ?>
        <?=$this->Html->link('Volver', array('controller'=>'Evaluafuncionarios', 'action'=>'FactorfuncionarioTodos', 'funcionario_id' => $urlIdFunc, 'elPeriodo' => $urlIdPeriodo), array('style'=> $styleLink) );?>
	</nav>
</div>