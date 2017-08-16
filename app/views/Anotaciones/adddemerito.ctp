<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => 'listaAnotacionDemerito', $idPerFunc) );?></nav>
    <fieldset>
        <legend class="lbl01" ><?='ANOTACIONES de DEMERITO / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre;?></legend>
        <?=$nombreFuncionario?>
        <?=$this->Form->create('Anotacione', array('type' => 'file', 'controller' => 'Anotaciones', 'action' => 'adddemerito') )?>
        <?=$this->Form->hidden('funcionario_id', array('default'=>$this->params['data']['Anotacione']['funcionario_id'] ) );?>
        <table>
        	<tr>
            	<th>Anotación</th>
                <th>Solicitada por</th>
                <th>Firma Jefe Directo</th>
            </tr>
            <tr>
            	<td><?=$this->Form->textarea('anotacion', array('default'=>'', 'cols'=>20, 'rows'=>7, 'style'=>'font-size:12px; resize: none;' ) );?></td>
                <td><?=$this->Form->select('solicita_id', $listaFuncionarios, null , array('empty' => 'Seleccione una persona') );?></td>
                <td><?=$this->Form->checkbox('firma_id', array('default'=>$idJefeDirecto) );?></td>
            </tr>
            <tr>
            	<td colspan="3">
                	<?=$this->Form->input('documento', array('type' => 'file', 'default' => 0) );?>
                </td>
            </tr>
        </table>
        <?=$this->Form->end('Guardar');?>
        
    </fieldset>
</div>
