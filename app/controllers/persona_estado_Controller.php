<?php
//App::uses('AppController', 'Controller');

class PersonaEstadoController extends AppController {
	//ATRIBUTOS***/
	//var $name = 'PersonaEstado';
	//var $scaffold;
	var $helpers = array('Html', 'Form');
	var $componets = array('Session');
	
	var $paginate = array(
        'limit' => 10,
        'order' => array(
            array('PersonaEstado.per_rut' => 'asc',
					/*'PersonaEstado.usuario' => 'asc'*/
				)
        )
    );


	//METODOS***/
	public function index() {
		//$this->Persona->recursive = 1;
		//$this->set('personas', $this->paginate());
		//$listaPersonaEstado = $this->PersonaEstado->find('all');
		//$this->set('listaPersonaEstado', $listaPersonaEstado);
		$this->set('listaPersonaEstado', $this->PersonaEstado->find('all') );
	}
	
	public function ver($id = null){
		if(!$id){ 
			throw new NotFoundException('Datos no validos'); }
		//$this->PersonaEstado->recursive = 3;
		////$detalleEstPers = $this->PersonaEstado->find(array('PersonaEstado.ident' => $id));
		$detalleEstPers = $this->PersonaEstado->find(array('PersonaEstado.id_per' => $id));
		////$detalleEstPers = $this->PersonaEstado->findById($id);
		if(!$detalleEstPers){ 
			throw new NotFoundException('Persona no existe'); }
		$this->set('detalleEstPers', $detalleEstPers);
	}
	
	public function editar($id = null){
		if(!$id){
			throw new NotFoundException('Datos no validos');	}
		////$detalleEstPers = $this->PersonaEstado->find(array('PersonaEstado.ident' => $id));
		$detalleEstPers = $this->PersonaEstado->find(array('PersonaEstado.id_per' => $id));
		if(!$detalleEstPers){
			throw new NotFoundException('Persona no existe');	}
		//if($this->request->is('post', 'put')){
		if(!empty($this->data)){
			$this->PersonaEstado->id = $id;
			if($this->PersonaEstado->save($this->data)){
				$this->Session->setFlash('Registro modificado', $element = 'default', $params = array('class' => 'success') );
				return $this->redirect(array('action' => 'index'));		}
			$this->Session->setFlash('No se pudo modificar el registro');	}
		if(!$this->data){
		//if(!empty($this->data)){
			$this->data = $detalleEstPers;	}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
/*
per_rut
per_estado
cod_grupo
funcional
id_per
id_per_yomi
usuario
honorario
calidadJuridica
*/
?>