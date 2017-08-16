<div class="divPrinc" >
    <?
		$valorsPeriodos = array();
		$attributes = array('legend' => false);
	?>
    <nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <br>
    <fieldset>
        <legend class="lbl01" ><?='INFORME DE DESEMPEÑO / Periodo: '.( isset($periodoEvaluados[$elPeriodo]) ? $periodoEvaluados[$elPeriodo] : '' ) ?></legend>
        <h4>
        	SubPeriodo: <?=$this->Form->select('PeriodoEvalua', $periodoEvaluados, $elPeriodo, 
								array('empty' => 'Seleccione...'
								, 'controller' => 'Evaluafuncionarios'
								, 'action' => 'ListaEvaluafuncionarioTodos'
								, 'onChange'=>'location.href = \''.$this->webroot.'Evaluafuncionarios/ListaEvaluafuncionarioTodos/perId:\'+this.value' ) );?>
        </h4>
        <?=count($arrayFuncSeleccionadosPeriodo).' registros.';?>
        <table cellpadding="0" cellspacing="0" border="1" >
         <tr>
            <th>Nombre</th>
            <th>Acción</th>
            <th></th>
         </tr>
         <? if(1): ?>
         <? foreach($arrayFuncSeleccionadosPeriodo as $listaFuncPer){
			 	$IdFunc = $listaFuncPer['funcionario_id'];
		 ?>
         <tr>
            <td><?=utf8_encode($listaFuncPer['Nombre'])?></td>
            <td>
            	<? $nroREspFunc = ( isset($arrayNroRespFunc[$IdFunc]) ? $arrayNroRespFunc[$IdFunc] : 0);
				   $nroREspFuncJust = ( isset($nroRespuestasFuncJustificacion[$IdFunc]) ? $nroRespuestasFuncJustificacion[$IdFunc] : 0);
				?>
				<?=$this->Form->create('Evaluafuncionario', array('url' => array('controller'=>'Evaluafuncionarios', 'action'=>'FactorfuncionarioTodos') ));?>
				<?=$this->Form->hidden('funcionario_id', array('default'=>$IdFunc) );?>
                <?=$this->Form->hidden('elPeriodo', array('default'=>$stringInSubperiodo) );?>
                <?
					$txtIr = 'Ir a Precalificación';
					$claseBoton = 'btnIrEvaluacionVerde';
					$disabled=''; /*** ESTO ES SOLO POR DESARROLLO, DESPUES SE DEJA HABILITADA LA VARIABLE PARA Q HABILITE SOLO CUANDO EL PERIODO ESTA ABIERTO ****/ 
				?>
                <?=$this->Html->link('Hoja de Precalificación', array('controller' => 'Evaluafuncionarios', 'action'=>'precalificacionMantenedor', $IdFunc )
																, array('class' => 'btnIrEvaluacionVerde')  );?>
            </td>
            <td>
	            <?=$this->Form->end(array('label'=>$txtIr, 'div'=>array('class'=>$claseBoton) ));?>
            </td>
         </tr>
         <? } ?>
         <? endif; ?>
        </table>
    </fieldset>
    <nav><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
</div>