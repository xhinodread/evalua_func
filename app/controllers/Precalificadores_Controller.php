<?
class PrecalificadoresController extends AppController{
	public $uses = array('Precalificadore', 'Persona', 'Periodo', 'Subperiodo');
	var $helpers = array('Html', 'Form');
	var $componets = array('Session', 'Auth');
	
	public function beforeFilter(){ parent::beforeFilter(); }
	
	public function index(){
		/*** SECCION QUE GUARDA/ELIMINA ***/
		if($this->data){
			$arrayIn = array();
			$arrayOut = array();
			foreach($this->data['Precalificadores'] as $ind => $valorChks){
				//// echo $ind.'-> '.$valorChks.'<br />';
				if($valorChks){
					//echo $ind.'-> '.$valorChks.'<br />';
					$chkPosIn = strpos($ind, 'In');
					$chkPosOut = strpos($ind, 'Out');
					
					if($chkPosIn){
						$idIn = substr($ind, $chkPosIn +2);
						//echo $chkPosIn.'<br />';
						//echo $idIn.'<br />';
						$arrayIn[]['funcionario_id'] = $idIn;
					}else if($chkPosOut){
						$idOut = substr($ind, $chkPosOut +3);
						//echo $chkPosOut.'<br />';
						//echo $idOut.'<br />';
						$arrayOut[]['funcionario_id'] = $idOut;
					}
					
				}
			}
			/*** ELIMINA PRECALIFICADORES DUPLICADOS QUE PUEDAN ESTAR EN LA DB, ESTO ES PARA TRATAR DE SOLUCIONAR EL F5 CUANDO SE AGREGA PRECALIF... ***/
			// $this->Precalificadore->
			// $this->Precalificadore->recursive=-1;
			$losPrecalificadores = $this->Precalificadore->find('list', array('fields' => array('Precalificadore.funcionario_id')) );
			foreach($arrayIn as $pnt => $listado){
				$varSFlash = '-> '.$pnt.' - '.$listado['funcionario_id'].'<br />';
				$varSFlash .= '--> '.$arrayIn[$pnt]['funcionario_id'].'<br />';
				$varSFlash .= '---> '.in_array($listado['funcionario_id'], $losPrecalificadores).'<br />';
				if(in_array($listado['funcionario_id'], $losPrecalificadores)){
					unset($arrayIn[$pnt]);
				}
			}
			
			$valorsIn = array();
			foreach($arrayOut as $lista){
				$valorsIn[] = $lista['funcionario_id'];
			}
			
			$conditions = array('Precalificadore.funcionario_id'=> $valorsIn);
			
			if(0):
				$varSFlash .= 'arrayIn: <pre>'.print_r($arrayIn, 1).'</pre><hr>'
				.'arrayOut: <pre>'.print_r($arrayOut, 1).'</pre><hr>'
				.'valorsIn: <pre>'.print_r($valorsIn, 1).'</pre><hr>'
				.'conditions: <pre>'.print_r($conditions, 1).'</pre><hr>'
				.'losPrecalificadores: <pre>'.print_r($losPrecalificadores, 1).'</pre><hr>';
				//echo 'arrayIn: <pre>'.print_r($arrayIn, 1).'</pre><hr>';
				$this->Session->setFlash($varSFlash);
				//echo '$this->data: <pre>'.print_r($this->data, 1).'</pre><hr>';
			else:
				$varIn=0;
				$varOut=0;
				if(count($arrayIn)>0){
					$this->Precalificadore->create();
					if( $this->Precalificadore->saveAll($arrayIn) ){
						$varIn=1;
					}
				}
				//echo 'arrayOut: <pre>'.print_r($arrayOut, 1).'</pre><hr>'.count($arrayIn);
				if(count($arrayOut)>0){
					if( $this->Precalificadore->deleteAll($conditions, false) ){
						$varOut=1;
					}else{$this->Session->setFlash(__('Ocurrio un Error:<br>'.print_r(error_get_last(),true), true)); break;}
				}
				if( $varIn==1 && $varOut==0 ){
					$this->Session->setFlash('Agregado');
				}else if( $varIn==0 && $varOut==1 ){
					$this->Session->setFlash('Eliminado');
				}else if( $varIn==1 && $varOut==1 ){
					$this->Session->setFlash('Cambios Listos');
				}else{
					$this->Session->setFlash('Sin cambios');
				}
			endif;
		} /*** FIN SECCION QUE GUARDA/ELIMINA ***/
		//$this->Precalificadore->recursive =1;
		$preCalificadores = $this->Precalificadore->find('all');
		//$preCalificadores = $this->Persona->Precalificadore->find('all', array( 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') ) );
		////$preCalificadores = $this->Precalificadore->Persona->find('all', array( 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT') ));
		////echo 'preCalificadores: <pre>'.print_r($preCalificadores, 1).'</pre><hr>';
		$this->Persona->recursive = 0;
		$funcionarios = $this->Persona->find('all', 
			array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')', 'PersonaEstado.per_estado = 1')
			, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')));
		////echo 'funcionarios: <pre>'.print_r($funcionarios, 1).'</pre><hr>';
		
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$elPeriodo = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
		else: /*** POR SUBPERIODO ***/
			$elPeriodo = $this->Subperiodo->find('all', 
				/*
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesdesde AND Subperiodo.meshasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
				*/
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND Subperiodo.mesevalhasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		
		$this->set(compact('preCalificadores', 'funcionarios', 'elPeriodo'));
	}
	
}
