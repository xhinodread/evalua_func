/*** EXPLICACIONES ***
+ if(0){} - Cierra un segmento de codigo permitieno al interprete ignorar el fragmento de codigo dentro de este IF.

*********************/

function clearjQueryCache(){
    for (var x in jQuery.cache){
        delete jQuery.cache[x];
    }
}

function habilitaCheck(objeto){
 try{
	 var checkEvLider = document.getElementById('PersonaChklid'+objeto.value);
	 var selectPrecalif = document.getElementById('precalif'+objeto.value);
	 if(objeto.checked){ 
	 	checkEvLider.disabled=false;
		selectPrecalif.disabled=false; selectPrecalif.selectedIndex =1; 
	 }else{ 
	 	checkEvLider.disabled=true; checkEvLider.checked=false; 
		selectPrecalif.selectedIndex =0; selectPrecalif.disabled=true;
	 }
 }catch(e){ alert('habilitaCheck - error\n'+e.toString()); }
}

function habilitaCheckFuncionariosAsignados(objeto){
 try{
	 var checkEvLider = document.getElementById('chklIdOut'+objeto.value);
	 if(objeto.checked){ 
	 	checkEvLider.disabled=false;
	 }else{ 
	 	checkEvLider.disabled=true; checkEvLider.checked=false; 
	 }
 }catch(e){ alert('habilitaCheckFuncionariosAsignados - error\n'+e.toString()); }
}

function evalNotaCalificacion(objeto){
 try{
	 if((objeto.value) > 10){
		alert('La nota debe ser menor o igual que 10'); 
		objeto.value=objeto.title;
		objeto.focus();
	 }
 }catch(e){ alert('evalNotaCalificacion - error\n'+e.toString()); }
}

function verFoto(objeto){	
 try{
	 var objeto = document.getElementById(objeto);
	 if( objeto.style.visibility=='hidden' ){
	 	objeto.style.visibility='visible';
	 }else{
		objeto.style.visibility='hidden'; 
	 }
 }catch(e){ alert('verFoto - error\n'+e.toString()); }
}

function verTr(idObjeto){
 try{
	 var objeto = document.getElementById(idObjeto);
	 var botn = document.getElementById('btnNueva');
	 
	 if( objeto.style.visibility=='hidden' ){
	 	objeto.style.visibility='visible';
		botn.innerHTML='Ocultar Observación';
	 }else{
		objeto.style.visibility='hidden'; 
		botn.innerHTML='Nueva Observación';
	 }
 }catch(e){ alert('verTr - error\n'+e.toString()); }
}

function seguroNotificacion(obj){
if(0){
 try{
	if(obj.checked == true)alert('Recuerde que una vez enviada la notificacion no puede volver atras.');
 }catch(e){ alert('seguroGral - error\n'+e.toString()); }
}
}

function submitAceptaNotificacion(){
 try{
	var validaAceptaEvaluacAceptar = document.getElementById('ValidaaceptaevaluacAceptar').checked;
	var validaAceptaEvaluacAceptarR = document.getElementById('ValidaaceptaevaluacAceptarR').checked;
	
	if(validaAceptaEvaluacAceptar && validaAceptaEvaluacAceptarR){
		return true;
	}else{
		alert('Debe aceptar la Notificación');
		event.preventDefault(); 
		return false;
	}
 }catch(e){ alert('submitAceptaNotificacion - error\n'+e.toString()); }
}

function submitAsignarPrecalificador(){
 try{
	if((event.target).nodeName == 'INPUT' && ( (event.target).type == 'submit' || (event.target).type == 'checkbox') ){
		var subPeriodo = document.getElementById('PersonaPeriodoEvalua');
		if(subPeriodo.value > 0){
			return true;
		}else{
			alert('Debe seleccionar un Subperiodo');
			event.preventDefault(); 
			return false;
		}
	}	
 }catch(e){ alert('submitAsignarPrecalificador - error\n'+e.toString()); }
}

/***************** miembros_juntacalificadora.ctp *****************/
function validaSelects(){
 try{
	//console.clear();
	var selects = $("select");
	var nroSelects = selects.length;
	var cntSelects = 0;
	selects.each(function(index){
	  $(this).each(function(datoSelect){
		  cntSelects = cntSelects + evaluaSeleccion($(this).val());
	  });  
	});
	submitAndMensaje(nroSelects, cntSelects);
 }catch(e){
	 alert('validaSelects() - Error: \n'+e.toString() );
 }
}
function evaluaSeleccion(valorEvaluar){
	if( valorEvaluar ){
		if( valorEvaluar > 0 ) return 1;
	}
	return 0;
}
function submitAndMensaje(nroSelects, cntSelects){
	var btnSubmit = $("input:submit");
	var elDiv = $("#divMsg");
	btnSubmit.prop('disabled', true);
	elDiv.addClass("notice");
	elDiv.text("Hay Calificadores sin asignar...");
	if( nroSelects == cntSelects ){
		btnSubmit.prop('disabled', false);
		elDiv.removeClass("notice");
		elDiv.text("");
	}
}

/***************** asigna_miembro_funcionario.ctp *****************/
function validaSelectsFirmaMJunta(){
 try{
	var selects = $("select");
	selects.each(function(index){
	  var obj = $(this)
	  var objId = obj.attr("id");
	  var objValor = obj.val();
	  selects.each(function(index2){
		  var objIdCompara = $(this).attr("id");
		  var objValorCompara = $(this).val();
		  if( objId != objIdCompara ){
			  if( objValor == objValorCompara ){
				 $(this).css("background-color", "yellow");
				 $(this).css("font-size", "14px");
			  }
		  }
	  });
	});
 }catch(e){
	 alert('validaSelectsFirmaMJunta() - Error: \n'+e.toString() );
 }
}

/***************** calificacion.ctp *****************/

function aExcelCalificaciones(idEscalafon, server){
 try{
	// alert('aExcelCalificaciones');
	var caracteristicas = "height=700,width=800,scrollTo,resizable=1,scrollbars=1,location=0";
	var url = 'http://'+server+'/evaluafunc/Evaluafuncionarios/calificacionExcel/'+idEscalafon
	
	nueva=window.open(url, 'Popup', caracteristicas);
	// nueva=window.open('http://192.168.200.113:8080/evaluafunc/Evaluafuncionarios/calificacionExcel/'+idEscalafon, 'Popup', caracteristicas);
	
	return false;
 }catch(e){
	 alert('aExcelCalificaciones() - Error: \n'+e.toString() );
 }
}