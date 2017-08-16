<? //='listaFactor:<pre>'.print_r($listaFactor,1).'</pre>';?>
<? //='subPerId:<pre>'.print_r($subPerId,1).'</pre>';?>
<div class="divPrinc" >
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <fieldset>
        <legend class="lbl01" ><?='HOJA DE VIDA / Periodo: '.$perNombre.'<br />Subperiodo: '.$subPerNombre?></legend>
        
        <table>
        	<tr>
            	<td>
                	NOMBRE COMPLETO
                </td>
                <td>
                	<?=$listaHistoria['NOMBRE']?>
                </td>
            </tr>
            <tr>
            	<td>
                	CALIDAD JURIDICA
                </td>
                <td>
                	<?=$listaHistoria['calidadJuridica']?>
                </td>
            </tr>
            <tr>
            	<td>
                	PLANTA
                </td>
                <td>
                	<?=$listaHistoria['FUNCION']?>
                </td>
            </tr>
            <tr>
            	<td>
                	GRADO
                </td>
                <td>
                	<?=$listaHistoria['grado']?>
                </td>
            </tr>
                	<tr>
            	<td>
                	UNIDAD DE DESEMPEÑO
                </td>
                <td>
                	<?=$listaHistoria['grupo']?>
                </td>
            </tr>
            <tr>
            	<td>
                	LUGAR DE DESEMPEÑO
                </td>
                <td>
                	<?=$listaHistoria['lugardesem']?>
                </td>
            </tr>
            <tr>
            	<td>
                	NOMBRE PRECALIFICADOR
                </td>
                <td>
                	<?=$precalificadorNombre?>
                </td>
            </tr>
        </table>
	</fieldset>
</div>            