<? //='nroReg: '.$nroReg.'<br>'?>
<? //='<pre>'.print_r($this, 1).'</pre>'?>

<?php echo $this->Html->script('mod01', array('inline' => false)); ?>
<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <fieldset>
        <legend class="lbl01" ><?='VALIDAR Y ACEPTAR PRECALIFICACIÓN / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre?></legend>
        <?=$this->Form->create('Validaaceptaevaluac', array('onsubmit'=>'submitAceptaNotificacion();') )?>
        <? $checked =($nroReg>0 ? true : false );
		   $disabled =($nroReg>0 ? true : false ); ?>
        <?=$this->Form->hidden('funcionario_id', array('default'=>$idPer))?>
		<?=$this->Form->hidden('subperiodo_id', array('default'=>$subPerId))?>
        <?=$this->Form->input('texto'
                                    , array('id'=>'nuevoTexto'
									, 'default'=>($datosValidaEvalucion['Validaaceptaevaluac']['texto'])
									, 'cols'=>40, 'rows' =>5
									, 'div' => 'mandatory'
									, 'type'=>'textarea'
									, 'label'=>'Observación'
									, 'class'=>'cssTextarea') );?>
        <?=$this->Form->input('aceptar', array('type'=>'checkbox', 'label'=>'He sido Notificado', 'checked'=> $checked, 'disabled'=>$disabled)	);?>
        <?=$this->Form->input('aceptarR', array('type'=>'checkbox', 'label'=>'He sido Retroalimentado', 'checked'=> $checked, 'disabled'=>$disabled) );?>
        <? if(!$checked){?>
			<? $options = array('id' => 'submit'); ?>
        	<?=$this->Form->submit('Enviar', $options);}?>
        <?=$this->Form->end();?>
	</fieldset>
</div>