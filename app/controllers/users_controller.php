<?
class UsersController extends AppController{
	var $name = 'Users';
	public $uses = array('User', 'Usersperfil', 'Usersperfils', 'PersonaEstado');
	//var $scaffold;

	var $componets = array ('Html', 'Form');
	
	function beforeFilter() {
			parent::beforeFilter(); 
			//$this->Auth->allow('index');
	}
	
	public function login(){ }
	
	public function logout(){
		$this->Session->setFlash('Salida');
		$this->redirect($this->Auth->logout());
	}
	
	public function home() {}
	
	public function verldap($datosUsr){
		
		//echo 'datosUsr: <pre>'.print_r($datosUsr, true).'</pre>';
	
		//$this->Session->setFlash('ak: '.$this->params);
		//echo 'param: '.print_r($this->params,1).'<br />';
		///echo base64_decode($this->params[pass][1]).'<br />';
		//echo '<pre>'.print_r($this, true).'</pre>';
		////echo '<pre>'.print_r($this->params, true).'</pre>';
		//$this->Session->setFlash('ak: '.$this->action);
		
		//if(!$datosUsr)$this->redirect(array('controller' => 'users', 'action' => 'login'));
		
		$usr=$this->params['pass'][0];
		//if(!$usr)$this->redirect(array('controller' => 'users', 'action' => 'login'));
		$pas=base64_decode($this->params['pass'][1]); //'Xhinodread400';
		$adConec = ldap_connect("ldap://192.168.200.198", 389); // or die();
		//$adConec = ldap_connect("ldap://192.168.200.198", 389) or die("ldap_connect Could nOt connect to SERVIDOR!");
		ldap_set_option($adConec, LDAP_OPT_PROTOCOL_VERSION, 2);
		ldap_set_option($adConec, LDAP_OPT_REFERRALS, 1);
		$conecActDir = ldap_bind($adConec, $usr."@gorecoquimbo.cl", $pas); // or die('Error en usuario o clave.');
		if(!$conecActDir){
			$this->Session->setFlash('Error en usuario o clave.');
			$this->Session->destroy();
			//$this->redirect($this->Auth->logout());
			//$this->redirect(array('controller'=>'users', 'action' => '/'));
			
			echo '<script type="text/javascript">';
			echo ' alert("Error en usuario o clave"); ';
			echo ' window.location = "/demoEvaluacionFunc/admin"; ';
			echo '</script>';
		}
		
		$this->User->recursive=2;
		$listaMenuUsr = $this->User->find('first', array('conditions'=> array('User.username = \''.$this->params['pass'][0].'\' ')) );
		//echo 'listaMenuUsr: <pre>'.print_r($listaMenuUsr, true).'</pre>';
		$personaDatos= $this->PersonaEstado->find('first', array('conditions'=> array('PersonaEstado.usuario = \''.$this->params['pass'][0].'\' ')));
		//echo 'personaDatos: <pre>'.print_r($personaDatos, true).'</pre>';
		$this->Session->write('personaDatos', $personaDatos);
		$listaMenuUsr0=array();
		//foreach($listaMenuUsr['Usersperfils'] as $lstMeUs){
		foreach($listaMenuUsr['User'] as $lstMeUs){
			//echo $lstMeUs['userperfil_id'].'<br />';
			$listaMenuUsr0[]=$lstMeUs['userperfil_id'];
		}
		//echo 'listaMenuUsr0: <pre>'.print_r($listaMenuUsr0, true).'</pre>';
		$listMenus0 = $this->Usersperfil->find('all');
		//echo 'listMenus0: <pre>'.print_r($listMenus0, true).'</pre>';
		$listMenus= array();
		foreach($listMenus0 as $lisM){
			$listMenus[$lisM['Usersperfil']['id']] = $lisM['Usersperfil']['etiqueta'];
		}
		//echo 'listMenus: <pre>'.print_r($listMenus, true).'</pre>';
		$eluser=$this->params['pass'][0];
		$lapass=base64_decode($this->params['pass'][1]);
		$this->Session->write('Nombredeusuario', $eluser);
		//echo 'usr0: '.$this->Session->read('Nombredeusuario');
		$this->set('eluser', $eluser);
		$this->set('lapass', $lapass);
		$this->set('listaMenuUsr', $listaMenuUsr);
		$this->set('listaMenuUsr0', $listaMenuUsr0);
		
		//$this->redirect(array('action' => 'admin'));
		//echo 'Nombredeusuario: '.$this->Session->read('Nombredeusuario').'-';
		//Uecho 'Session: <pre>'.print_r($this->Session, 1).'</pre>';
		//echo 'this: <pre>'.print_r($this, true).'</pre>';
		
	}
	
	public function index(){
		//$this->redirect(array('action'=>'login'));
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}
	
	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}
	function add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
	}
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}
	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}
	function admin_add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
	}
	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}

}
?>