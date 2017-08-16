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
/*
.squaredTwo label {
	cursor: pointer;
	position: absolute;
	width: 20px;
	height: 20px;
	left: 4px;
	top: 4px;

	-webkit-box-shadow: inset 0px 1px 1px rgba(0,0,0,0.5), 0px 1px 0px rgba(255,255,255,1);
	-moz-box-shadow: inset 0px 1px 1px rgba(0,0,0,0.5), 0px 1px 0px rgba(255,255,255,1);
	box-shadow: inset 0px 1px 1px rgba(0,0,0,0.5), 0px 1px 0px rgba(255,255,255,1);

	background: -webkit-linear-gradient(top, #222 0%, #45484d 100%);
	background: -moz-linear-gradient(top, #222 0%, #45484d 100%);
	background: -o-linear-gradient(top, #222 0%, #45484d 100%);
	background: -ms-linear-gradient(top, #222 0%, #45484d 100%);
	background: linear-gradient(top, #222 0%, #45484d 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#222', endColorstr='#45484d',GradientType=0 );
}

.squaredTwo label:after {
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
	filter: alpha(opacity=0);
	opacity: 0;
	content: '';
	position: absolute;
	width: 9px;
	height: 5px;
	background: transparent;
	top: 4px;
	left: 4px;
	border: 3px solid #fcfff4;
	border-top: none;
	border-right: none;

	-webkit-transform: rotate(-45deg);
	-moz-transform: rotate(-45deg);
	-o-transform: rotate(-45deg);
	-ms-transform: rotate(-45deg);
	transform: rotate(-45deg);
}

.squaredTwo label:hover::after {
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=30)";
	filter: alpha(opacity=30);
	opacity: 0.3;
}
*/
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

<div class="divPrinc">
	<?=$this->Form->create('Persona', array('url' => array('controller' => 'personas', 'action' => 'funcionariosAsignados')) )?>
    <a href="#abajo">Bajar</a><br>
    <fieldset>
        <legend class="lbl01" ><?='FUNCIONARIOS ASIGNADOS POR PRECALIFICADOR'?></legend>
        <table border="0">
            <tr>
                <td>
                    SubPeriodo:<br>
                    <?=$this->Form->select('subperiodo_id', $valorsPeriodos, $elSubPeriodo, 
                                                array('empty' => 'Seleccione SubPeriodo', 'controller' => 'personas', 'action' => 'funcionariosAsignados'
                                                    , 'onChange'=>'location.href = \''.$this->webroot.'personas/funcionariosAsignados/subperId:\'+this.value' ) );?>
                    <br><br>
                    Precalificadores:<br>
                    <? $varDisabled='true';if($elSubPeriodo)$varDisabled='false';?>
                    <?=$this->Form->select('precalificadore_id', $listaPrecalificadores, $elPrecalificador, 
                                                array('empty' => 'Seleccione Precalificador', 'controller' => 'personas', 'action' => 'funcionariosAsignados'
                                                    , 'onChange'=>'location.href = \''.$this->webroot.'personas/funcionariosAsignados/subperId:'
                                                        .$elSubPeriodo.'/precalifId:\'+this.value'
                                                        
                                                    , 'disabled' => $varDisabled) );?>
                </td>
            </tr>
            <tr>
                <td>
                
                    <table>
                        <tr>
                            <th><center>ASIGNADOS</center></th>
                            <th><center> NO ASIGNADOS</center></th>
                        </tr>
                        <tr>
                            <td>
                            
                                <table>
                                    <tr>
                                        <th>Vinculado</th>
                                        <th>Funcionario</th>
                                    </tr>
                                    <? foreach($listaPreEvaluadosActuales as $pnt => $lista){ ?>
                                    <tr>
                                        <td>
                                            <div class="squaredTwo">
                                            <?=$this->Form->checkbox('chkIn.'.$pnt, array('default' => $pnt
                                                                            , 'checked'=> 'True'
                                                                            , 'class' => 'squaredTwo'
                                                                            , 'label' => 'Selección'
                                                                         /*   , 'onclick'=> 'habilitaCheck(this)' */
                                                                          /*  , 'disabled' => (isset($nroAsignaciones)? 'false' : 'true' )*/ )
                                                                         );?>
                                            </div>
                                       </td>
                                       <td>
                                            <?=$pnt;?>&nbsp;
                                            <?=utf8_encode($lista);?>
                                        </td>
                                    </tr>
                                    <? } ?>
                                </table>
                                
                            </td>
                            <td>
                            
                                <table>
                                    <tr>
                                        <th>Selección</th>
                                        <th>Evalua Liderazgo</th>
                                        <th>Funcionario</th>
                                    </tr>
                                     <? foreach($listaPersonas as $pnt => $lista){ 
                                            if( !array_key_exists($pnt, $listaPreEvaluadosActuales) && !array_key_exists($pnt, $listaPreEvaluadosActualesOtrosPrecal) ){?>
                                    <tr>
                                        <td>
                                            <div class="squaredTwo">
                                                <?=$this->Form->checkbox('chkOut.'.$pnt, array('default' => $pnt
                                                                             /*   , 'checked'=> 'False' */
                                                                                , 'class' => 'squaredTwo'
                                                                                , 'label' => 'Selección'
                                                                               , 'onclick'=> 'habilitaCheckFuncionariosAsignados(this)' 
                                                                              /*  , 'disabled' => (isset($nroAsignaciones)? 'false' : 'true' )*/ )
                                                     );?>
                                            </div>
                                        </td>
                                        <td style=" border:0 auto;">
                                            <div class="checklIDER">
                                                <? 
                                                    $checkedFuncActual = false; // TEMPORAL, ESTA VARIABLE DEBE SER DINAMICA ****/
                                                    $varDisabled='true';
                                                    if($checkedFuncActual)$varDisabled='false';?>
                                                <?=$this->Form->checkbox('chklIdOut.'.$pnt, array('default' => $pnt
                                                                              /*      , 'checked'=> $checkedEvLid  */
                                                                                    , 'class' => ''
                                                                                    , 'label' => 'Selección'
                                                                                    , 'disabled' => $varDisabled  )
                                                    );?>
                                            </div>
                                        </td>
                                        <td>
                                            <?=$pnt;?>&nbsp;
                                            <?=utf8_encode($lista);?>
                                            <strong><br />Observación:
                                            <? if( isset($listaFuncSinPrecConObs[$pnt]) ){ ?>
													<?=$listaFuncSinPrecConObs[$pnt];?>
                                            <? }else{ ?>
                                            		Sin observación
                                            <? } ?>
                                            </strong>
                                        </td>
                                    </tr>
                                    <? 		} ?>
                                    <? } ?>
                                </table>
                                
                            </td>
                        </tr>
                   </table>
                   
               </td>
            </tr>
        </table>
        <?=$this->Form->end('Guardar Cambios')?>
	</fieldset>
</div>
