<?php echo $this->Html->script('mod01', array('inline' => false)); ?>
<style>
.labelSinObs{
	background-color:#FC0;
	text-align:center;
	margin: 0 auto;
	font-size:20px;
	width:200px;
}
.labelSinObs:hover{	cursor:pointer; }

textarea{
	font-size:20px;
	width:460px; 
	height:60px;
}
</style>
<script>
  $(function(){
	$('label').dblclick(function () {
		var $input = $(this);
		var value = $input.attr('value');
		/****console.log($input.parent());
		console.log($input);
		console.log(value);****/		
		var newTextArea = document.createElement('textarea')
		newTextArea.name = 'data[observacion]['+value+']';
		newTextArea.id = 'observacion'+value;
		newTextArea.cols = '30';
		newTextArea.rows = '2';
		$(this).parent().append(newTextArea);
		$(this).remove();		
	});
  });
</script>
<div class="divPrinc">
	<?=$this->Form->create('funcionariosSinprecalificador', array('url' => array('controller' => 'personas', 'action' => 'funcionariosSinprecalificador')) )?>
    <a href="#abajo">Bajar</a><br>
    <fieldset>
        <legend class="lbl01" ><?='FUNCIONARIOS SIN PRECALIFICADOR'?></legend>
        <table border="0">
            <tr>
                <td>
                    SubPeriodo:<br>
                    <?=$this->Form->select('subperiodo_id', $valorsPeriodos, $elSubPeriodo, 
                                                array('empty' => 'Seleccione SubPeriodo', 'controller' => 'personas', 'action' => 'funcionariosSinprecalificador'
                                                    , 'onChange'=>'location.href = \''.$this->webroot.'personas/funcionariosSinprecalificador/subperId:\'+this.value' ) );?>
                    <br>
                </td>
            </tr>
            <tr>

            <tr>
                <th><center>SIN PRECALIFICADOR</center></th>
            </tr>
            <tr>
                <td>
                    <table>
                        <tr>
                            <th>Funcionario</th>
                            <th>Observación</th>
                            <th>Acción</th>
                        </tr>
                         <? foreach($listaPersonas as $pnt => $lista){ 
                                if( !array_key_exists($pnt, $listaPreEvaluadosSolos) ){?>
                        <tr>
                            <td>
                                <?=$pnt;?>&nbsp;
                                <?=utf8_encode($lista);?>
                                <?=$this->Form->input('funcionario_id.'.$pnt, array('type' => 'hidden', 'id' => $pnt, 'default' => $pnt) );?>
                            </td>
                            <td style=" border:0 auto;">
                            	<? if( isset($listaFuncSinPrecConObs[$pnt]) ){ ?>
                            	<?=$this->Form->input('observacion.'.$pnt, array('type' => 'textarea'
															, 'label' => false
                                                            , 'default' => (isset($listaFuncSinPrecConObs[$pnt]) ? $listaFuncSinPrecConObs[$pnt] : '') ) );?>
                            	<? }else{ ?>
                                	<? //=$this->Form->input('observacion.'.$pnt, array('type' => 'text', 'default' => '') );?>
                              <label name="observacion.<?=$pnt?>" value="<?=$pnt?>" title="2 click parfa agregar observación" class="labelSinObs" >Sin observación</label>
                                <? } ?>
                          </td>
                            <td>
                                <?=$this->Form->submit('Guardar')?>
                            </td>
                        </tr>
                        <? 		} ?>
                        <? } ?>
                    </table>
                </td>
            </tr>
        </table>
        <?=$this->Form->end()?>
	</fieldset>
</div>
