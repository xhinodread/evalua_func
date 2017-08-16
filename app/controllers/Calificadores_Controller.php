<?
class CalificadoresController extends AppController{
	public $uses = array('Calificadore', 'Persona', 'Historia', 'Periodo', 'Subperiodo');
	var $helpers = array('Html', 'Form');
	var $componets = array('Session', 'Auth');
	
	public function beforeFilter(){ parent::beforeFilter(); }
	
	public function index(){
		/*if($this->data){
			echo '$this->data: <pre>'.print_r($this->data, 1).'</pre><hr>';
		}*/
		if($this->data){
			$arrayIn = array();
			$arrayOut = array();
			foreach($this->data['Calificadores'] as $ind => $valorChks){
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
			//echo 'arrayIn: <pre>'.print_r($arrayIn, 1).'</pre><hr>';
			//echo 'arrayOut: <pre>'.print_r($arrayOut, 1).'</pre><hr>'.count($arrayIn);
			//echo '$this->data: <pre>'.print_r($this->data, 1).'</pre><hr>';
	  if(1):
			$varIn=0;
			$varOut=0;
			if(count($arrayIn)>0){
				$this->Calificadore->create();
				if( $this->Calificadore->saveAll($arrayIn) ){
					$varIn=1;
				}
			}
			//echo 'arrayOut: <pre>'.print_r($arrayOut, 1).'</pre><hr>'.count($arrayIn);
			if(count($arrayOut)>0){
				if( $this->Calificadore->deleteAll($arrayOut) ){
					$varOut=1;
				}else{$this->Session->setFlash(__('Opps!!!<br>'.print_r(error_get_last(),true), true)); break;}
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
		/****************************************/
		
		//$this->Precalificadore->recursive =1;
		$calificadores = $this->Calificadore->find('all');
		/*
		$this->Historia->Persona->recursive = 0;
		$funcionarios = $this->Historia->Persona->find('all', 
			array('conditions' => array('PersonaEstado.calidadJuridica in (1, 2)', 'Persona.id_per = 6')
			, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')));
		*/		
		/*** COMENTADO TEMPORALMENTE ***/
		$this->Persona->recursive = 0;
		$funcionarios = $this->Persona->find('all', 
			array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')', 'PersonaEstado.per_estado = 1' )
			, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')));			
		//echo 'funcionarios: <pre>'.print_r($funcionarios, 1).'</pre><hr>';
		foreach($funcionarios as $pntF => $listaFunc){
			///echo 'listaFunc: <pre>'.print_r($listaFunc['Persona'], 1).'</pre><hr>';
			$this->Historia->recursive = -1;
			$historia = $this->Historia->find('all', array('conditions'=> 'Historia.ID_PER = '.$listaFunc['Persona']['ID_PER'],
														   'order'=> 'Historia.FEC_DESDE DESC') );
			//echo 'historia: <pre>'.print_r($historia, 1).'</pre><hr>';
			foreach($historia as $listaHistoria){
				//echo 'listaHistoria: <pre>'.print_r($listaHistoria, 1).'</pre><hr>';
				if($listaHistoria['Historia']['COD_GRADO']){
					$funcionarios[$pntF]['Persona']['COD_GRADO']=$listaHistoria['Historia']['COD_GRADO'];
					$funcionarios[$pntF]['Persona']['COD_CARGO']=$listaHistoria['Historia']['COD_CARGO'];
					//echo 'akkk';
					break 1;
				}else{
					$funcionarios[$pntF]['Persona']['COD_GRADO']='';
					$funcionarios[$pntF]['Persona']['COD_CARGO']='';
				}
			}
		}
		//echo 'funcionarios2: <pre>'.print_r($funcionarios, 1).'</pre><hr>';
		$grados = $this->Historia->find('all', array('fields'=> 'DISTINCT Historia.COD_CARGO',
													 'conditions'=> 'Historia.COD_CARGO is not null',
													 'order'=> 'Historia.COD_CARGO') );
		//echo 'grados: <pre>'.print_r($grados, 1).'</pre><hr>';
		$listaFuncGrados = array();
		foreach($grados as $listaGrados){
			//echo 'listaGrados: <pre>'.print_r($listaGrados, 1).'</pre><hr>';
			//echo 'Grado: <pre>'.$listaGrados['Historia']['COD_CARGO'].'</pre><hr>';
			foreach($funcionarios as $listaFunc){
				if(isset($listaFunc['Persona']['COD_GRADO'])){
					if($listaFunc['Persona']['COD_GRADO'] == $listaGrados['Historia']['COD_CARGO']){
						$listaFuncGrados[] =$listaFunc;
					}
				}
			}		
		}
		//echo 'listaFuncGrados: <pre>'.print_r($listaFuncGrados, 1).'</pre><hr>';
		/**********/
		$this->Persona->recursive = 0;
		$funcionarios = $this->Persona->find('all', 
			array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')', 'PersonaEstado.per_estado = 1' )
			, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')));
		
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
		//echo 'listaFuncGrados: <pre>'.print_r($listaFuncGrados, 1).'</pre><hr>';
				
		$this->set(compact('calificadores', 'listaFuncGrados', 'funcionarios', 'elPeriodo'));
	}
	
}
