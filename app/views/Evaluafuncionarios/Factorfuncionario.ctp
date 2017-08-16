<div class="divPrinc" >
	<? //='<pre>'.print_r($this->params, true).'</pre>';?> 
	<? //='<pre>'.print_r($evaluacionFuncionario, true).'</pre>';?> 
    <? //='<pre>'.print_r($listaFactores, true).'</pre>';?> 
	<? //='<pre>'.print_r($elFuncionario, true).'</pre>';?>
    <? //='<pre>'.print_r($idPeriodo, true).'</pre>';?>
    <? //='ponenota: '.$ponenota?>
    <? //=$idSubPeriodo?>
    <? 
		$elPeriodo =$this->params['data']['Evaluafuncionario']['elPeriodo'];
		if(!$elPeriodo)$elPeriodo =$this->params['named']['elPeriodo'];
	 ?>
    <nav class="flotaDerecha" >
	<?=$this->Html->link('Volver', 
							array('action' => 'ListaEvaluafuncionario',
								  'perId'=>$elPeriodo) );?>
	</nav>
    <br>
    <fieldset class="cssFieldset" >
        <legend class="lbl01" ><?='PRECALIFICACIÓN AL PERSONAL'?></legend>
        <table class="tablaPerTramo" >
        	<tr>
            	<th>PERIODO</th>
                <td>:</td>
                <td><?=$nomPeriodo?></td>
                <td rowspan="4">
                    <? //='<img src="data:image/jpeg;base64,'.base64_encode( $elFuncionario['Personaimagen']['FOTO_PER'] ).'"/ width="130"  >';?>
                </td>
            </tr>
            <tr>
            	<th>TRAMO</th>
                <td>:</td>
                <td><?=$nomSubPeriodo?></td>
            </tr>
            <tr>
            	<td colspan="3">
                	<h3><?=utf8_encode($elFuncionario['Persona']['NOMBRES'].' '.$elFuncionario['Persona']['AP_PAT'].' '.$elFuncionario['Persona']['AP_MAT'])?></h3>
                </td>
           </tr>
           <tr>
           		<td colspan="3">
                	<? if(0): ?>
                	<? $optons = array( 'action'=>'pdf', 'target'=> '_blank' ) ?>
                    <?=$this->Form->create('Evaluafuncionarios', $optons);?>
                    <?=$this->Form->input('funcionario_id', array('type'=>'hidden', 'default'=>$elFuncionario['Persona']['ID_PER']) );?>
                    <?=$this->Form->input('periodo_id', array('type'=>'hidden', 'default'=>$idPeriodo) );?>
                    <?=$this->Form->end('Precalificación');?>
                  <? endif; ?>
                    <!---------------------------------------------------------------------->
                    <? if($ponenota > 0){ ?>
						<?=$this->Form->create('Evaluafuncionarios', array('action'=>'informeprecalificacionPdf', 'target'=>'_blank'));?>
                        <?=$this->Form->hidden('funcionario_id', array('default'=>$elFuncionario['Persona']['ID_PER']) );?>
                        <?=$this->Form->hidden('periodo_id', array('default'=>$idPeriodo) );?>
                        <?=$this->Form->input('subPeriodo_id', array('type'=>'hidden', 'default'=>$idSubPeriodo) );?>
                        <? $options=array('type'=>'image', 'style'=>'width:50px; height:60px;','div' =>false, 'title'=>'Informe de Precalificación');?>
                        <?=$this->Form->submit('/img/bajarPdfButton.jpg', $options);?>
                        <?=$this->Form->end();?>
                    <? } ?>
        			<!---------------------------------------------------------------------->
				  <? if(0): ?>
                    <? $optons = array( 'action'=>'informeprecalificacion', 'target'=> '_blank' ) ?>
                    <?=$this->Form->create('Evaluafuncionarios', $optons);?>
                    <?=$this->Form->input('funcionario_id', array('type'=>'hidden', 'default'=>$elFuncionario['Persona']['ID_PER']) );?>
                    <?=$this->Form->input('periodo_id', array('type'=>'hidden', 'default'=>$idPeriodo) );?>
                    <?=$this->Form->input('id', array('type'=>'text', 'default'=>$idSubPeriodo) );?>
                    <?=$this->Form->end('Informe de Precalificación');?>
                    <? //=$this->Html->link('Precalificación', array('controller' => 'Evaluafuncionarios', 'action'=>'precalificacion'), array('class'=>'primera') );?>
                  <? endif; ?>
                    <!---------------------------------------------------------------------->
                    <? $optons = array( 'action'=>'emailPrecalifica') ?>
                    <?=$this->Form->create('Evaluafuncionarios', $optons);?>
                    <?=$this->Form->input('funcionario_id', array('type'=>'hidden', 'default'=>$elFuncionario['Persona']['ID_PER']) );?>
                    <? //=$this->Form->input('periodo_id', array('type'=>'hidden', 'default'=>$idPeriodo) );?>
                    <?=$this->Form->input('periodo_id', array('type'=>'hidden', 'default'=>$idPeriodo) );?>
                    <?=$this->Form->input('subPeriodo_id', array('type'=>'hidden', 'default'=>$idSubPeriodo) );?>
                    <?=$this->Form->end('Citar a Retroalimentación');?>
                    <br />
                    <? //=$elFuncionario['Persona']['RUT'];?>
                    <? //=$nomSubPeriodo?>
                    <? 
						$mesesAsistencia = explode('-', $nomSubPeriodo);
						//echo print_r($mesesAsistencia, 1);
						$arrayMesesNro = Configure::read('arrayMesesLetraNro');
						//echo '<br />desde:'.$arrayMesesNro[$mesesAsistencia[0]].' hasta:'.$arrayMesesNro[$mesesAsistencia[1]];
						//echo '<br /><br />';
						//echo print_r($arrayMesesNro, 1);
						$mesIni = 'mes_ini='.$arrayMesesNro[$mesesAsistencia[0]];
						$mesFin = 'mes_fin='.$arrayMesesNro[$mesesAsistencia[1]];
						
						/**************/
						//echo ''.$nomPeriodo.'<br />';
						$mesesAsistencia = explode('-', $nomPeriodo);
						//echo ''.print_r($mesesAsistencia, 1).'<br />';
						$arrayFechaIni = explode(' ', trim($mesesAsistencia[0]));
						$arrayFechaFin = explode(' ', trim($mesesAsistencia[1]));
						//echo 'arrayFechaIni'.print_r($arrayFechaIni, 1).'<br />';
						//echo 'arrayFechaFin'.print_r($arrayFechaFin, 1).'<br />';
						
						$stringUrl = 'rut='.$elFuncionario['Persona']['RUT']
									.'&mes_ini='.$arrayMesesNro[$arrayFechaIni[0]]
									.'&anio_ini='.$arrayFechaIni[1]
									.'&mes_fin='.$arrayMesesNro[$arrayFechaFin[0]]
									.'&anio_fin='.$arrayFechaFin[1];
						// echo 'stringUrl: '.$stringUrl.'<br />'
					?>
                    <a href="http://192.168.200.190:8080/Proy_Upersonas/EvaluaFuncionarioAsistenciaPdf.php?<?=$stringUrl?>" target="_blank">Ver Asistencia</a>
                </td>
           </tr>
        </table>
        <div class="fotoFlota" >
			<? if($elFuncionario['Personaimagen']['FOTO_PER']){ ?>
                    <?='<img src="data:image/jpeg;base64,'.base64_encode( $elFuncionario['Personaimagen']['FOTO_PER'] ).'"/ width="130"  >';?>
            <? }else{ ?>
                    <? //='<img src="'.BASE_PATH.'sinFoto.png" width="130"  >';?>
                    <?=$this->Html->image('sinFoto.png', array('class'=>'fotoNohay'));?>
            <? } ?>
        </div>
        <?if(0):?><h4><?='PERIODO: '.$nomPeriodo.'<br>TRAMO: '.$nomSubPeriodo?></h4><?endif;?>
        <table class="tablaFactor" >
             <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Accion</th>
             </tr>
             <? foreach($evaluacionFuncionario as $valor){ ?>
             <tr>
                <td><?=$valor['Factor']['etiqueta']?></td>
                <td><?=$valor['Factor']['descripcion']?></td>
                <td>
                	<? 
						//echo 'elPeriodo-id: '.$elPeriodo.'<br>';
						//echo 'Persona-id: '.$elFuncionario['Persona']['ID_PER'].'<br>';
						///echo 'Factor-id: '.$valor['Factor']['id'].'<hr>';
						//echo '-> '.$arrayNroRespFuncJust[$valor['Factor']['id']].'<br />';
						$factorId = $valor['Factor']['id'];
						//echo 'factorId: '.$factorId.'<br>';
						
						$nroPregSubFactFunc = ($nroPregSubFact[$factorId] ? $nroPregSubFact[$factorId] : 0);
						$nroRespSubfacFunc = ($arrayNroRespSubfacFunc[$factorId] ? $arrayNroRespSubfacFunc[$factorId] : 0);
						//if($ponenota == 1){
							//echo 'nroPregSubFact: '.$nroPregSubFactFunc.'<br>';
							//echo 'arrayNroRespSubfacFunc: '.$nroRespSubfacFunc.'<hr>';
						//}
						$nroRespFactJust = ( isset($arrayNroRespFuncJust[$factorId]) ? $arrayNroRespFuncJust[$factorId] : 0);
						$nroRespFact = ( isset($arrayNroRespFactorFunc[$factorId]) ? $arrayNroRespFactorFunc[$factorId] : 0);
						///echo 'nroResp: '.$nroRespFact.'<br>';
						///echo 'nroRespFactJust: '.$nroRespFactJust.'<br>';
						$nroPreg = $nroPreguntasFactor[$factorId];
						$nroPregJust = $nroPregSubFact[$factorId];
                        ///echo 'nroPreg: '.$nroPreg.'<br />';
						///echo 'nroPregJust: '.$nroPregJust.'<br />';
						//echo 'nroRespFact: '.$nroRespFact.' + nroRespFactJust: '.$nroRespFactJust.'<br />';
						//echo 'nroPreg: '.$nroPreg.' + nroPregJust: '.$nroPregJust.'<br />';
						
						if($ponenota == 1){
							$textSubmit = ( (($nroRespFact + $nroRespFactJust + $nroRespSubfacFunc) / ($nroPreg + $nroPregJust + $nroPregSubFactFunc)) == 1 ?
										 'Finalizado' : "Precalificar (".($nroRespFact + $nroRespFactJust + $nroRespSubfacFunc).'/'.($nroPreg + $nroPregJust + $nroPregSubFactFunc).") " );
						}else{
							$textSubmit = ( (($nroRespFact + $nroRespFactJust) / ($nroPreg + $nroPregJust)) == 1 ? 
										'Finalizado' : "Precalificar (".($nroRespFact + $nroRespFactJust).'/'.($nroPreg + $nroPregJust).") " );
						}
					?>
					<? //=$this->Html->link('Evaluar', array('action' => 'Evaluacionfuncionario', $valor['Factor']['id']) ); ?>
                    <?=$this->Form->create('Evaluar', array('url' => array('controller'=>'Evaluafuncionarios', 'action'=>'Evaluacionfuncionario') ));?>
					<?=$this->Form->hidden('funcionario_id', array('default'=>$elFuncionario['Persona']['ID_PER']) );?>
                    <?=$this->Form->hidden('elPeriodo', array('default'=>$elPeriodo) );?>
                    <?=$this->Form->hidden('elFactor', array('default'=>$valor['Factor']['id']) );?>
                    <?=$this->Form->end($textSubmit);?>
                </td>
             </tr>
             <? } ?>
        </table>
    </fieldset>
    <nav>
	<?=$this->Html->link('Volver', array('action' => 'ListaEvaluafuncionario', 'perId'=>$elPeriodo) );?>
	</nav>
</div>