<?=$this->Html->script('jquery-1.10.2', array('inline' => false)); ?>
<?=$this->Html->script('jquery-ui', array('inline' => false)); ?>
<script>
  $(function(){ $( "#datepicker" ).datepicker(); });
  
  $(function(){
	  $("#msgLugar").change(function(){
		  	$("#msgOtrolugar").val("");
			if($("#msgLugar").val() == 'Otro lugar'){
				$("#msgOtrolugar").css("visibility", "visible");
			}else{
				$("#msgOtrolugar").css("visibility", "hidden");
			}
		} );
  });
  
 $(function() { 
	  $("#EvaluafuncionariosEmailPrecalificaForm").submit(function(event){
		  var fecha = $("#datepicker").val();
		  var lugar = $("#msgLugar").val();
		  var otrolugar = $("#msgOtrolugar").val().trim();
		  var hora = $("#msgHoraHour").val();
		  var minuto = $("#msgMinutoMin").val();
		  if(fecha == 'Seleccione Fecha'){
			alert('Seleccione una fecha.');
		  	event.preventDefault();
		  }
		  if(lugar.length <=0){
			alert('Seleccione un lugar.');
		  	event.preventDefault();
		  }else if(lugar.length ==10){
			if(otrolugar.length <=0 && otrolugar.trim() == ''){
				alert('Ingrese un lugar.');
				event.preventDefault();
		  	}
		  }
		  if(hora.length <=0){
			alert('Seleccione una hora.');
		  	event.preventDefault();
		  }
		  if(minuto.length <=0){
			alert('Seleccione minutos.');
		  	event.preventDefault();
		  }
	  })
 });  
</script>
<?
$optionsStyle=array('div' => false, 'type'=>'submit', 
        'style'=>'background:transparent;border:hidden;color:#003d4c;text-decoration:underline;border-radius:0;cursor:pointer;font-weight:bold;'); 
?>
<? //='subPerId:'.$subPerId?>
<div class="divPrinc" >
	<nav class="flotaDerecha" >
        <?=$this->Form->create('Evaluafuncionario', array('url' => array('controller'=>'Evaluafuncionarios', 'action'=>'Factorfuncionario') ));?>
        <?=$this->Form->input('funcionario_id', array('type'=>'hidden', 'default'=>$elFuncionario['Persona']['ID_PER']) );?>
        <?=$this->Form->input('elPeriodo', array('type'=>'hidden', 'default'=>$subPerId) );?>
        <?=$this->Form->submit('Volver', $optionsStyle );?>
        <?=$this->Form->end();?>
    </nav>
    <fieldset>
        <legend class="lbl01" ><?='CITACION A FUNCIONARIO / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre?></legend>
		<? $optons = array('action'=>'emailPrecalifica') ?>
		<?=$this->Form->create('Evaluafuncionarios');?>
        <?=$this->Form->hidden('msg.nombreF', array('default'=>$elFuncionario['Persona']['NOMBRES'].' '.$elFuncionario['Persona']['AP_PAT'].' '.$elFuncionario['Persona']['AP_MAT']) );?>
        <? $elEmail =  strtolower($elFuncionario['Persona']['EMAIL']); ?>
        <?='Casilla de destino: '.$elEmail?>
        <?=$this->Form->hidden('msg.email', array('default'=>$elEmail) );?>
        <?=$this->Form->input('funcionario_id', array('type'=>'hidden', 'default'=>$elFuncionario['Persona']['ID_PER']) );?>
        <?=$this->Form->input('periodo_id', array('type'=>'hidden', 'default'=>$idPeriodo) );?>
        <?=$this->Form->input('subPeriodo_id', array('type'=>'hidden', 'default'=>$subPerId) );?>
        <table>
        	<tr>
            	<td style="font-size:22px">
	                Estimado(a) <?=$elFuncionario['Persona']['NOMBRES'].' '.$elFuncionario['Persona']['AP_PAT'].' '.$elFuncionario['Persona']['AP_MAT']?><br>
                    <?=$texto?>
                </td>
            </tr>
        	<tr>
            	<td>
                    <? $seleccionado=0;?>
	                <?='Lugar<br> '.$this->Form->select('msg.lugar', $lugarReunion, 'Oficina de Jefatura', array('empty' => 'Seleccione uno') );?>
                    <?=$this->Form->input('msg.otrolugar', array('div'=>false, 'default'=>'', 'label'=>'', 'style'=>'visibility:hidden' ) );?>
                    <div id="divotrolugar" ></div>
                </td>
            </tr>   
        	<tr>
            	<td>
                    <?=$this->Form->input('msg.fecha', array('id'=>'datepicker', 'default'=>"Seleccione Fecha", 'div'=>false, 'label'=>'Fecha'
															, 'style'=>'font-size:16px;width:130px;text-align:center', 'readonly'=>'readonly')
					 );?>
                </td>
            </tr> 
            <tr>
            	<td> Hora<br>
                	<?=$this->Form->hourOffice('msg.hora', true, -1, array('empty' => 'Seleccione Hora') );?>&nbsp;<strong>:</strong>&nbsp;
                    <?=$this->Form->minute('msg.minuto', -1, array('empty' => 'Seleccione Minuto', 'interval' => 30) );?>
                </td>
            </tr>           
        </table>
        <?=$this->Form->end('Enviar citación');?>
    </fieldset>              
</div>
