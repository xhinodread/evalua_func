<? //='nroReg: '.$nroReg.'<br>'?>
<? //='<pre>'.print_r($this, 1).'</pre>'?>
<? //='perNombre: '.strlen($perNombre).'<br>'?>
<? //='datosValidaEvalucion: '.strlen(trim($datosValidaEvalucion['Validaaceptaevaluac']['texto'])).'<br>'?>
<?php echo $this->Html->script('mod01', array('inline' => false)); ?>
<div class="divPrinc" id="divContent" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <fieldset>
        <legend class="lbl01" ><?=( $muestraLink == 1 ? "VALIDAR Y ACEPTAR PRECALIFICACIÓN" : "VALIDAR Y ACEPTAR INFORME DE DESEMPEÑO").' / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre?></legend>
        <? // =$this->Form->create('Validaaceptaevaluac', array('onsubmit'=>'submitAceptaNotificacion();') ) /*** ESTA FUNCION DEBE ELIMINARSE EN mod01.js ***/ ?>
        <?=$this->Form->create('Validaaceptaevaluac')?>
        <? $checked =($nroReg>0 ? true : false );
		   $disabled =($nroReg>0 ? true : false ); ?>
        <?=$this->Form->hidden('funcionario_id', array('default'=>$idPer))?>
		<?=$this->Form->hidden('subperiodo_id', array('default'=>$subPerId))?>
        <?=$this->Form->input('texto'
                                    , array('id'=>'nuevoTexto'
									, 'default'=>( strlen(trim($datosValidaEvalucion['Validaaceptaevaluac']['texto'])) <=0 ? 
													'Sin Observación' : $datosValidaEvalucion['Validaaceptaevaluac']['texto'] )
									, 'cols'=>40, 'rows' =>5
									, 'div' => 'mandatory'
									, 'type'=>'textarea'
									, 'label'=>'Observación'
									, 'class'=>'cssTextarea') );?>
        <?=$this->Form->input('aceptar', array('type'=>'checkbox', 'label'=>'He sido Notificado', 'checked'=> $checked, 'disabled'=>$disabled)	);?>
        <?=$this->Form->input('aceptarR', array('type'=>'checkbox', 'label'=>'He sido Retroalimentado', 'checked'=> $checked, 'disabled'=>$disabled) );?>
        <? if( !$checked && strlen($perNombre) > 0){?>
			<? $options = array('id' => 'submit', 'name' => 'submit'
								,'onClick' => "blockBoton(this)"); ?>
        	<?=$this->Form->submit('Enviar', $options);
		}?>
        <?=$this->Form->end();?>
	</fieldset>
</div>
<script>
function blockBoton(elBoton){
	//event.preventDefault();
	$(elBoton).css({"pointer-events": "none", "tab-index": "-1"});
	var template = $( "<div>", {
		id: "flashMessage",
		class: "alert alert-warning", 
		text: "Espere un momento mientras enviamos su mensaje." 			
	});		
	$('#divContent').append(template);
	//console.log('fin');
}
</script>