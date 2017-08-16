<? //='listaFactor:<pre>'.print_r($listaFactor,1).'</pre>';?>
<? //='$this->data:<pre>'.print_r($this->data,1).'</pre>';?>
<? //='$this->params:<pre>'.print_r($this->params,1).'</pre>';?>

<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('controller' => 'Anotaciones', 'action' => 'anotademeritoindex') );?></nav>
    <fieldset>
        <legend class="lbl01" ><?='ANOTACIONES de DEMERITO / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre?></legend>
        <?=$nombreFuncionario?>
        <nav class="flotaDerecha" >
        <?=$this->Form->create('Anotacione', array('type'=>'POST', 'url' => array('action'=> 'adddemerito')) );?>
		<?=$this->Form->hidden('funcionario_id', array('default'=>$idPerAnotacion ) )?>
        <?=$this->Form->end('Nueva Anotación');?>
        </nav>
        <? foreach($listaAnotaciones as $lista){ ?>
			<?=$this->Form->create('Anotacione', array('type'=>'POST', 'url' => array('action'=> 'editAnotacion') ) );?>
            <?=$this->Form->input('id', array('default'=>$lista['Anotademerito']['id']) );?>
            <?=$this->Form->hidden('funcionario_id', array('default'=>$lista['Anotademerito']['funcionario_id']) );?>
            <table style="width:950px;">
        	<tr>
            	<th>Anotación</th>
                <th>Solicitada por</th>
                <th>Firma Jefe Directo</th>
                <? if(0): ?><th>Acción</th><? endif; ?>
            </tr>
            <tr>
                <td>
                	<?=$this->Time->format('d/m/Y h:i', $lista['Anotademerito']['created'])?>
                    <? //=$this->Time->nice($lista['Anotacione']['created'])?>
                    <?=$this->Form->textarea('anotacion', array('label'=>'Anotacion'
                                                                , 'rows' => '5'
                                                                , 'cols' => '15'
                                                                , 'default'=>$lista['Anotademerito']['anotacion']
                                                                , 'readonly'=>true
																, 'style'=>'font-size:12px; resize: none;') );
                    ?>
                </td>
                <td>
                    <?=$this->Form->input('solicitado', array('default'=>$listaFuncionarios[$lista['Anotademerito']['solicita_id']]
															, 'readonly'=>true
															, 'label'=>false
															, 'style'=>'width:500px; font-size:12px;'
															 ) );
					?>
                    <?=$this->Form->hidden('solicita_id', array('default'=>$lista['Anotademerito']['solicita_id']) );?>
                    <? //=strlen($lista['Anotacione']['archivo_nombre']);?>
                    <? if( strlen($lista['Anotademerito']['archivo_nombre']) > 0 ){ 
							echo $this->Html->link('Descargar documento', array('controller' => 'Anotaciones', 'action' => 'documentofichero_bajar'
																				, $lista['Anotademerito']['archivo_nombre']
																				, 'deDemerito')
																		, array('title' => $lista['Anotademerito']['archivo_nombre']) );
						}					 
					?>                    
                </td>
                <td>
                	<? $checked = ($lista['Anotademerito']['firma_id'] > 0 ? true : false) ?>
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