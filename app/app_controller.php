<?php
App::import('Vendor','Funcionespropias');
class AppController extends Controller {
	
	// Added by Jason Wydro: Simple Auth CakePHP v1.0
	var $components = array('Auth','Session');
	//public $uses = array( 'User');
	public $uses = array( 'User', 'Usersperfil', 'Usersperfils', 'PersonaEstado', 'Periodo', 'Subperiodo');
	
	var $calidJuridSinHonorarios = "1, 2, 4, 5";
	var $swPeriodoEvaluados = 0;
	var $miembrosJunta = array(1 => 'Integrante Junta', 2 => 'Representante Personal', 3 => 'Representante Asociación', 4 => 'Secretario(a) Junta');


	function beforeFilter() {
		Configure::write('Config.language', $this->Session->read('Config.language'));
		//$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
		$this->Auth->loginRedirect = array('controller' => 'pages', 'action' => 'home');
		$this->Auth->authError = 'Debe loguear para ver esta pagina';
		$this->Auth->loginError = 'Usuario/password incorrectos';
		
		//$this->Auth->loginRedirect = array('controller' => 'pages', 'action' => 'display', 'home');
		$this->Auth->allow('login', 'logout');
		//$this->Auth->allow('*');
		$this->Auth->authorize = 'controller';
		//$this->log($this->data, LOG_DEBUG);
		
		$listaMenuUsr0 = -1;
		if($this->Auth->user()){
			$current_user = $this->Auth->user();
			if( isset($current_user['User']['id']) ) {
				$condiciones = array('Usersperfils.user_id' => $current_user['User']['id']);
				$listaMenuUsr = $this->Usersperfils->find('all', array('conditions'=> $condiciones ) );
				$listaMenuUsr0=array();
				if(count($listaMenuUsr) >0){
					foreach($listaMenuUsr as $lstMeUs){
						//$this->log($lstMeUs['Usersperfils']['userperfil_id'], LOG_DEBUG);
						$listaMenuUsr0[]=$lstMeUs['Usersperfils']['userperfil_id'];
					}
				}
				$idPer = $this->PersonaEstado->find('first', array('conditions'=> array('PersonaEstado.usuario' => $current_user['User']['username']) ) );
				//echo '<br />idPer:<pre>'.print_r($idPer,1).'</pre>';
				$this->set('idFunc', $current_user['User']['id']);
				$this->set('idPer', $idPer['PersonaEstado']['id_per']);
			}
		}else{
			if( !empty($this->data) ){
				$revisaUsuario = $this->User->find('count', array('conditions'=> array('User.username' => $this->data['User']['username']) ) );
				if( $revisaUsuario == 0){
					//$this->Session->setFlash('Your stuff: '.$revisaUsuario.', us: '.print_r($this->data, 1));
					$this->Auth->loginError = 'Usted no se encuentra registrado como usuario en el sistema.<br />Comuniquese con Unidad de Personas.';
					// $this->Session->setFlash('Usted no se encuentra registrado como usuario en el sistema.<br />Comuniquese con Unidad de Personas.');
				}
			}
		}
		
		$periodoEvaluados = $this->Periodo->getperiodo();
		//echo (date('d-m-Y')).'periodoEvaluados<pre>'.print_r($periodoEvaluados, 1).'</pre><hr>';
		
		$EvaluafuncionarioElPeriodo = $this->Periodo->getsubperiodosid($periodoEvaluados);
		//echo 'EvaluafuncionarioElPeriodo<pre>'.print_r($EvaluafuncionarioElPeriodo, 1).'</pre><hr>';
		$this->set('subperiodosDelPeriodo', $EvaluafuncionarioElPeriodo);
		
		$subperiodoEvaluado = $this->Subperiodo->find('first', array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(m, 1, Subperiodo.mesevalhasta)')
																, 'order' => array('Subperiodo.mesdesde'=> '')
																,'fields' => array('Subperiodo.id') ) );
		//echo 'subperiodoEvaluado<pre>'.print_r($subperiodoEvaluado, 1).'</pre><hr>';
		$subPeriodoPoneNota = $this->Periodo->getsubperiodoponenota($periodoEvaluados);
		//echo 'subPeriodoPoneNota<pre>'.print_r($subPeriodoPoneNota, 1).'</pre><hr>';
		$this->set('muestraLink', $this->menusegunsubperiodo($subperiodoEvaluado['Subperiodo']['id'], $subPeriodoPoneNota));
		
		$this->set('current_user', $this->Auth->user());
		$this->set('listaMenuUsr0', $listaMenuUsr0);
		$this->set('ubicacionEn', $this->viewPath);
		$this->set('miembrosJunta', $this->miembrosJunta);
		
		$elIdDelFuncionario = $this->PersonaEstado->getidfuncionario($current_user['User']['username']);
		$this->set('elIdDelFuncionario', $elIdDelFuncionario);
		//echo 'elIdDelFuncionario<pre>'.print_r($elIdDelFuncionario, 1).'</pre><hr>';
	}
	
	function isAuthorized() { return true; }
	
	function beforeRender(){ }
	
	function menusegunsubperiodo($subperiodoEvaluado, $subPeriodoPoneNota){
		if($subperiodoEvaluado == $subPeriodoPoneNota){
			return 1;
		}return 0;
	}
	
}