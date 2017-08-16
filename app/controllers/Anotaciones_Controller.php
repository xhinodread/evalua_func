<?
App::import('Vendor','Funcionespropias');
class AnotacionesController extends AppController{
	public $uses = array('Anotacione', 'Calificadore', 'Persona', 'Historia', 'Periodo', 'Subperiodo', 'Anotademerito', 'Evaluafuncionario');
	//public $uses = '';
	var $helpers = array('Html', 'Form', 'Time');
	var $componets = array('Session', 'Auth');
	/////var $scaffold;
	
	public function index(){
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => /*array(' getdate() BETWEEN Subperiodo.mesdesde AND Subperiodo.meshasta')*/
					array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(d, 20, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
				
		endif;
		//echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId = $periodoEvaluados['Periodo']['id'];
		$perNombre = $periodoEvaluados['Periodo']['etiqueta'];
		$subPerId = $periodoEvaluados['Subperiodo']['id'];
		$subPerNombre = $periodoEvaluados['Subperiodo']['etiqueta'];
		
		$datosSession = $this->viewVars;
		//echo '<br />datosSession:<pre>'.print_r($datosSession,1).'</pre>';
		$idEvaluador = $datosSession['idPer'];
		if( $idEvaluador == 444 ){
			// $idEvaluador = 693;
			// $idEvaluador = 22;
			 $idEvaluador = 79;
		}
		
		$options = array('conditions' => array('Evaluafuncionario.precalificadore_id = '.$idEvaluador, 'Evaluafuncionario.subperiodo_id = '.$subPerId)
						, 'fields' => 'DISTINCT Evaluafuncionario.funcionario_id' );
		//$cuentaAsignados = $this->Evaluafuncionario->find('count', $options);
		if( $this->Evaluafuncionario->find('count', $options) <= 0 ){
			$this->Session->setFlash('No existen funcionarios asociados para este precalificador...');
		}
		//echo '<br />cuentaAsignados:<pre>'.print_r($cuentaAsignados,1).'</pre>';
		$listaAsignados = $this->Evaluafuncionario->find('all', $options);
		//echo '<br />listaAsignados:<pre>'.print_r($options,1).'</pre>';
		$arrayFuncionarios = array();
		foreach($listaAsignados as $lista){
			$arrayFuncionarios[] = $lista['Evaluafuncionario']['funcionario_id'];
		}
		//echo '<br />arrayFuncionarios:<pre>'.print_r($arrayFuncionarios,1).'</pre>';
		$inFuncionarios = implode(',', $arrayFuncionarios);
		
		$listaSelecEvaluados = array();
		if( $this->Evaluafuncionario->find('count', $options) > 0){
			//$this->Session->setFlash('No existen funcionarios asociados a este preevaluador..');
			$this->Persona->recursive = 1;
			$listaSelecEvaluados = $this->Persona->find('all', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')'
																						 , 'PersonaEstado.per_estado = 1'
																					 , 'Persona.ID_PER IN ('.$inFuncionarios.')' )
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields' => array('Persona.*')
																) );
		}
		//echo $subPerId.'<br />listaSelecEvaluados:<pre>'.print_r($listaSelecEvaluados,1).'</pre>';
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'listaSelecEvaluados') );
	}
	
	public function documentofichero_bajar($nombreFichero = null, $tipoAnotacion = null){
		$this->view = 'Media';
		$Funcionespropias = new Funcionespropias();
		$rutaArchivo = "files/".$tipoAnotacion."/";
		$params = array(
		   'extension' => $Funcionespropias->sacaExtencionArchivo($nombreFichero),
		   'id' => $nombreFichero,
		   'name' => str_replace('.pdf', '', $nombreFichero),
		   'download' => true,
   		  /* 'extension' => $Funcionespropias->sacaExtencionArchivo($nombreFichero),*/
		  /* 'mimeType' => array('xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), */
		   'path' => $rutaArchivo
		);
		header("Content-Disposition: attachment; filename=".$nombreFichero );
		//header("Content-Transfer-Encoding: binary");
		$this->set($params);
	}	
	
	function add(){
		/******************************/
		/***** SECCION QUE GUARDA *****/
		/******************************/
		if( !empty($this->data) && count($this->data['Anotacione']) > 2){
			$this->Session->setFlash('data :<pre>'.print_r($this->data,1).'</pre>'.($this->data['Anotacione']['documento']['tmp_name']));
			
			$Funcionespropias = new Funcionespropias();
			
			$msgDemeritoAdd = 'ANOTACIONES de MERITO guardada.';
			if( isset($this->data['Anotacione']['documento']['error']) && $this->data['Anotacione']['documento']['error'] == 0 ){
				$nombreArchivo = 'anotaMerito_'.$this->data['Anotacione']['funcionario_id'].'_'
								.$this->data['Anotacione']['solicita_id'].'_'.date('dmY').'_'.rand(1,900000).'.'
								.$Funcionespropias->sacaExtencionArchivo($this->data['Anotacione']['documento']['name'] );
				$nombreRutaArchivo = WWW_ROOT."files".DS."deMerito".DS.$nombreArchivo;
				
				if(move_uploaded_file($this->data['Anotacione']['documento']['tmp_name'], $nombreRutaArchivo)){
					$this->data['Anotacione']['archivo_nombre'] = $nombreArchivo;
					$msgDemeritoAdd = 'ANOTACIONES de MERITO Subida y guardada.';
				}
			}
			
			unset( $this->data['Anotacione']['documento'] );
			
			if($this->Anotacione->save($this->data)){
				$this->Session->setFlash($msgDemeritoAdd);
				$this->redirect( array('action' => 'listaAnotacion', $this->data['Anotacione']['funcionario_id']) );
			}else{
				$errores = $Funcionespropias->mostrarErrores($this->Anotacione->invalidFields());
				$msgDemeritoAdd = 'Ocurrio un error y no se pudo guardar.<br />'.$errores;
			}
			$this->Session->setFlash($msgDemeritoAdd);
		}
		/******************************/
		/*** FIN SECCION QUE GUARDA ***/
		/******************************/
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => /*array(' getdate() BETWEEN Subperiodo.mesdesde AND Subperiodo.meshasta')*/
					array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(d, 1, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		
		// echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		$this->Persona->recursive=0;
		$listaFuncionarios0 = $this->Persona->find('all', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')', 'PersonaEstado.per_estado = 1')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																) );
		//echo 'listaFuncionarios0:<pre>'.print_r($listaFuncionarios0,1).'</pre>';
		$listaFuncionarios=array();
		foreach($listaFuncionarios0 as $lista){
			$listaFuncionarios[$lista['Persona']['id_per']] = utf8_encode($lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT']);
		}
		//echo 'listaFuncionarios:<pre>'.print_r($listaFuncionarios,1).'</pre>';

		$idPerFunc = $this->data['Anotacione']['funcionario_id'];
		/*** SOLO PARA DESARROLLO ***/
		//$idPerFunc =676;
		//if($idPer == 444)$idPerFunc=676;
		/*** $listaHistoria = $this->Historia->traeFuncionariosHistoria($idPerFunc); ***/
		$listaHistoria = $this->Historia->traeFuncionariosHistoria($idPerFunc);
		$listaHistoria = $listaHistoria[0][0];
		$idJefeDirecto = $listaHistoria['idJefeDirecto'];
		//echo 'listaHistoria:<pre>'.print_r($listaHistoria,1).'</pre>';
		//echo 'data:<pre>'.print_r($this->data,1).'</pre>';
		
		$this->Persona->recursive=0;
		$datosFuncionario = $this->Persona->find('first', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')'
																						, 'PersonaEstado.per_estado = 1'
																						, 'Persona.id_per = '.$idPerFunc.' ')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																) );
		$nombreFuncionario = utf8_encode($datosFuncionario['Persona']['NOMBRES'].' '.$datosFuncionario['Persona']['AP_PAT']
										.' '.$datosFuncionario['Persona']['AP_MAT'] );
		
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'listaFuncionarios', 'idJefeDirecto', 'idPerFunc', 'nombreFuncionario') );
	}
	
	public function documento_subir() {
		if ($this->request->is('post')) {
			if( $this->data['Pages']['file']['error'] == 4 ){
				$this->Session->setFlash('No existe fichero');
				$this->redirect('documento_subir');
			}
			$varSetFlash = 'request<pre>'.print_r($this->request->data,1).'</pre>';
			$nombreArchivo = $this->data['Pages']['file']['name'];
			$docuSize = $this->data['Pages']['file']['size'];
			$docuTipo = $this->data['Pages']['file']['type'];
			// $nombreRutaArchivo = WWW_ROOT.DS."files/".$nombreArchivo;
			$nombreRutaArchivo = WWW_ROOT.DS."files".DS.$nombreArchivo;
			
			/* copiamos el archivo*/
			if(0):
				$this->Session->setFlash($varSetFlash);
			else:
				if(move_uploaded_file($this->data['Pages']['file']['tmp_name'], $nombreRutaArchivo)){
					$archivo = file_get_contents($nombreRutaArchivo);			
					$filenameComprimido = gzencode($archivo, 9);
					//$fp = fopen(WWW_ROOT.DS.'files'.DS.'testArchivo2.gz', "w");
					$fp = fopen(WWW_ROOT.DS.'files'.DS.$nombreArchivo.'.gz', "w");
					fwrite($fp, $filenameComprimido);
					/*************************************************************************************
					$data = fread($fp, filesize(WWW_ROOT.DS.'files'.DS.$nombreArchivo.'.gz'));  
					***** SUPUESTAMENTE ESTA LINEA CARGA EN MEMORIA EL ARCHIVO A GUARDAR EN LA TABLA *****
					*************************************************************************************/
					fclose($fp);
					/* ELIMINA EL BINARIO NO COMPRIMIDO */
					unlink($nombreRutaArchivo);
					
					/************/
					//leer el archivo temporal en binario
					$archivoComprimido = WWW_ROOT.'files'.DS.$nombreArchivo.'.gz';
					$fp     = fopen(WWW_ROOT.DS.'files'.DS.$nombreArchivo.'.gz', 'r+b');
					$data = fread($fp, filesize(WWW_ROOT.DS.'files'.DS.$nombreArchivo.'.gz'));
					$docGzSize = filesize(WWW_ROOT.DS.'files'.DS.$nombreArchivo.'.gz');
					fclose($fp);
					//escapar los caracteres
					//// $data = mysql_escape_string($data);
					// $data = addslashes($data);  OTRA ALTERNATIVA 
					/************/
					//$arrayDocuProy = array('DocumentoProyecto');
					$arrayDocuProy['DocumentoProyecto']['proy_id'] = 1;
					$arrayDocuProy['DocumentoProyecto']['dopro_documento'] = $data; // "'".$data."'";
					$arrayDocuProy['DocumentoProyecto']['dopr_nombre'] = $nombreArchivo.'.gz';
					$arrayDocuProy['DocumentoProyecto']['dopr_tamano'] = $docGzSize;
					$arrayDocuProy['DocumentoProyecto']['dopr_tipo'] = 'application/gzip';
					$varSetFlash = $nombreRutaArchivo; //''; 'arrayDocuProy<pre>'.print_r($arrayDocuProy,1).'</pre>';
					if( $this->DocumentoProyecto->save($arrayDocuProy) ){
						/* ELIMINA EL BINARIO COMPRIMIDO */
						unlink($archivoComprimido);
						$this->Session->setFlash('Archivo subido satisfactoriamente y guardado.<br />'.$archivoComprimido);
					}else{			
						/* mensaje al usaurio */
						$this->Session->setFlash('Archivo subido satisfactoriamente.<br />'.$varSetFlash);
					}
				}else{
					/* mensaje al usaurio */
					$this->Session->setFlash('Error al subir el archivo, verificar.<br />'.$varSetFlash);
				}
			endif;
		}
	}	
	
	function listaAnotacion($idPerAnotacion=null){
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => /*array(' getdate() BETWEEN Subperiodo.mesdesde AND Subperiodo.meshasta')*/
					array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(d, 1, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		
		// echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId = $periodoEvaluados['Periodo']['id'];
		$perNombre = $periodoEvaluados['Periodo']['etiqueta'];
		$subPerId = $periodoEvaluados['Subperiodo']['id'];
		$subPerNombre = $periodoEvaluados['Subperiodo']['etiqueta'];
		
		$datosSession = $this->Session->read('personaDatos');
		$idPer = $datosSession['PersonaEstado']['id_per'];
		$rutPer = $datosSession['PersonaEstado']['per_rut'];
		////echo 'datosSession:<pre>'.print_r($datosSession,1).'</pre>';
		
		$this->Persona->recursive=1;
		$listaFuncionarios0 = $this->Persona->find('all', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')', 'PersonaEstado.per_estado = 1')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																)
													);
		//echo 'listaFuncionarios0:<pre>'.print_r($listaFuncionarios0,1).'</pre>';
		$listaFuncionarios=array();
		foreach($listaFuncionarios0 as $lista){
			$listaFuncionarios[$lista['Persona']['id_per']] =utf8_encode($lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT']);
		}
		if(is_null($idPerAnotacion)){
			$idPerAnotacion=$this->data['Anotacione']['id_per'];
		}
		//$this->Session->setFlash('idPerAnotacion1: '.$idPerAnotacion);
		/*** SOLO PARA DESARROLLO ***/
		//$idPer =166;
		$condiciones= array('conditions'=>'Anotacione.funcionario_id = '.$idPerAnotacion, 'order' => 'Anotacione.created'  );
		$listaAnotaciones = $this->Anotacione->find('all', $condiciones);
		//echo 'listaAnotaciones<pre>'.print_r($listaAnotaciones, 1).'</pre><hr>';
		
		//$this->params['data']['Anotacione']['id_per']
		
		$this->Persona->recursive=0;
		$datosFuncionario = $this->Persona->find('first', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')'
																						, 'PersonaEstado.per_estado = 1'
																						, 'Persona.id_per = '.$idPerAnotacion.' ')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																)
													);
		//echo 'datosFuncionario:<pre>'.print_r($datosFuncionario,1).'</pre>';
		$nombreFuncionario = utf8_encode($datosFuncionario['Persona']['NOMBRES'].' '.$datosFuncionario['Persona']['AP_PAT'].' '.$datosFuncionario['Persona']['AP_MAT']);
		
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'listaAnotaciones', 'listaFuncionarios', 'idPerAnotacion', 'nombreFuncionario'));
	}
	
	function editAnotacion($id=null){
		//echo 'id: '.!is_null($id).'<br />';
		//echo 'this<pre>'.print_r($this->data, 1).'</pre><hr>';
		/******************************/
		/***** SECCION QUE GUARDA *****/
		/******************************/
		if(!is_null($id)){
			$this->Session->setFlash('Sin cambios');
			if($this->Anotacione->save($this->data)){
				$this->Session->setFlash('Grabado');
				////$this->redirect( array('action' => 'listaAnotacion', $id) );
			}
		}
		/******************************/
		/*** FIN SECCION QUE GUARDA ***/
		/******************************/
		
		if(0): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(d, 20, Subperiodo.mesevalhasta)')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		//echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		$this->Persona->recursive=1;
		$listaFuncionarios0 = $this->Persona->find('all', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')', 'PersonaEstado.per_estado = 1')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																)
													);
		///echo 'listaFuncionarios0:<pre>'.print_r($listaFuncionarios0,1).'</pre>';
		$listaFuncionarios=array();
		foreach($listaFuncionarios0 as $lista){
			$listaFuncionarios[$lista['Persona']['id_per']] =utf8_encode($lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT']);
		}
		//echo 'listaFuncionarios:<pre>'.print_r($listaFuncionarios,1).'</pre>';
		
		$idPerAnotacion=$this->data['Anotacione']['funcionario_id'];
		//echo 'idPerAnotacion:<pre>'.print_r($idPerAnotacion,1).'</pre>';
		
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'listaFuncionarios', 'idPerAnotacion'));
	}
	
	function view(){}
	
	function misAnotaciones(){
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => /*array(' getdate() BETWEEN Subperiodo.mesdesde AND Subperiodo.meshasta')*/
					array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(m, 2, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		//echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		$current_user = $this->Auth->user();
		$condiciones = array('PersonaEstado.usuario' => $current_user['User']['username']);
		$datosSession = $this->PersonaEstado->find('first', array('conditions'=> $condiciones ) );
		//$datosSession = $this->Session->read('personaDatos');
		$idPer = $datosSession['PersonaEstado']['id_per'];
		$rutPer = $datosSession['PersonaEstado']['per_rut'];
		//echo 'datosSession:<pre>'.print_r($datosSession,1).'</pre>';
		
		$this->Persona->recursive=1;
		$listaFuncionarios0 = $this->Persona->find('all', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')', 'PersonaEstado.per_estado = 1')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																)
													);
		//echo 'listaFuncionarios0:<pre>'.print_r($listaFuncionarios0,1).'</pre>';
		$listaFuncionarios=array();
		foreach($listaFuncionarios0 as $lista){
			$listaFuncionarios[$lista['Persona']['id_per']] =utf8_encode($lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT']);
		}
		/*** SOLO PARA DESARROLLO ***/
		//$idPer =166;
		//$this->Session->setFlash('idPer: '.$idPer);
		$condiciones= array('conditions'=>'Anotacione.funcionario_id = '.$idPer.' '  );
		$listaAnotaciones = $this->Anotacione->find('all', $condiciones);
		//echo 'listaAnotaciones<pre>'.print_r($listaAnotaciones, 1).'</pre><hr>';
		
		//$this->params['data']['Anotacione']['id_per']
		
		$this->Persona->recursive=0;
		$datosFuncionario = $this->Persona->find('first', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')'
																						, 'PersonaEstado.per_estado = 1'
																						, 'Persona.id_per = '.$idPer.' ')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																)
													);
		//echo 'datosFuncionario:<pre>'.print_r($datosFuncionario,1).'</pre>';
		$nombreFuncionario = utf8_encode($datosFuncionario['Persona']['NOMBRES'].' '.$datosFuncionario['Persona']['AP_PAT'].' '.$datosFuncionario['Persona']['AP_MAT']);
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'listaAnotaciones', 'listaFuncionarios', 'idPerAnotacion', 'nombreFuncionario'));
	}
	
	public function anotaMeritoTodos(){
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => /*array(' getdate() BETWEEN Subperiodo.mesdesde AND Subperiodo.meshasta')*/
					array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(d, 30, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
				
		endif;
		// echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		//$subPerId=47;
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		$datosSession = $this->viewVars; 
		//echo 'datosSession:<pre>'.print_r($datosSession,1).'</pre>';

		$idEvaluador = $datosSession['idPer'];
		if( $idEvaluador == 444 ){
			// $idEvaluador = 693;
			//$idEvaluador = 22;
			// $idEvaluador = 6;
		}
		/*				
		$options = array('conditions' => array('Evaluafuncionario.precalificadore_id = '.$idEvaluador, 'Evaluafuncionario.subperiodo_id = '.$subPerId)
						, 'fields' => 'DISTINCT Evaluafuncionario.funcionario_id' );
		*/
		$options = array('conditions' => array('Evaluafuncionario.subperiodo_id = '.$subPerId), 'fields' => 'DISTINCT Evaluafuncionario.funcionario_id' );
						
		if( $this->Evaluafuncionario->find('count', $options) <= 0 ){
			$this->Session->setFlash('No existen funcionarios asociados para este periodo-subperiodo...');
		}
						
						
		$listaAsignados = $this->Evaluafuncionario->find('all', $options);
		// echo '<br />listaAsignados:<pre>'.print_r($listaAsignados,1).'</pre>';
		$arrayFuncionarios = array();
		foreach($listaAsignados as $lista){
			$arrayFuncionarios[] = $lista['Evaluafuncionario']['funcionario_id'];
		}
		//echo '<br />arrayFuncionarios:<pre>'.print_r($arrayFuncionarios,1).'</pre>';
		$inFuncionarios = implode(',', $arrayFuncionarios);
		
		$listaSelecEvaluados = array();
		if( $this->Evaluafuncionario->find('count', $options) > 0 ){
			$this->Persona->recursive = 1;
			$listaSelecEvaluados = $this->Persona->find('all', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')'
																						 , 'PersonaEstado.per_estado = 1'
																						 , 'Persona.ID_PER IN ('.$inFuncionarios.')' )
																	, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																	, 'fields' => array('Persona.*')
																	) );
		}
		// echo 'listaSelecEvaluados:<pre>'.print_r($listaSelecEvaluados,1).'</pre>';
		
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'listaSelecEvaluados') );		
	}

	/*****************************************/
	/*******	ANOTACION DE DEMERITO	******/
	/*****************************************/
	public function anotaDemeritoindex(){
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => /*array(' getdate() BETWEEN Subperiodo.mesdesde AND Subperiodo.meshasta')*/
					array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(d, 1, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
				
		endif;
		//echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		$datosSession = $this->viewVars; 
		//echo 'datosSession:<pre>'.print_r($datosSession,1).'</pre>';

		$idEvaluador = $datosSession['idPer'];
		if( $idEvaluador == 444 ){
			// $idEvaluador = 693;
			//$idEvaluador = 22;
			// $idEvaluador = 6;
		}

		$options = array('conditions' => array('Evaluafuncionario.subperiodo_id = '.$subPerId), 'fields' => 'DISTINCT Evaluafuncionario.funcionario_id' );					
		if( $this->Evaluafuncionario->find('count', $options) <= 0 ){
			$this->Session->setFlash('No existen funcionarios asociados para este periodo-subperiodo...');
		}
						
						
		$listaAsignados = $this->Evaluafuncionario->find('all', $options);
		// echo '<br />listaAsignados:<pre>'.print_r($listaAsignados,1).'</pre>';
		$arrayFuncionarios = array();
		foreach($listaAsignados as $lista){
			$arrayFuncionarios[] = $lista['Evaluafuncionario']['funcionario_id'];
		}
		//echo '<br />arrayFuncionarios:<pre>'.print_r($arrayFuncionarios,1).'</pre>';
		$inFuncionarios = implode(',', $arrayFuncionarios);
		
		$listaSelecEvaluados = array();
		if( $this->Evaluafuncionario->find('count', $options) > 0 ){
			$this->Persona->recursive = 1;
			$listaSelecEvaluados = $this->Persona->find('all', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')'
																						 , 'PersonaEstado.per_estado = 1'
																						 , 'Persona.ID_PER IN ('.$inFuncionarios.')' )
																	, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																	, 'fields' => array('Persona.*')
																	) );
		}
		// echo 'listaSelecEvaluados:<pre>'.print_r($listaSelecEvaluados,1).'</pre>';
		
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'listaSelecEvaluados') );		
	}
	public function listaAnotacionDemerito($idPerAnotacion = null){
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => /*array(' getdate() BETWEEN Subperiodo.mesdesde AND Subperiodo.meshasta')*/
					array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(d, 1, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		
		// echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId = $periodoEvaluados['Periodo']['id'];
		$perNombre = $periodoEvaluados['Periodo']['etiqueta'];
		$subPerId = $periodoEvaluados['Subperiodo']['id'];
		$subPerNombre = $periodoEvaluados['Subperiodo']['etiqueta'];
		
		$datosSession = $this->Session->read('personaDatos');
		$idPer = $datosSession['PersonaEstado']['id_per'];
		$rutPer = $datosSession['PersonaEstado']['per_rut'];
		////echo 'datosSession:<pre>'.print_r($datosSession,1).'</pre>';
		
		$this->Persona->recursive=1;
		$listaFuncionarios0 = $this->Persona->find('all', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')', 'PersonaEstado.per_estado = 1')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																)
													);
		//echo 'listaFuncionarios0:<pre>'.print_r($listaFuncionarios0,1).'</pre>';
		$listaFuncionarios=array();
		foreach($listaFuncionarios0 as $lista){
			$listaFuncionarios[$lista['Persona']['id_per']] =utf8_encode($lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT']);
		}
		//echo 'listaFuncionarios:<pre>'.print_r($listaFuncionarios,1).'</pre>';
		
		if(is_null($idPerAnotacion)){
			$idPerAnotacion=$this->data['Anotacione']['id_per'];
		}
		//$this->Session->setFlash('idPerAnotacion1: '.$idPerAnotacion);
		/*** SOLO PARA DESARROLLO ***/
		//$idPer =166;
		$condiciones= array( 'conditions'=>'Anotademerito.funcionario_id = '.$idPerAnotacion, 'order' => 'Anotademerito.created'  );
		$listaAnotaciones = $this->Anotademerito->find('all', $condiciones);
		//echo 'listaAnotaciones<pre>'.print_r($listaAnotaciones, 1).'</pre><hr>';
		
		//$this->params['data']['Anotacione']['id_per']
		
		$this->Persona->recursive=0;
		$datosFuncionario = $this->Persona->find('first', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')'
																						, 'PersonaEstado.per_estado = 1'
																						, 'Persona.id_per = '.$idPerAnotacion.' ')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																)
													);
		//echo 'datosFuncionario:<pre>'.print_r($datosFuncionario,1).'</pre>';
		$nombreFuncionario = utf8_encode($datosFuncionario['Persona']['NOMBRES'].' '.$datosFuncionario['Persona']['AP_PAT'].' '.$datosFuncionario['Persona']['AP_MAT']);
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'listaAnotaciones', 'listaFuncionarios', 'idPerAnotacion', 'nombreFuncionario'));
	}
	
	public function adddemerito(){
		/******************************/
		/***** SECCION QUE GUARDA *****/
		/******************************/
		if( !empty($this->data) && count($this->data['Anotacione']) > 2){
			$this->data['Anotademerito'] = $this->data['Anotacione'];
			if(0):
				$this->Session->setFlash('data :<pre>'.print_r($this->data,1).'</pre>'.($this->data['Anotacione']['documento']['tmp_name']));
				$this->data['Anotademerito'] = $this->data['Anotacione'];
				//////unset($this->data['Anotacione']);
				$this->Session->setFlash('data :<pre>'.print_r($this->data,1).'</pre>'.($this->data['Anotademerito']['documento']['tmp_name']));
			else:
				$Funcionespropias = new Funcionespropias();
				
				$msgDemeritoAdd = 'ANOTACION de DEMERITO guardada.';
				if( isset($this->data['Anotademerito']['documento']['error']) && $this->data['Anotademerito']['documento']['error'] == 0 ){
					$nombreArchivo = 'anotaDemerito_'.$this->data['Anotademerito']['funcionario_id'].'_'
									.$this->data['Anotademerito']['solicita_id'].'_'.date('dmY').'_'.rand(1, 900000).'.'
									.$Funcionespropias->sacaExtencionArchivo($this->data['Anotademerito']['documento']['name'] );
					$nombreRutaArchivo = WWW_ROOT."files".DS."deDemerito".DS.$nombreArchivo;
					
					if(move_uploaded_file($this->data['Anotademerito']['documento']['tmp_name'], $nombreRutaArchivo)){
						$this->data['Anotademerito']['archivo_nombre'] = $nombreArchivo;
						$msgDemeritoAdd = 'ANOTACION de DEMERITO Subida y guardada.';
					}
				}
				
				unset( $this->data['Anotacione']['documento'] );
				unset( $this->data['Anotademerito']['documento'] );
				
				if($this->Anotademerito->save($this->data['Anotademerito'])){
					$this->Session->setFlash($msgDemeritoAdd);
					$this->redirect( array('action' => 'listaAnotacionDemerito', $this->data['Anotacione']['funcionario_id']) );
				}else{
					$errores = $Funcionespropias->mostrarErrores($this->Anotacione->invalidFields());
					$msgDemeritoAdd = 'Ocurrio un error y no se pudo guardar.<br />'.$errores;
				}
				$this->Session->setFlash($msgDemeritoAdd);
			endif;
		}
		/******************************/
		/*** FIN SECCION QUE GUARDA ***/
		/******************************/
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => /*array(' getdate() BETWEEN Subperiodo.mesdesde AND Subperiodo.meshasta')*/
					array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(d, 1, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		
		// echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		$this->Persona->recursive=0;
		$listaFuncionarios0 = $this->Persona->find('all', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')', 'PersonaEstado.per_estado = 1')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																) );
		//echo 'listaFuncionarios0:<pre>'.print_r($listaFuncionarios0,1).'</pre>';
		$listaFuncionarios=array();
		foreach($listaFuncionarios0 as $lista){
			$listaFuncionarios[$lista['Persona']['id_per']] = utf8_encode($lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT']);
		}
		//echo 'listaFuncionarios:<pre>'.print_r($listaFuncionarios,1).'</pre>';

		$idPerFunc = $this->data['Anotacione']['funcionario_id'];
		/*** SOLO PARA DESARROLLO ***/
		//$idPerFunc =676;
		//if($idPer == 444)$idPerFunc=676;
		/*** $listaHistoria = $this->Historia->traeFuncionariosHistoria($idPerFunc); ***/
		$listaHistoria = $this->Historia->traeFuncionariosHistoria($idPerFunc);
		$listaHistoria = $listaHistoria[0][0];
		$idJefeDirecto = $listaHistoria['idJefeDirecto'];
		//echo 'listaHistoria:<pre>'.print_r($listaHistoria,1).'</pre>';
		//echo 'data:<pre>'.print_r($this->data,1).'</pre>';
		
		$this->Persona->recursive=0;
		$datosFuncionario = $this->Persona->find('first', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')'
																						, 'PersonaEstado.per_estado = 1'
																						, 'Persona.id_per = '.$idPerFunc.' ')
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields'=> array('DISTINCT Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') 
																) );
		$nombreFuncionario = utf8_encode($datosFuncionario['Persona']['NOMBRES'].' '.$datosFuncionario['Persona']['AP_PAT']
										.' '.$datosFuncionario['Persona']['AP_MAT'] );
		
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'listaFuncionarios', 'idJefeDirecto', 'idPerFunc', 'nombreFuncionario') );
	}

}

?>