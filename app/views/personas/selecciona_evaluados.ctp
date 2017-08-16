<?php echo $this->Html->script('mod01', array('inline' => false)); ?>
<style>
ol li { list-style-type: lower-alpha; font-weight:bold; }
.centroMedio{ text-align:center; alignment-adjust:middle }
.checklIDER { width:50px; margin: 12px auto; }
/* SQUARED TWO */
.squaredTwo_ {
	width: 28px;
	height: 28px;
	background: #fcfff4;

	background: -webkit-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: -moz-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: -o-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: -ms-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfff4', endColorstr='#b3bead',GradientType=0 );
	margin: 5px auto;

	-webkit-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
	-moz-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
	box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
	position: relative;
	z-index:-1px;
}
.squaredTwo_ input[type=checkbox] {
	width: 28px;
	height: 28px;
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
	filter: alpha(opacity=100);
	opacity: 1;
}
.squaredTwo_ input[type=checkbox]:checked + label:after {
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
	filter: alpha(opacity=100);
	opacity: 1;
}
</style>
<div class="divPrinc" >
	<? //='<pre>'.print_r($listaSelecEvaluados, true).'</pre><hr>';?>
    <? //='<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';?>
    <? //='<br />seleccionado elSubPeriodo:<pre>'.print_r($elPeriodo, true).'</pre><hr>';?>
    <? //='<br />arraFuncActuals:<pre>'.print_r($arraFuncActuals, true).'</pre><hr>';?>
    <?
		$valorsPeriodos = array();
		$attributes = array('legend' => false);
		////foreach($periodoEvaluados as $listaSubPeriodos)$valorsPeriodos[$listaSubPeriodos['Subperiodo']['id']] = $listaSubPeriodos['Periodo']['etiqueta'].' / '.$listaSubPeriodos['Subperiodo']['etiqueta'];
		foreach($periodoEvaluados as $listaSubPeriodos)$valorsPeriodos[$listaSubPeriodos['Subperiodo']['id']] = $listaSubPeriodos['Subperiodo']['etiqueta'].
		' / '.date('Y', strtotime($listaSubPeriodos['Subperiodo']['mesdesde']));
	?>
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <br>
    <fieldset>
        <legend class="lbl01" ><?='ASIGNAR PRECALIFICADORES / Periodo: '.$periodoEvaluados[0]['Periodo']['etiqueta']?></legend>
        <?=$this->Form->create('Persona', array('url' => array('controller' => 'personas', 'action' => 'seleccionaEvaluados')
				,'onClick'=>'submitAsignarPrecalificador();') )?>
        <h4>
        	Subperiodo: <?=$this->Form->select('PeriodoEvalua', $valorsPeriodos, $elSubPeriodo, 
			array('empty' => 'Seleccione uno', 'controller' => 'personas', 'action' => 'seleccionaEvaluados',
			 'onChange'=>'location.href = \''.$this->webroot.'personas/seleccionaEvaluados/perId:\'+this.value' ) );?>
        </h4>
        <? //=$elPeriodo?><br />
        <? //=$nroAsignaciones?><br />
        <? //=count($asignacionSubPeriodoAnterior['evaluaFuncionarios'])?>
        <? if( isset($nroAsignaciones) && $nroAsignaciones == 0 && $elSubPeriodo ){?>
        <div>
        	<? //=count($asignacionSubPeriodoAnterior['evaluaFuncionarios'])?>
            <br />
            <? //='<pre>'.print_r($asignacionSubPeriodoAnterior['evaluaFuncionarios'][0],1).'</pre>'?>
        	Traer Asignacion Periodo Anterior
        </div>
        <? } ?>        
        <table cellpadding="0" cellspacing="0" border="1" >
         <tr>
            <th>Nombre</th>
            <th>Selección</th>
            <th>Evalua Liderazgo</th>
            <th>Precalificalificador</th>
         </tr>
         <? foreach($listaSelecEvaluados as $listaFunc){ ?>
         <tr>
            <td>
            <?=$listaFunc['Persona']['ID_PER']?>
			<?=utf8_encode($listaFunc['Persona']['NOMBRES'].' '.$listaFunc['Persona']['AP_PAT'].' '.$listaFunc['Persona']['AP_MAT'])?></td>
            <td >
            	<div class="squaredTwo">
            	<? 
					$checkedFuncActual = false; if(in_array($listaFunc['Persona']['ID_PER'], $arraFuncActuals)) $checkedFuncActual = true; 
					echo $this->Form->checkbox('chk'.$listaFunc['Persona']['ID_PER'], array('default' => $listaFunc['Persona']['ID_PER']
												, 'checked'=> $checkedFuncActual
												, 'class' => 'squaredTwo'
												, 'label' => 'Selección'
												, 'onclick'=> 'habilitaCheck(this)'
												, 'disabled' => (isset($nroAsignaciones)? 'false' : 'true' ) )
											 );
				?>
                </div>
            </td>
            <td style=" border:0 auto;">
                <div class="checklIDER">
                <?
                    //echo '* '.$checkedFuncActual.'<br />';
                    $checkedEvLid = false;
                    if(in_array($listaFunc['Persona']['ID_PER'], $arraFuncActuals)){
                        foreach($listaFunc['Evaluafuncionario'] as $listaEvaluFunc){
                            //echo 'listaEvaluFunc: <pre>'.print_r($listaEvaluFunc, true).'</pre><hr>';
							//echo '>'.$listaEvaluFunc['subperiodo_id'].' == '.$elPeriodo.' && '.$listaEvaluFunc['factore_id'].'<br />';
                            if($listaEvaluFunc['subperiodo_id'] == $elSubPeriodo && $listaEvaluFunc['factore_id'] == 1 ){
                                $checkedEvLid = true;
                            }
                        }
                    }
                    $varDisabled='true';if($checkedFuncActual)$varDisabled='false';
                    echo $this->Form->checkbox('chklid'.$listaFunc['Persona']['ID_PER'], array('default' => $listaFunc['Persona']['ID_PER']
                                                    , 'checked'=> $checkedEvLid
                                                    , 'class' => ''
                                                    , 'label' => 'Selección'
                                                    , 'disabled' => $varDisabled )
                    );
                ?>
                </div>
            </td>
            <td>
                <?
					$valPrecalif=0;
					foreach($listaFunc['Evaluafuncionario'] as $lista){
						if($lista['subperiodo_id'] == $elSubPeriodo){
							$valPrecalif=$lista['precalificadore_id'];
						}
					}
				?>
				<?=$this->Form->select('precalif.'.$listaFunc['Persona']['ID_PER'], $listaPrecalificadores, $valPrecalif, 
											array('empty' => 'Seleccione Precalificador'
												, 'disabled' => $varDisabled) );
				?>
                <? //=$listaFunc['Persona']['ID_PER']?>
                <? if(!$valPrecalif){ ?>
                    <br />
                    <strong>Observación:
                    <? if( isset($listaFuncSinPrecConObs[$listaFunc['Persona']['ID_PER']]) ){ ?>
                            <?=$listaFuncSinPrecConObs[$listaFunc['Persona']['ID_PER']];?>
                    <? }else{ ?>
                            Sin observación
                    <? } ?>
                <? } ?>
                </strong>
            </td>
         </tr>
         <? } ?>
        </table>
        <?=$this->Form->end('Guardar Cambios')?>
    </fieldset>
    <nav><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
</div>