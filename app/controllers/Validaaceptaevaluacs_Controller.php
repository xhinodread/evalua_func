<?
App::import('Vendor', 'phpmailer', array('file' => 'phpmailer'.DS.'PHPMailerAutoload.php'));
App::import('Vendor', 'Funcionespropias');
class ValidaaceptaevaluacsController extends AppController{
	public $uses = array('Validaaceptaevaluac', 'Calificadore', 'Persona', 'Historia', 'Periodo', 'Subperiodo', 'Evaluafuncionario');
	//public $uses = '';
	var $helpers = array('Html', 'Form');
	//var $componets = array('Session', 'Auth');
	var $components = array('Email'); 
	//var $scaffold;
	
	
	public function index(){
		//echo 'this:<pre>'.print_r($this->Validaaceptaevaluac, true).'</pre><hr>';
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND DATEADD(m, 2, Subperiodo.mesevalhasta)')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		// echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		//echo ' $this->Auth->user()<pre>'.print_r($this->Auth->user(), true).'</pre><hr>';
		if( !isset($this->Auth) ){
			$this->redirect( array('controller'=>'users', 'action' => 'logout'));
		}else{
			$authUser = $this->Auth->user();
			$datosSession =$this->PersonaEstado->find('first', array('conditions'=> 'PersonaEstado.usuario = \''.$authUser['User']['username'].'\'') );
		}
		$idPer = $datosSession['PersonaEstado']['id_per'];
		$rutPer = $datosSession['PersonaEstado']['per_rut'];
		//echo 'datosSession:<pre>'.print_r($datosSession,1).'</pre>';
		
		/*** SOLO PARA DESARROLLO ***/
		if($idPer == 444)$idPer = 676; //25; // 211; // 47; //79; // 166; //  $idPer = 47;
		// echo 'idPer:'.$idPer.', subPerId: '.$subPerId;
		$condiciones= array('conditions'=>array('Validaaceptaevaluac.funcionario_id = '.$idPer.' '
												,'Validaaceptaevaluac.subperiodo_id = '.$subPerId.' ')
					);
		$datosValidaEvalucion = $this->Validaaceptaevaluac->find('first', $condiciones);
		$nroReg = count($datosValidaEvalucion['Validaaceptaevaluac']);
		//echo 'count: '.count($datosValidaEvalucion['Validaaceptaevaluac']).'<hr>';
		//echo 'datosValidaEvalucion<pre>'.print_r($datosValidaEvalucion, true).'</pre><hr>';
		
		/******************************/
		/***** SECCION QUE GUARDA *****/
		/******************************/

			if(!empty($this->data)){
				if(0):
					//echo 'this->data:<pre>'.print_r($this->data, true).'</pre><hr>';
					$this->emailAceptaPrecalif($subPerId);
				else:
					//echo 'this->data:<pre>'.print_r($this->data, true).'</pre><hr>';
					if(!isset($this->data['Validaaceptaevaluac']['aceptar']))$this->redirect(array('action' => 'index'));
					$estadoChek = $this->data['Validaaceptaevaluac']['aceptar'];
					// unset($this->data['Validaaceptaevaluac']['aceptar']);
					// unset($this->data['Validaaceptaevaluac']['aceptarR']);
					//$this->data['Validaaceptaevaluac']['funcionario_id']=$idPer;
					//$this->data['Validaaceptaevaluac']['subperiodo_id']=$subPerId;
					
					$varDebug = '<br />'.$estadoChek.'<br />'.print_r($this->data, 1).'<br />'.print_r($condiciones['conditions'], 1);
					$this->Session->setFlash('Sin cambios');
					
					
				  if(0):
						$this->emailAceptaPrecalif($subPerId);
				  else:
						if($estadoChek == 0){
							//echo print_r($this->data['Validaaceptaevaluac'],1).'Borrar lo existente<br />';
							//if($this->Validaaceptaevaluac->deleteAll($this->data['Validaaceptaevaluac'], false)){
							if($this->Validaaceptaevaluac->deleteAll($condiciones['conditions'], false)){
								$this->Session->setFlash('Notificación actualizada, sin notificación en el email.');
							}
						}else if($estadoChek == 1){
							$this->Session->setFlash('Sin cambios');
							//if($this->Validaaceptaevaluac->deleteAll($this->data['Validaaceptaevaluac'], false)){
							if($this->Validaaceptaevaluac->deleteAll($condiciones['conditions'], false)){
								if($this->Validaaceptaevaluac->save($this->data)){
									$this->emailAceptaPrecalif($subPerId);
									//$this->Session->setFlash('Actualizado, notificado.');
									$this->redirect(array('action' => 'index'));
								}
							}else{
								if($this->Validaaceptaevaluac->save($this->data)){
									$this->emailAceptaPrecalif($subPerId);
									//$this->Session->setFlash('Actualizado, notificado.');
									$this->redirect(array('action' => 'index'));
								}
							}
						}
						$this->redirect(array('action' => 'index'));
						//echo 'this->data:<pre>'.print_r($this->data, true).'</pre><hr>';
				  endif;
			endif;
			}
		/*******************************/
		/***** FIN SECCION QUE GUARDA **/
		/*******************************/
		
		$this->set(compact('nroReg', 'idPer', 'perNombre', 'subPerNombre', 'subPerId', 'datosValidaEvalucion'));
	}
	
	public function emailAceptaPrecalif($subPerId){
		if( !isset($this->data['Validaaceptaevaluac']) && count($this->data['Validaaceptaevaluac'] <= 0) 
				&& $this->data['Validaaceptaevaluac']['funcionario_id'] <= 0 ){
			$this->Session->setFlash('Sin información');
			$this->redirect(array('action' => 'index'));
		}
		$this->autoRender = false;
		
		$heSidoNotificado = ($this->data['Validaaceptaevaluac']['aceptar'] == 1 ? " - He sido Notificado" : "");
		$heSidoRetroalimentado = ($this->data['Validaaceptaevaluac']['aceptarR'] == 1 ? " - He sido Retroalimentado" : "");
		
		$this->Persona->recursive = -1;
		$funcionario = $this->Persona->findByid_per($this->data['Validaaceptaevaluac']['funcionario_id']);
		$nombreFuncionario = $funcionario['Persona']['NOMBRES'].' '.$funcionario['Persona']['AP_PAT'].' '.$funcionario['Persona']['AP_MAT'];
		$this->Evaluafuncionario->recursive = -1;
		$options = array('conditions' => array('Evaluafuncionario.funcionario_id' => $this->data['Validaaceptaevaluac']['funcionario_id']
											 , 'Evaluafuncionario.subperiodo_id' => $subPerId)
						,'fields' => array('DISTINCT Evaluafuncionario.precalificadore_id')
				);
		$idPrecalificador = $this->Evaluafuncionario->find('first', $options);
		$emailPrecalificadorX = ( $this->Persona->findByid_per($idPrecalificador['Evaluafuncionario']['precalificadore_id']) );
		$emailPrecalificador = strtolower( $emailPrecalificadorX['Persona']['EMAIL'] );
		
		$emailPrecalificador = ( strlen($emailPrecalificador)>2 ? $emailPrecalificador : 'soporte@gorecoquimbo.cl' );
		
		//$emailPrecalificador = 'jaracena@gorecoquimbo.cl';		
		$body = 'Estimado(a) Precalificador(a)<br /> El funcionario(a) '.$nombreFuncionario.' <br /> Ha validado el informe de desempeño para este periodo.<br />'
				.'<br />Las observaciones son:<br />'
				.''.( strlen($this->data['Validaaceptaevaluac']['texto']) <=0 ? 'Sin texto' : $this->data['Validaaceptaevaluac']['texto'])
				."<br /><br />".$heSidoNotificado.'<br />'.$heSidoRetroalimentado
				.'<br /><br />Sin otro particular.<br />Me despido.<br />Atentamente.';
				//.'<br /><br />Sin otro particular.<br />Me despido.<br />Atentamente.<pre>'.print_r($emailPrecalificadorX, 1).'</pre>';
		
		
		$Funciones = new Funcionespropias();
		if($Funciones->enviaCorreo($body, 'VALIDAR Y ACEPTAR PRECALIFICACIÓN', $emailPrecalificador )){
			$this->Session->setFlash('Email Enviado a ... '.$emailPrecalificador);
			$this->redirect(array('action' => 'index'));
		}else{
			$this->Session->setFlash('Ocurrio un error, no pudo enviarse el correo:<pre>'.print_r(error_get_last(), 1).'</pre>');
			$this->redirect(array('action' => 'index'));
		}
		
		$this->redirect(array('action' => 'index'));
	}
	
	public function indexMantenedor($idPer = null){
		//echo 'this:<pre>'.print_r($this->Validaaceptaevaluac, true).'</pre><hr>';
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND DATEADD(d, 2, Subperiodo.mesevalhasta) ')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		//echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		//echo ' $this->Auth->user()<pre>'.print_r($this->Auth->user(), true).'</pre><hr>';
		if( !isset($this->Auth) ){
			$this->redirect( array('controller'=>'users', 'action' => 'logout'));
		}else{
			$authUser = $this->Auth->user();
			$datosSession =$this->PersonaEstado->find('first', array('conditions'=> 'PersonaEstado.usuario = \''.$authUser['User']['username'].'\'') );
		}
		// $idPer = $datosSession['PersonaEstado']['id_per'];
		//echo 'datosSession:<pre>'.print_r($datosSession,1).'</pre>';
		
		$condiciones= array('conditions'=>array('Validaaceptaevaluac.funcionario_id = '.$idPer.' '
												,'Validaaceptaevaluac.subperiodo_id = '.$subPerId.' ')
					);
		$datosValidaEvalucion = $this->Validaaceptaevaluac->find('first', $condiciones);
		$nroReg = count($datosValidaEvalucion['Validaaceptaevaluac']);
		//echo 'count: '.count($datosValidaEvalucion['Validaaceptaevaluac']).'<hr>';
		//echo 'datosValidaEvalucion<pre>'.print_r($datosValidaEvalucion, true).'</pre><hr>';
		
		/******************************/
		/***** SECCION QUE GUARDA *****/
		/******************************/

			if(!empty($this->data)){

			}
		/*******************************/
		/***** FIN SECCION QUE GUARDA **/
		/*******************************/
		
		$this->set(compact('nroReg', 'idPer', 'perNombre', 'subPerNombre', 'subPerId', 'datosValidaEvalucion'));
	}
	
}

?>