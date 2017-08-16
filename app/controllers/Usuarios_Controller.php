<?
class UsuariosController extends AppController{
	public $uses = array( 'User', 'Usersperfil') ; //, 'Usersperfils');
	var $componets = array ('Auth', 'Html', 'Form', 'Session');
	//////var $scaffold;

	var $paginate = array(
        'limit' => 10,
        'order' => array(
            'User.username' => 'ASC'
        )
    );
	
	public function traePerfils(){
		$listaPerfiles0 = $this->Usersperfil->find('all');
		$listaPerfiles2 = array();
		foreach($listaPerfiles0 as $listaP){
			$listaPerfiles2[$listaP['Usersperfil']['id']] = $listaP['Usersperfil']['etiqueta'];
		}
		return $listaPerfiles2;
	}
	
	public function index(){
		$listaPerfiles0 = $this->Usersperfil->find('all');
		$listaPerfiles2 = array();
		foreach($listaPerfiles0 as $listaP){
			$listaPerfiles2[$listaP['Usersperfil']['id']] = $listaP['Usersperfil']['etiqueta'];
		}
		$this->User->recursive = 2;
		$listaUsuarios = $this->paginate();
		$this->set('users', $listaUsuarios);
		$this->set('listaPerfiles2', $listaPerfiles2);
	}
	
	function add() {
		if (!empty($this->data)) {	
			if($this->data['User']['password'] == '47e08b26869db7fa9d1c9ac55793c6861dae5dca'){
				$this->data['User']['password']='';
			}
			$this->data['User']['usersperfil_id']= 4;
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('Usuario grabado', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('No se pudo grabar. Por favor, intente nuevamente.', true));
			}
		}
		$perfils = $this->traePerfils();
		$this->set('perfils', $perfils);
	}
	
	function edit($id = null) {
		if (!empty($this->data)) {
			if(isset($this->data['User']['cambiaPassw']))unset($this->data['User']['cambiaPassw']);
			else unset($this->data['User']['password']);
			
			$array_User = array(
								'id'=>$this->data['User']['id'],
								'username'=>$this->data['User']['username'],
								'usersperfil_id'=>0
			);
			
			$array_Usersperfils=array();
			if(isset($this->data['Usersperfils']) && count($this->data['Usersperfils'])>0){
				foreach($this->data['Usersperfils'] as $pnt=> $listUsersperfil){				
						$elIdReg ='';
						$array_Usersperfils[]= array(
												 'user_id'=>$this->data['User']['id']
												, 'userperfil_id'=>$this->data['Usersperfils'][$pnt]
											);
				}
			}
			$this->data['Usersperfils']=$array_Usersperfils;
			
		   if(0):
				echo '$this->data2: <pre>'.print_r($this->data, true).'</pre>';
		   else:
				if ($this->User->saveAll($this->data['User'])) {
					$countReg = $this->Usersperfils->find('count', array('conditions'=>
																		array('Usersperfils.user_id = '.$this->data['User']['id'])
															)
					);
					if($countReg>0)
						$this->Usersperfils->deleteAll(array('Usersperfils.user_id = '.$this->data['User']['id']), false);
					if ($this->Usersperfils->saveAll($this->data['Usersperfils'])) {
						$this->Session->setFlash(__('Usuario Actualizado.', true));
						$this->redirect(array('action' => 'index'));
					}else {
						$countReg = $this->Usersperfils->find('count', array('conditions'=>
																		array('Usersperfils.user_id = '.$this->data['User']['id'])
															)
						);
						if($countReg>0)$this->Session->setFlash(__('3: No se pudo guardar. Por favor, intente nuevamente.', true));
						else $this->Session->setFlash(__('Usuario Actualizado.', true));$this->redirect(array('action' => 'index'));
					}
				} else {
					$this->Session->setFlash(__('1: No se pudo guardar. Por favor, intente nuevamente.', true));
				}
		   endif;
		}
		if (empty($this->data))$this->data = $this->User->read(null, $id);
		$perfils = $this->traePerfils();
		$userUsersperfil = $this->Usersperfils->find('all', array('conditions'=>array('Usersperfils.user_id'=>$this->data['User']['id']) ) );
		$userUsersperfil2=array();
		foreach($userUsersperfil as $lstUserUsersperfil){
			if(isset($lstUserUsersperfil['Usersperfils']['userperfil_id'])){
				$userUsersperfil2[]=$lstUserUsersperfil['Usersperfils']['userperfil_id'];
			}
		}
		
		$this->set('perfils', $perfils);
		$this->set('userUsersperfil2', $userUsersperfil2);
	}
	
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('Usuario borrado permanentemente', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Usuario no ha sido borrado, intente nuevamente.', true));
		$this->redirect(array('action' => 'index'));
	}
	
}
?>
