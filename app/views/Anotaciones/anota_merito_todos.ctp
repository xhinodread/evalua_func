<? //='listaFactor:<pre>'.print_r($listaFactor,1).'</pre>';?>
<? //='subPerId:<pre>'.print_r($subPerId,1).'</pre>';?>
<style>
.sinBordes{
	margin-top: 0em;
	margin-bottom: 0em;
	padding: 0em;
	/*'div' => array('class' => 'sinBordes')*/
}
.sinBordes input[type=submit] {
    background: #62af56;
    background: -webkit-gradient(linear, left top, left bottom, from(#a8ea9c), to(#62af56));
    background-image: -moz-linear-gradient(top, #a8ea9c, #62af56);
    border-color: #2d6324;
    color: #000;
    text-shadow: #8cee7c 0px 1px 0px;
	cursor: pointer;
}
/*.sinBordes:hover{ cursor:hand; }*/


</style>
<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('controller' => 'pages', 'action' => 'home') );?></nav>
    <fieldset>
        <legend class="lbl01" ><?='ANOTACIONES de MERITO/ Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre?></legend>
        <? if( count($listaSelecEvaluados) > 0 ){ ?>
            <table cellpadding="0" cellspacing="0" border="1" >
             <tr>
                <th>Nombre</th>
                <th>Acción</th>
             </tr>
             <? foreach($listaSelecEvaluados as $listaFunc){ ?>
             <tr>
                <td><?=utf8_encode($listaFunc['Persona']['NOMBRES'].' '.$listaFunc['Persona']['AP_PAT'].' '.$listaFunc['Persona']['AP_MAT']);?></td>
                <td >
                    <?=$this->Form->create('Anotacione', array('action'=> 'listaAnotacion') );?>
                    <?=$this->Form->hidden('id_per', array('default'=>$listaFunc['Persona']['ID_PER']) );?>
                    <? $options =  array('label' => 'Anotaciones', 'div' => array('class' => 'sinBordes'));?>
                    <?=$this->Form->end($options);?>
                </td>
             </tr>
             <? } ?>
            </table>
        <? }else{ echo '<h3>Sin Anotaciones</h3>'; } ?>
    </fieldset>
</div>