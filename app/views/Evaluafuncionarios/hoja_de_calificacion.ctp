<? //='listaJustFuncTmp:<pre>'.print_r($listaJustFuncTmp,1).'</pre>';?>
<?
$Funcionespropias = new Funcionespropias();
$arrayNotas = array();
$arrayPromedios = array();
?>
<style>
.cellTextoMedioIzq{
	padding-left:5em;
}
.cellTextoMedio{
	text-align:center;
}
.cellTextoMedio td{
	text-align:center;
}
</style>
<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('controller' => 'Personas', 'action' => 'listaMiembroFuncionario') );?></nav>
    <fieldset>
    Mantenedor
        <legend class="lbl01" ><?='HOJA DE CALIFICACIÓN / Periodo: '.$periodoNombre.'<br />Subperiodo: '.$subPeriodoNombre?></legend>
        <h1><?=utf8_encode($datosUserFuncionario['Persona']['NOMBRES'].' '.$datosUserFuncionario['Persona']['AP_PAT'].' '.$datosUserFuncionario['Persona']['AP_MAT']);?></h1>        <?=$this->Html->link('Hoja De Calificacion Pdf', array('action' => 'hojaDeCalificacionPdf', $datosUserFuncionario['Persona']['ID_PER'])
													   , array('target' => '_blank') );?>
        <table>
        	<tr>
            	<td style="border-bottom:#000 solid 2px;"></td>
                <td style="text-align:center; font-weight:bold; border-bottom:#000 solid 2px; " >Nota SubFactor</td>
                <td style="text-align:center; font-weight:bold; border-bottom:#000 solid 2px; " >Nota Factor</td>
                <td style="text-align:center; font-weight:bold; border-bottom:#000 solid 2px; " >% Coef.</td>
                <td style="text-align:center; font-weight:bold; border-bottom:#000 solid 2px; " >Puntaje</td>
            </tr>
            
        <? foreach($listaFactor as $listado){ ?>
        	<tr>
            	<th><?=$listado['Factor']['etiqueta']?></th>
                <th></th>
                <td class="cellTextoMedio" >
					<?=$this->Form->input('nF_'.$listado['Factor']['id'], array('div'=>false
																				, 'label'=>false
																				, 'readonly'=>true
																				, 'default'=>''
																				, 'class'=>'txtCentro')
										);
					?>
				</td>
                <td class="cellTextoMedio" >
					<?=$this->Form->input('nCoef_'.$listado['Factor']['id'], array('div'=>false
																				, 'label'=>false
																				, 'readonly'=>true
																				, 'default'=> $listaCoeficientes[$listado['Factor']['id']]
																				, 'class'=>'txtCentro')
										);
					?>
				</td>
                <td class="cellTextoMedio" >
					<?=$this->Form->input('nPuntaje_'.$listado['Factor']['id'], array('div'=>false
																				, 'label'=>false
																				, 'readonly'=>true
																				, 'default'=>''
																				, 'class'=>'txtPuntaje')
										);
					?>
				</td>            </tr>
			<? $subFactorJustifica = array(); ?>
            <? foreach($listado['Subfactor'] as $listadoSub){ ?>
        	<tr>
            	<td><?=$listadoSub['etiqueta']?></td>
                <td colspan="4" class="cellTextoMedioIzq" >
					<?=$this->Form->input('nSf_'.$listadoSub['id'], array('div'=>false
																		, 'label'=>false
																		, 'readonly'=>true
																		, 'default'=>$lstNotas[$listadoSub['id']]
																		, 'class'=>'txtCentro')
										);
						$arrayNotas[$listado['Factor']['id']][] = $lstNotas[$listadoSub['id']]; 
					?>
                </td>
                
            </tr>
            <? } ?>
            <tr>
            	<td colspan="5" style="border-bottom:#000 solid 2px;"></td>
            </tr>
        <? } ?> 
            <tr>
            	<td ></td>
                <td style="text-align:right; " ><strong>Puntaje Final</strong></td>
                <td></td>
                <td></td>
                <td style="text-align:center; " >
					<?=$this->Form->input('puntajeFinal', array('div'=>false
																	, 'label'=>false
																	, 'readonly'=>true
																	, 'default'=> 0
																	, 'class'=>'txtPuntaje')
						);
					?>
                </td>
            </tr>
            <tr>
            	<td style="text-align:right; " ><strong>Lista de calificación</strong></td>
                <td style="text-align:center; ">
					<?=$this->Form->input('listaCalificacion', array('div'=>false
																				, 'label'=>false
																				, 'readonly'=>true
																				, 'default'=> 0 /* $distribucionPuntaje / * $lstNotas[$listadoSub['id']] */
																				, 'class'=>'txtCentro')
						);
					?>
                 </td>
                <td ></td>
                <td ></td>
                <td ></td>
            </tr>

        </table>
	    <!---*************************************************-->    
        <table width="0%" border="0" class="cellTextoMedio" >
          <tr>
            <td>
            	<?=$nombreFirmantes[$datosUserFuncionario['FirmasHojacalifica']['slc_presi']];?>
	            <br />
            	Presidente Junta Calificadora
            </td>
            <td>
            	<?=$nombreFirmantes[$datosUserFuncionario['FirmasHojacalifica']['slc_integrante1']];?>
	            <br />
            	Integrante Junta Calificadora
            </td>
            <td>
            	<?=$nombreFirmantes[$datosUserFuncionario['FirmasHojacalifica']['slc_integrante2']];?>
	            <br />
            	Integrante Junta Calificadora</td>
          </tr>
          <tr>
            <td>
            	<?=$nombreFirmantes[$datosUserFuncionario['FirmasHojacalifica']['slc_integrante3']];?>
	            <br />
            	Integrante Junta Calificadora</td>
            <td>
            	<?=$nombreFirmantes[$datosUserFuncionario['FirmasHojacalifica']['slc_integrante4']];?>
	            <br />
            	Integrante Junta Calificadora</td>
            <td>
            	<?=$nombreFirmantes[$datosUserFuncionario['FirmasHojacalifica']['slc_representante']];?>
	            <br />
            	Representante del Personal</td>
          </tr>
          <tr>
            <td>
            	<?=$nombreFirmantes[$datosUserFuncionario['FirmasHojacalifica']['slc_secretario']];?>
	            <br />
            	Secretario(a) Junta Calificadora
            </td>
            <td>
            	<?=$nombreFirmantes[$datosUserFuncionario['FirmasHojacalifica']['slc_asociacion']];?>
	            <br />
            	Representante Asociación
            </td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
    </fieldset>
</div>
<?
$arrayPromedios = $Funcionespropias->sumarNotas($arrayNotas); 
$Funcionespropias->ponerPromedios($arrayPromedios); 
$Funcionespropias->ponerPuntajes($arrayPromedios, $listaCoeficientes);
?>
