<?
App::import('Vendor', 'phpmailer', array('file' => 'phpmailer'.DS.'PHPMailerAutoload.php'));
class Funcionespropias {

	static function mostrarErrores($arrayErrores){
		$listaErrores = '';
		foreach($arrayErrores as $lista){
			$listaErrores .= '- '.$lista.'<br />';
		}
		return $listaErrores;
	}

	static function sacaExtencionArchivo($nombreArchivo){
		return substr($nombreArchivo, strripos($nombreArchivo, '.')+1);
	}
	
	static function arrayIn($arrayDatos = null, $nodo1, $nodo2){
		$arraySalida = array();
		foreach($arrayDatos as $lista){
			$arraySalida[] = $lista[$nodo1][$nodo2];
		}
		return $arraySalida;
	}

	static function arrayInPuntero($arrayDatos = null, $pnt, $nodo1, $nodo2){
		$arraySalida = array();
		foreach($arrayDatos as $lista){
			$arraySalida[$lista[$nodo1][$pnt]] = $lista[$nodo1][$nodo2];
		}
		return $arraySalida;
	}

	static function arrayInPunteroNombrePersona($arrayDatos = null){
		$arraySalida = array();
		foreach($arrayDatos as $lista){
			$arraySalida[$lista['Persona']['ID_PER']] = utf8_encode($lista['Persona']['NOMBRES']).' '.utf8_encode($lista['Persona']['AP_PAT']).' '.utf8_encode($lista['Persona']['AP_MAT']);
		}
		return $arraySalida;
	}
	
	/*** informeprecalificacion_pdf ***/
	static function obtenerJustificacion($arrayDatos, $idSubFactor){
		$valRescatado=array();
		foreach($arrayDatos as $pnt => $lista){
			if( $idSubFactor == $lista['Justificacionsubperiodo']['subfactore_id'] ){
				$valRescatado[$idSubFactor][][$lista['Justificacionsubperiodo']['subperiodo_id']] = $lista['Justificacionsubperiodo']['texto'];
			}
		}
		return ($valRescatado);
	}
		
	static function listarJustificacion($arrayJustificaciones){
		$subFactorJustifica = array();
		$key=0;
		foreach($arrayJustificaciones as $lista){
			// $subFactorJustifica[] = '- '.$lista;
			//$subFactorJustifica[] = '- '.$lista[41];
			$key = array_keys($lista);
			$subFactorJustifica[$key[0]] = $key[0].' '.$lista[$key[0]];
		}
		return $subFactorJustifica;
	}
	
	static function sacaPromedioFactor($arrayNotas){
		return round($arrayNotas['sumaNota'] / $arrayNotas['nroNotas'], 1);
	}
	
	/********************************/
	/*** hoja_de_calificacion.ctp ***/
	
	static function sumarNotas($arrayNotas){
		$arrayPromedios = array();
		//$laNota = 0;
		foreach($arrayNotas as $pnt => $lista){
			$laNota = 0;
			foreach($lista as $listaNotas){
				if( is_numeric($listaNotas) && $listaNotas != '' ){
					$laNota += $listaNotas;
				}
			}
			$arrayPromedios[$pnt] = array($laNota, count($lista));
		}
		return $arrayPromedios;
	}
	
	static function ponerPromedios($arrayPromedios){
		$promedio = 0;
		foreach($arrayPromedios as $pnt => $lista){
			//echo 'nF_'.$pnt.' <br />';
			$promedio = round(($lista[0] / $lista[1]), 2);
			if($promedio > 0 ){
				echo "<script>var textInput = document.getElementById('nF_".$pnt."');textInput.value = '".$promedio."';</script>";
			}
		}
	}

	static function ponerPuntajes($arrayPromedios, $arrayCoeficientes){
		// echo print_r($arrayCoeficientes,1 ).'<br />';
		$Puntaje = 0;
		$PuntajeFinal = 0;
		foreach($arrayPromedios as $pnt => $lista){
			$promedio = round(($lista[0] / $lista[1]), 2);
			if($promedio > 0 ){
				$Puntaje = round($promedio * ($arrayCoeficientes[$pnt] / 100), 2);
				echo "<script>var textInput = document.getElementById('nPuntaje_".$pnt."'); textInput.value = '".($Puntaje * 10)."';</script>";
				$PuntajeFinal += $Puntaje;
			}
		}
		$PuntajeFinal = round($PuntajeFinal * 10, 2);
		echo "<script>var textInput = document.getElementById('puntajeFinal'); textInput.value = '".$PuntajeFinal."';</script>";
		$listaCalificacion = self::distribucionPuntaje($PuntajeFinal);
		echo "<script>var textInput = document.getElementById('listaCalificacion'); textInput.value = '".$listaCalificacion."';</script>";
	}
	
	static function distribucionPuntaje($valorPuntaje){
		$lista = 4;
		if( $valorPuntaje && is_numeric($valorPuntaje) ){
			if( $valorPuntaje >= 85 && $valorPuntaje <= 100 ){
				$lista = 1;
			}
			if( $valorPuntaje >= 50 && $valorPuntaje < 84.99 ){
				$lista = 2;
			}
			if( $valorPuntaje >= 30 && $valorPuntaje < 49.99 ){
				$lista = 3;
			}
		}
		return $lista;
	}
	
	/************************************/
	/*** hoja_de_calificacion_pdf.ctp ***/	
	
	static function corrigeSaltoLineaFirma($varDireccion, $valorLn = null){
		if($varDireccion == 1){
			if($valorLn)
				return $valorLn;
			else
				return 9;
		}else{
			return 20;
		}
	}

	static function enviaCorreo($body = null, $subject = null, $destino = null ){
		$mail = new PHPMailer(true); 
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "tls";
		$mail->CharSet="UTF-8";
		// Configuración del servidor SMTP
		$mail->Port = 587;
		$mail->Host = "smtp.office365.com";
		$mail->Username = "sgdoc@gorecoquimbo.cl";
		$mail->Password = "prat350+";
		// Configuración cabeceras del mensaje
		$mail->setFrom('sgdoc@gorecoquimbo.cl', 'Administrador');
		$mail->AddAddress($destino);
		// $mail->AddAddress($destino, 'Usted');
		$mail->Subject = $subject;
		$mail->IsHTML(true);
		$mail->Body = nl2br($body);
		
		if( $mail->Send() ){
			return true;
			//$this->Session->setFlash('Email Enviado a '.$destino);
		}else{
			return false;
			//$this->Session->setFlash('1) Ocurrio un error, no pudo enviarse el correo:<pre>'.print_r(error_get_last(), 1).'</pre>2<pre>'.$mail->ErrorInfo.'</pre>');
		}
	}

}
?>