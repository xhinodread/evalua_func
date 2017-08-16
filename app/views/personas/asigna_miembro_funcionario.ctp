<script>
$(document).ready(function(){	
	clearjQueryCache();
	validaSelectsFirmaMJunta();
});
</script>
<? //='<pre>'.print_r($listamiembrosJuntaTmp, 1).'</pre>';?>
<? //$parametros = array('empty' => ' -- Seleccione -- ', 'onchange' =>'hacerPost(this.value);'); ?>
<? $parametrosSelect = array('empty' => ' -- Seleccione -- ', 'class'=>'styleSelectJunta', 'allowEmpty' => false ); ?>
<? $miembrosJunta = 0; ?>
<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('controller'=>'Personas', 'action' => 'listaMiembroFuncionario') );?></nav>
    <fieldset>
        <legend class="lbl01" ><?='ASIGNAR MIEMBROS AL FUNCIONARIO(A) / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre?></legend>
        <br>
        <?=$this->Form->create(null, array('controller' => 'Personas', 'action' => 'setAsignarMiembrosFuncionario') );?>
        <? //=$this->Form->create();?>
        <table >
          <? $parametros = array('empty' => ' -- Seleccione -- ', 'onclick'=>'return false;' ); ?>
          <tr>
            <td>
				<?=$traePersona['Persona']['NOMBRES'].' '.$traePersona['Persona']['AP_PAT'].' '.$traePersona['Persona']['AP_MAT']?>
            </td>
            <td>
                <?=$this->Form->hidden('funcionario_id', array('default' => $traePersona['Persona']['ID_PER'] ) );?>
            </td>
          </tr>
		  <tr>
          	<td colspan="2"><hr /></td>
          </tr>
          <tr>
          	<td>
            	<strong>Presidente Junta Calificadora</strong> <label class="styleRequired" >*</label><br />
				<?=$this->Form->select('slc_presi', $losMiembrosJunta, $miembrosFirma['FirmasHojacalifica']['slc_presi'], $parametrosSelect);?>
            </td>
          	<td>
            	<strong>Integrante Junta Calificadora</strong> <label class="styleRequired" >*</label><br />
            	<?=$this->Form->select('slc_integrante1', $losMiembrosJunta['Integrante Junta'], $miembrosFirma['FirmasHojacalifica']['slc_integrante1'], $parametrosSelect);?>
            </td>
          </tr>
          <tr>
          	<td>
            	<strong>Integrante Junta Calificadora</strong> <label class="styleRequired" >*</label><br />
				<?=$this->Form->select('slc_integrante2', $losMiembrosJunta['Integrante Junta'], $miembrosFirma['FirmasHojacalifica']['slc_integrante2'], $parametrosSelect);?>
            </td>
          	<td>
            	<strong>Secretario de la Junta Calificadora</strong> <label class="styleRequired" >*</label><br />
            	<?=$this->Form->select('slc_secretario', $losMiembrosJunta['Secretario(a) Junta'], $miembrosFirma['FirmasHojacalifica']['slc_secretario'], $parametrosSelect);?>
            </td>
          </tr>
          <tr>
          	<td>
            	<strong>Integrante Junta Calificadora</strong> <label class="styleRequired" >*</label><br />
				<?=$this->Form->select('slc_integrante3', $losMiembrosJunta['Integrante Junta'], $miembrosFirma['FirmasHojacalifica']['slc_integrante3'], $parametrosSelect);?>
            </td>
          	<td>
            	<strong>Representante del personal</strong> <label class="styleRequired" >*</label><br />
            	<?=$this->Form->select('slc_representante', $losMiembrosJunta['Representante Personal'], $miembrosFirma['FirmasHojacalifica']['slc_representante'], $parametrosSelect);?>
        <div class="fotoFlota" >
            <? if($traePersona['Personaimagen']['FOTO_PER']){ ?>
                    <?='<img src="data:image/jpeg;base64,'.base64_encode( $traePersona['Personaimagen']['FOTO_PER'] ).'"/ width="80"  >';?>
            <? }else{ ?>
                    <?=$this->Html->image('sinFoto.png', array('class'=>'fotoNohay'));?>
            <? } ?>
        </div>
            </td>
          </tr>
		  <tr>
          	<td>
            	<strong>Integrante Junta Calificadora</strong> <label class="styleRequired" >*</label><br />
            	<?=$this->Form->select('slc_integrante4', $losMiembrosJunta['Integrante Junta'], $miembrosFirma['FirmasHojacalifica']['slc_integrante4'], $parametrosSelect);?>
            </td>
          	<td>
            	<strong>Representante Asociación</strong> <label class="styleRequired" >*</label><br />
            	<?=$this->Form->select('slc_asociacion', $losMiembrosJunta['Representante Asociación'], $miembrosFirma['FirmasHojacalifica']['slc_asociacion'], $parametrosSelect);?>
            </td>
          </tr>
        </table>
        <?=$this->Form->hidden('funcionario_id', array('default' => $traePersona['Persona']['ID_PER'] ) );?>
        <? //='idPer: '.$traePersona['Persona']['ID_PER'];?>
		<?=$this->Form->end('Guardar');?>
        <div id="divMsg" class="" style="width:400px;"></div>
	</fieldset>
</div>
<?
unset($traePersona['Personaimagen']);
unset($traePersona['Historia']);
?>
<? // ='traePersona<pre>'.print_r($traePersona, 1).'</pre>';?>
