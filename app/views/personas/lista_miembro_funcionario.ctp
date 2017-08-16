<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('controller'=>'Personas', 'action' => 'miembrosJuntacalificadora') );?></nav>
    <fieldset>
        <legend class="lbl01" ><?='ASIGNAR MIEMBROS AL FUNCIONARIO(A) / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre;?></legend>
        <br>
        <table >
          <? foreach($listaFuncionarios as $lista){ ?>
          <tr>
            <td>
				<?=utf8_encode($lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT']);?>
            </td>
            <td>
            	&nbsp;<?=$this->Html->link('Asignar pie de firma', array('controller'=>'Personas', 'action' => 'asignaMiembroFuncionario', $lista['Persona']['ID_PER']) );?>
                -
                &nbsp;<?=$this->Html->link('Hoja de Calificación', array('controller' => 'Evaluafuncionarios', 'action'=>'hojaDeCalificacion', $lista['Persona']['ID_PER'] ) );?>

            </td>
          </tr>
          <? } ?>
        </table>
	</fieldset>
</div>
<? //='<pre>'.print_r($listamiembrosJunta, 1).'</pre>';?>
