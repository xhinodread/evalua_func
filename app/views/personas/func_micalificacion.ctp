<div class="divPrinc" >
<?
if( count($calificacionFuncionario) > 0 ){
?>
    <h2><?=$nombreEscalafon;?></h2>
    <fieldset>
        <legend class="lbl01" ><?='CALIFICACIÓN'?></legend>
        <table border="1" cellpadding="0" cellspacing="0" >
            <tr style="border:#000 solid 1px;" >
                <th>FACTOR</th>
                <? foreach($cabezaFactores as $ind => $listaFactores){ ?>
                <th colspan="<?=count($listaFactores);?>" style="text-align:center; border:#000 solid 1px;" >
                        <?=$ind;?>
                </th>
                <? } ?>
            </tr>
            
            <tr>
                <th>SUBFACTOR</th>
            <? foreach($cabezaFactores as $ind => $listaFactores){ ?>
                <? foreach($listaFactores as $listaSubfactores){ ?>
                <td style="text-align:center; " >
                     <?=$listaSubfactores['etiqueta']?>
                </td>
                <? } ?>
            <? } ?>
            </tr>
            
           <tr>
           		<th>NOTA</th>
                 <? foreach($cabezaFactores as $ind => $listaFactores){ ?>
					<? foreach($listaFactores as $listaSubfactores){ ?>
                <td style="text-align:center; " >
                         <?=$notaCalificaciones[$listaSubfactores['id']]?>
                </td>
                    <? } ?>
                <? } ?>
           </tr> 
        </table>
    </fieldset>
<? }else{ ?>
	<div class="alert alert-danger" >SIN INFO</div>
<? } ?>
</div>