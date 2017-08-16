<div class="divPrinc" >
    <?
		$valorsPeriodos = array();
		$attributes = array('legend' => false);
		foreach($periodoEvaluados as $listaSubPeriodos)$valorsPeriodos[$listaSubPeriodos['Subperiodo']['id']] = $listaSubPeriodos['Subperiodo']['etiqueta'].
		' / '.date('Y', strtotime($listaSubPeriodos['Subperiodo']['mesdesde']));
	?>
    <nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <br>
    <fieldset>
        <legend class="lbl01" ><?='PRECALIFICACIÓN A FUNCIONARIOS / Periodo: '.$periodoEvaluados[0]['Periodo']['etiqueta']?></legend>
        <h4>
        	SubPeriodo: <?=$this->Form->select('PeriodoEvalua', $valorsPeriodos, $elPeriodo, 
			array('empty' => 'Seleccione...'
			, 'controller' => 'Evaluafuncionarios'
			, 'action' => 'ListaEvaluafuncionario'
			, 'onChange'=>'location.href = \''.$this->webroot.'Evaluafuncionarios/ListaEvaluafuncionario/perId:\'+this.value' ) );?>
        </h4>
        <? //='Nro preguntas:'.$nroPreguntas?>
        <? if( count($arrayFuncSeleccionadosPeriodo) > 0 ){ ?>
	       		Sus funcionarios asignados
        <? } ?>
        <table cellpadding="0" cellspacing="0" border="1" >
         <tr>
            <th>Nombre</th>
            <th>Selección</th>
         </tr>
         <? foreach($arrayFuncSeleccionadosPeriodo as $listaFuncPer){
			 	$IdFunc = $listaFuncPer['funcionario_id'];
		 ?>
         <tr>
            <td><?=$IdFunc.' '.utf8_encode($listaFuncPer['Nombre'])?></td>
            <td>
            	<? //='<pre>'.print_r($arrayNroRespFunc,1).'</pre>';?>
            	<? $nroREspFunc = ($arrayNroRespFunc[$IdFunc] ? $arrayNroRespFunc[$IdFunc] : 0);
				   $nroREspFuncJust = ( isset($nroRespuestasFuncJustificacion[$IdFunc]) ? $nroRespuestasFuncJustificacion[$IdFunc] : 0);
				?>
				<?=$this->Form->create('Evaluafuncionario', array('url' => array('controller'=>'Evaluafuncionarios', 'action'=>'Factorfuncionario') ));?>
				<?=$this->Form->hidden('funcionario_id', array('default'=>$IdFunc) );?>
                <?=$this->Form->hidden('elPeriodo', array('default'=>$elPeriodo) );?>
                <? 
					if($ponenota == 0){
						$nroPreguntasCalculo = $nroPreguntas + $nroPregSubFactTotal ;
						if($arrayNroItemsFunc[$IdFunc] == 3)$nroPreguntasCalculo-=8;
						$totalRespuestas = ($nroREspFunc + $nroREspFuncJust );
					}else{
				
						$nroRespFuncNotasSubFac = ( isset($nroNotaSubFact[$IdFunc]) ? $nroNotaSubFact[$IdFunc] : 0 );
						$nroPregFuncNotasSubFac = ($arrayNroItemsFunc[$IdFunc] == 3 ? $nroPregSubFactTotal-4 : $nroPregSubFactTotal);
						$nroPreguntasCalculo=$nroPreguntas + $nroPregSubFactTotal + $nroPregFuncNotasSubFac;
						if($arrayNroItemsFunc[$IdFunc] == 3)$nroPreguntasCalculo-=8;
						$totalRespuestas = ($nroREspFunc + $nroREspFuncJust + $nroRespFuncNotasSubFac);
					}
					//echo 'vars: totalRespuestas:'.$totalRespuestas.' > 0 && totalRespuestas: '.$totalRespuestas.' < nroPreguntasCalculo: '.$nroPreguntasCalculo.'<hr>';
					$txtIr = 'Ir a Precalificación (Pendiente)';
					$claseBoton = 'btnIrEvaluacionRojo';
					if( $totalRespuestas > 0 && $totalRespuestas < $nroPreguntasCalculo){
						$claseBoton = 'btnIrEvaluacionAmarillo';
						$txtIr = 'Ir a Precalificación (Parcial)';
					}else if( $totalRespuestas > 0 && $totalRespuestas >= $nroPreguntasCalculo){
						$claseBoton = 'btnIrEvaluacionVerde';
						$txtIr = 'Precalificación (Finalizada)';
					}
					$disabled=($varDis == 1 ? true : false);
					//$disabled=''; /*** ESTO ES SOLO POR DESARROLLO, DESPUES SE DEJA HABILITADA LA VARIABLE PARA Q HABILITE SOLO CUANDO EL PERIODO ESTA ABIERTO ****/ 
					?>
				<?=$this->Form->end(array('label'=>$txtIr, 'div'=>array('class'=>$claseBoton), 'disabled'=>$disabled ));?>
            </td>
         </tr>
         <? } ?>
        </table>
    </fieldset>
    <nav><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
</div>