<div>
	<? 
		$cntArray = count($laListaDos);
		$txtSubmit1= "Ver Calificación";
	
	?>
	<table width="0" border="0">
      <tr>
        <th>N</th>
        <th>Periodo</th>        
        <th>Subperiodo</th>
        <th>Precalificador</th>
      </tr>
      <?
	  $primeraKey = array_keys($laListaDos);
	  $auxPer = $primeraKey[0];
	  $cnt=1;
	   foreach($laListaDos as $lista){ 
			if( $auxPer != $lista[0] && $auxPer > 0 ){
			?>
			  <tr >
				 <td colspan="5" style="text-align:right;" >
						<?=$this->Form->create('Personas', array('action'=>'funcMicalificacion') );?>
							<?=$this->Form->input('per_id', array('type'=>'hidden', 'value' => $auxPer ) );?>
							<?=$this->Form->input('func_id', array('type'=>'hidden', 'value' => $idPer));?>
							<?=$this->Form->submit($txtSubmit1);?>
						<?=$this->Form->end();?>
				</td>
			  </tr>
		<?
			}
			$auxPer = $lista[0];
	   ?>
		  <tr>
			<td><?=($cnt)?></td>
			<td><?=$losPeriodos[$lista[0]]?></td>
			<td><?=$lista[1]?></td>
			<td><?=$losPreevaluadores[$lista[2]]?><? //=$lista[0];?></td>
		  </tr>
		  <? 
		  $cnt++;
	  } 
	  if( $cnt >= count($laListaDos) ){ ?>
          <tr>
             <td colspan="4" style="text-align:right;" >
                    <?=$this->Form->create('Personas', array('action'=>'funcMicalificacion') );?>
                        <?=$this->Form->input('per_id', array('type'=>'hidden', 'value' => $auxPer ) );?>
                        <?=$this->Form->input('func_id', array('type'=>'hidden', 'value' => $idPer));?>
                        <?=$this->Form->submit($txtSubmit1);?>
                    <?=$this->Form->end();?>
            </td>
          </tr>     
      <? } ?>
    </table>
</div>
<script>
if(0){
$( document ).ready(function() {
  $('form').on('submit', function(e){
	  e.preventDefault();
	  //console.log( $('input[type="submit"]') );
	  /*
	  alert('Modulo aun no Diponible.\nDisculpe.');
	  var losSubmit = $('input[type="submit"]');
	  losSubmit.remove();
	  */	  
  });
});
}
</script>