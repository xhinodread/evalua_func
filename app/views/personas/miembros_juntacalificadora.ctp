<script>
$(document).ready(function() {	
	validaSelects();
	$( "select" ).change(function () {
		validaSelects();
	});
});
</script>
<? //$parametros = array('empty' => ' -- Seleccione -- ', 'onchange' =>'hacerPost(this.value);'); ?>
<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <fieldset>
        <legend class="lbl01" ><?='MIEMBROS JUNTA CALIFICADORA / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre?></legend>
        <? if(0): ?>
        	<nav class="flotaDerechaX" ><?=$this->Html->link('Asignar Miembros a Funcionarios', array('action' => 'listaMiembroFuncionario') );?></nav>
		<? endif; ?>
        <br>
        <?=$this->Form->create();?>
        <table >
          <? $parametros = array('empty' => ' -- Seleccione -- ', 'onclick'=>'return false;' ); ?>
          <? foreach($listaFuncionariosCalificadores as $lista){ ?>
          <tr>
            <td>
				<?=$lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT']?>
            </td>
            <td>
            	<? $idTipoIntegrante =( isset($listamiembrosJunta[$lista['Calificadore']['funcionario_id']]) ?
											 $listamiembrosJunta[$lista['Calificadore']['funcionario_id']] : '' );
				?>
				<?=$this->Form->select('slc_comision.'.$lista['Calificadore']['funcionario_id'], $miembrosJunta, $idTipoIntegrante, $parametros);?>
            </td>
            <td>&nbsp;</td>
          </tr>
          <? } ?>
        </table>
		<?=$this->Form->end('Guardar');?>
        <div id="divMsg" class="" style="width:400px;"></div>
	</fieldset>
</div>
<? //='<pre>'.print_r($listamiembrosJunta, 1).'</pre>';?>
