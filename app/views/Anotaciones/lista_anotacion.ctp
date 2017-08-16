<? //='listaFactor:<pre>'.print_r($listaFactor,1).'</pre>';?>
<? //='$this->data:<pre>'.print_r($this->data,1).'</pre>';?>
<? //='$this->params:<pre>'.print_r($this->params,1).'</pre>';?>
<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '/') );?></nav>
    <fieldset>
        <legend class="lbl01" ><?='ANOTACIONES de MERITO / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre?></legend>
        <?=$nombreFuncionario?>
        <nav class="flotaDerecha" >
        <?=$this->Form->create('Anotacione', array('type'=>'POST', 'url' => array('action'=> 'add')) );?>
		<?=$this->Form->hidden('funcionario_id', array('default'=>$idPerAnotacion ) )?>
        <?=$this->Form->end('Nueva Anotación');?>
        </nav>
        <? foreach($listaAnotaciones as $lista){ ?>
			<?=$this->Form->create('Anotacione', array('type'=>'POST', 'url' => array('action'=> 'editAnotacion') ) );?>
            <?=$this->Form->input('id', array('default'=>$lista['Anotacione']['id']) );?>
            <?=$this->Form->hidden('funcionario_id', array('default'=>$lista['Anotacione']['funcionario_id']) );?>
            <table style="width:950px;">
        	<tr>
            	<th>Anotación</th>
                <th>Solicitada por</th>
                <th>Firma Jefe Directo</th>
                <? if(0): ?><th>Acción</th><? endif; ?>
            </tr>
            <tr>
                <td>
                	<?=$this->Time->format('d/m/Y h:i', $lista['Anotacione']['created'])?>
                    <? //=$this->Time->nice($lista['Anotacione']['created'])?>
                    <?=$this->Form->textarea('anotacion', array('label'=>'Anotacion'
                                                                , 'rows' => '5'
                                                                , 'cols' => '15'
                                                                , 'default'=>$lista['Anotacione']['anotacion']
                                                                , 'readonly'=>true
																, 'style'=>'font-size:12px; resize: none;') );
                    ?>
                </td>
                <td>
                    <?=$this->Form->input('solicitado', array('default'=>$listaFuncionarios[$lista['Anotacione']['solicita_id']]
															, 'readonly'=>true
															, 'label'=>false
															, 'style'=>'width:500px; font-size:12px;'
															 ) );
					?>
                    <?=$this->Form->hidden('solicita_id', array('default'=>$lista['Anotacione']['solicita_id']) );?>
                    <? //=strlen($lista['Anotacione']['archivo_nombre']);?>                    
                    <? if( strlen($lista['Anotacione']['archivo_nombre']) > 0 ){ 
							echo $this->Html->link('Descargar documento', array('controller' => 'Anotaciones', 'action' => 'documentofichero_bajar'
																				, $lista['Anotacione']['archivo_nombre']
																				, 'deMerito')
																		, array('title' => $lista['Anotacione']['archivo_nombre']) );
							// echo ' ('.$lista['Anotacione']['archivo_nombre'].')';
						}					 
					?>                    
                </td>
                <td>
                	<? $checked = ($lista['Anotacione']['firma_id'] > 0 ? true : false) ?>
                    <?=$this->Form->checkbox('firma_id', array('checked'=>$checked
															, 'onclick' => 'javascript: return false;'
															/*,'style'=>'width:30px; height:30px;'*/) );
					?>
                    <?=$this->Form->end();?>
                </td>
                
                <? if(0): ?><td>
                    <?=$this->Form->end();?>
                </td><? endif; ?>
                
            </table>
        <? } ?>
    </fieldset>
</div>