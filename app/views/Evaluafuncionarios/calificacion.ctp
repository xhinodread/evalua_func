<? //='count<pre>'.count($factores).'</pre>';?>
<? //='arrayCntSub<pre>'.print_r($arrayCntSub,1).'</pre>';?>
<? //='factores<pre>'.print_r($factores,1).'</pre>';?>
<? //='listaPersona<pre>'.print_r($listaPersona,1).'</pre>';?>
<? //='notasFuncionarios<pre>'.print_r($notasFuncionarios,1).'</pre>';?>
<? //='notasFuncionarios: <pre>'.print_r($notasFuncionarios,1).'</pre>';?>

<?php echo $this->Html->script('mod01', array('inline' => false)); ?>
<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <br>
    <h2><?=$nombreEscalafon?></h2>
    <fieldset>
        <legend class="lbl01" ><?='CALIFICACIÓN / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre?></legend>
        <?=$this->Form->create('Evaluafuncionario', array('url' => array('controller'=>'Evaluafuncionarios', 'action'=>'calificacion', $idEscalafon) ));?>
		<table border="1" cellpadding="0" cellspacing="0" >
        	<tr>
            	<td colspan="9">
                <?=$this->Form->select('precalificador', $arrayPrecalificadores, $precalifId, 
								array('empty' => 'Seleccione Precalificador...'
								, 'controller' => 'Evaluafuncionarios'
								, 'action' => 'calificacion'
								, 'onChange'=>'location.href = \''.$this->webroot.'Evaluafuncionarios/calificacion/'.$idEscalafon.'/precalifId:\'+this.value' ) );?>
                </td>
            </tr>
			<tr style="border:#000 solid 1px;" >
            	<th>
                	FUNC/FACTOR
                </th>
                <? foreach($factores as $listaFactores){ ?>
                <th colspan="<?=$arrayCntSub[$listaFactores['Factor']['id']]?>" style="text-align:center; border:#000 solid 1px;" >
                   	<?=$listaFactores['Factor']['etiqueta']?>
                </th>
                <? } ?>
                <th>
                	CALIFICADO
                </th>
			</tr>
            <!--------------------------------------------------------------------------->
            <tr>
            	<th>
                	SUBFACTOR
                </th>
                <? foreach($factores as $listaFactores){ ?>
                	<? foreach($listaFactores['Subfactor'] as $listaSubfactores){ ?>
                <td style="text-align:center; " >
                   	<?=$listaSubfactores['etiqueta']?>
                </td>
                	<? } ?>
                <? } ?>
                <td>
                	
                </td>
            </tr>     
            <!-------------------------------------- COMIENZO DE LISTADO --------------------------------------------------------->
            <? $cntBtn=-1; ?>
            <? foreach($listaPersona as $listaFunc){ ?>
	            <? $laidPer=$listaFunc['Persona']['id_per']?>
                <? //='-> '.isset($cntNotasSubfactor[$laidPer]).' ::: '.(strlen($cntNotasSubfactor[$laidPer])).', '.$cntNotasSubfactor[$laidPer].'<br />'?>
                <? /// $cantNotas = $cntNotasSubfactor[$laidPer];(strlen($cntNotasSubfactor[$laidPer])) ?>
                <? $cantNotas = ( isset($cntNotasSubfactor[$laidPer]) ? $cntNotasSubfactor[$laidPer] : 0); ?>
                <? //=$cantNotas.', '.$totalSubfactor?>
                <? if($totalSubfactor <= $cantNotas){ ?>
                <? $cntBtn++; ?>
            <tr>
            	<th>
                	<label onclick="verFoto('<?='Mono'.$laidPer?>');" style="cursor:pointer;" >
						<?=utf8_encode($listaFunc['Persona']['NOMBRES'].' '.$listaFunc['Persona']['AP_PAT'].' '.$listaFunc['Persona']['AP_MAT']);?>
                    </label>
                    <? //='<br>Mono'.$laidPer?>
                    <?='<img id="Mono'.$laidPer.'" style="visibility:hidden;" align="right" class="zoom" src="data:image/jpeg;base64,'
						.base64_encode( $listaFotoPersona[$laidPer]).'"/ width="30" title="'.$listaFunc['Persona']['NOMBRES'].' '.$listaFunc['Persona']['AP_PAT'].' '.$listaFunc['Persona']['AP_MAT'].' ID:'.$laidPer.'"  >';?>
                    <? //=$totalSubfactor.', '.$cntNotasSubfactor[$laidPer] ?>
                </th>
                <? foreach($factores as $listaFactores){ ?>
                	<? foreach($listaFactores['Subfactor'] as $listaSubfactores){ ?>
                <td >
                   	<? //=$listaSubfactores['id']?>
                   <!-- <br> -->
                    <? //=$listaFunc['Persona']['id_per']?>
					<? foreach($notasFuncionarios as $pnt => $listaNotas){ ?>
                    	<? if($listaNotas['Notasubfactor']['funcionario_id'] == $listaFunc['Persona']['id_per'] 
							&& $listaNotas['Notasubfactor']['subfactore_id'] == $listaSubfactores['id']){ ?>
                            <? //='<pre>'.print_r($listaNotas['Notasubfactor'],1).'</pre>'?>
                            <?  $elId=$listaNotas['Notasubfactor']['id'];
								$elPeriodoId=$listaNotas['Notasubfactor']['periodo_id'];
								$elSubfactoreId = $listaNotas['Notasubfactor']['subfactore_id'];
								$elFuncionarioId = $listaNotas['Notasubfactor']['funcionario_id'];
								$laNota = $listaNotas['Notasubfactor']['nota'];
							?>
                            <? //=$listaNotas['Notasubfactor']['nota']?>
                            <?=$this->Form->hidden('id.'.$listaFunc['Persona']['id_per'].'_'.$listaSubfactores['id'], array('default'=>$elId) );?>
                            <?=$this->Form->input('.'.$listaFunc['Persona']['id_per'].'_'.$listaSubfactores['id']
														, array('default'=>$laNota
																,'title'=>$laNota
																,'readonly'=>true
																,'label'=>false
																,'div'=>false
																,'style'=>'background-color:#ccc; text-align:center'
																,'onkeyup'=>'evalNotaCalificacion(this)'
																/*,'onchange'=>'evalNotaCalificacion(this)'*/ ) 
													);?>
                            <?=$this->Form->hidden('califFuncId.'.$listaFunc['Persona']['id_per'].'_'.$listaSubfactores['id'], array('default'=>$listaCalifFunc[$elPeriodoId][$elSubfactoreId]['id']) );?>
                            <?
								$notaFinalMostrar = $laNota;
								if( $listaCalifFunc[$elPeriodoId][$elSubfactoreId][$elFuncionarioId] ){
									$notaFinalMostrar = $listaCalifFunc[$elPeriodoId][$elSubfactoreId][$elFuncionarioId];
								}
								// , array('default'=> $laNota.', '.$listaCalifFunc[$elPeriodoId][$elSubfactoreId][$elFuncionarioId]
							?>
                            <?=$this->Form->input('califFunc'.'.'.$listaFunc['Persona']['id_per'].'_'.$listaSubfactores['id']
														, array('default'=> $notaFinalMostrar
																,'title'=>$listaCalifFunc[$elPeriodoId][$elSubfactoreId][$elFuncionarioId]
																,'label'=>false
																,'div'=>false
																, 'style'=>'text-align:center'
																,'onkeyup'=>'evalNotaCalificacion(this)'
																/*,'onchange'=>'evalNotaCalificacion(this)'*/ ) 
													); 
							?>
                            <? //=$listaCalifFunc[$elPeriodoId][$elSubfactoreId][$elFuncionarioId]?>
                            <? unset($listaCalifFunc[$elPeriodoId][$elSubfactoreId][$elFuncionarioId]); ?>
                            <? unset($notasFuncionarios[$pnt]); ?>
                        <? } ?>
                    <? } ?>
                </td>
                	<? } ?>
                <? } ?>
                <td style="text-align:center;" >
                	<? $checkedEvLid = false;if(in_array($listaFunc['Persona']['id_per'], $arrayChkNotas))$checkedEvLid = true; ?>
                	<? /*=$this->Form->checkbox('chkNota'.'.'.$listaFunc['Persona']['id_per'].'_'.$listaSubfactores['id'] */ ?>
					<?=$this->Form->checkbox('chkNota'.'.'.$listaFunc['Persona']['id_per'].'_'.$perId
													, array('default' => $listaFunc['Persona']['id_per']
                                                    , 'checked'=> $checkedEvLid
                                                    , 'class' => ''
                                                    , 'label' => 'Selección'
                                                    )
											);
					?>
                </td>
            
            </tr> 
            	<? } ?>
            <? } ?>
      
		</table>
        <?=$this->Form->hidden('perId', array('default'=>$perId) );?>
        <? 
		if($cntBtn < 0){ 
			echo $this->Form->end();
		}else{
			echo $this->Form->end('Aceptar');
		}
		?>
        <?=$this->Form->button('A Excel', array('type'=>'button', 'class'=>'button_'
								, 'onClick'=>'aExcelCalificaciones('.$idEscalafon.', '."'".$_SERVER['HTTP_HOST']."'".')') );?>
    </fieldset>
</div>
<? //='notasFuncionarios<pre>'.print_r($notasFuncionarios,1).'</pre>';?>