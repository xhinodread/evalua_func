<? //=print_r($nroPreguntas,1)?>
<? //='<pre>'.print_r($arrayFuncSeleccionadosPeriodo,1).'</pre>'?>
<script>
window.addEventListener("scroll", function(event){
    var top = this.scrollY,
        left = this.scrollX;
	// console.log(top)
	 if(document.getElementById('tblCabezaPendientes')){
		 var elObj = document.getElementById('tblCabezaPendientes');
		 if(top >= 300){
			 if(elObj.style.visibility=="hidden"){
				 elObj.style.visibility="visible";
				 elObj.style.position = "fixed";
				 elObj.style.top = "1px";
				 elObj.style.textAlign="center";
			 }
		 }else{
			 elObj.style.visibility="hidden";
		 }
	 }
}, false);
</script>
<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <br>
    <fieldset>
        <legend class="lbl01" ><?='FUNCIONARIOS PENDIENTES PRECALIFICACIÓN / Periodo: '.$subPerEtiqueta?></legend>
        SubPeriodo:<br>
        <?=$this->Form->select('subperiodo_id', $valorsPeriodos, $elSubPeriodo, 
                                    array('empty' => 'Seleccione SubPeriodo', 'controller' => 'Evaluafuncionarios', 'action' => 'pendientes'
                                        , 'onChange'=>'location.href = \''.$this->webroot.'Evaluafuncionarios/pendientes/subperId:\'+this.value' ) );?>
        <br>
        <table cellpadding="0" cellspacing="0" border="1" id="tblCabezaPendientes" class="estiloCabeceraTabla" >
            <tr>
            	<th style="text-align:center;" width="35" >#</th>
	            <th style="text-align:center;" width="423" >Nombre</th>
                <th style="text-align:center;" width="423" >Preevaluador</th>
                <th style="text-align:center;" width="146" >Estado</th>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" border="1" >
            <tr>
            	<th style="text-align:center;" >#</th>
	            <th style="text-align:center;" >Nombre</th>
                <th style="text-align:center;" >Preevaluador</th>
                <th style="text-align:center;" >Estado</th>
            </tr>
            <? foreach($arrayFuncSeleccionadosPeriodo as $pnt => $listaFuncPer){ ?>
	        <tr>
            	<td style="text-align:center;" ><?=($pnt + 1)?></td>
            	<td><?=$listaFuncPer['funcionario_id'].' '.$listaFuncPer['Nombre']?></td>
                <td><?=$listaFuncPer['precalificadore_id']?></td>
                <? 
					$txt = '(Pendiente)';
					$color = '#FF0000';
					// if($listaFuncPer['respuestas'] > 0 && $listaFuncPer['respuestas'] < $nroPreguntas){
					if($listaFuncPer['respuestas'] > 0 && $listaFuncPer['respuestas'] < $listaFuncPer['preguntas']){
						$color = '#FFFF00';
						$txt = '(Parcial)';
					// }else if($listaFuncPer['respuestas'] > 0 && $listaFuncPer['respuestas'] >= $nroPreguntas){
					}else if($listaFuncPer['respuestas'] > 0 && $listaFuncPer['respuestas'] >= $listaFuncPer['preguntas']){
						$color = '#62af56';
						$txt = '(Finalizada)';
					}
				?>
            	<td style="font-weight:bold; text-align:center; background-color:<?=$color?>;" ><?=$listaFuncPer['respuestas'].'/'.$listaFuncPer['preguntas'].' '.$txt?></td>
            </tr>
            <? } ?>
         
        </table>
        
    </fieldset>
</div>