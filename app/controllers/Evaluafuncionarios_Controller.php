<?
App::import('Vendor', 'phpmailer', array('file' => 'phpmailer'.DS.'PHPMailerAutoload.php'));
App::import('Vendor', 'Funcionespropias');

class EvaluafuncionariosController extends AppController{
	public $uses = array('Evaluafuncionario', 'Calificafuncionario', 'Justificacionsubperiodo', 'Notasubfactor', 'Persona', 'Personaimagen'
	, 'Periodo', 'Subperiodo', 'Factor', 'Subfactor', 'PreguntaValor', 'PreguntaValornota', 'Item', 'User', 'PersonaEstado', 'Historia', 'Chknota'
	, 'Calificacionfuncionario', 'Validaaceptaevaluac', 'FirmasHojacalifica', 'Precalificadore', 'FactorCalificacion');
	var $helpers = array('Html', 'Form');
	var $components = array('Email');
	//var $components = array('Auth');
	public $colorConcepto = array(2=>'#EAF1DD', 4=>'#D6E3BC', 6=>'#C2D69B', 8=>'#92D050', 10=>'#00B050');
	public $colorConceptoPdf = array(
									2=>array('r'=>234, 'g'=>241, 'b'=>221),
									4=>array('r'=>214, 'g'=>227, 'b'=>188),
									6=>array('r'=>194, 'g'=>214, 'b'=>155),
									8=>array('r'=>146, 'g'=>208, 'b'=>80),
									10=>array('r'=>0, 'g'=>176, 'b'=>80)
								);

	public $arrayEscalfones = array(
								 1=>'Directivo'
								,3=>'Profesional'
								,4=>'Técnico'
								,5=>'Administrativo'
								,6=>'Auxiliar'
							);
	
	public function beforeFilter(){ parent::beforeFilter(); }
	
	//var $scaffold;
	public function index(){}
	
	public function precalificacion(){
		$Funcionespropias = new Funcionespropias();
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(m, 2, Subperiodo.mesevalhasta)')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		// echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$periodoId = $periodoEvaluados['Periodo']['id'];
		$periodoNombre = $periodoEvaluados['Periodo']['etiqueta'];
		$subPerId = $periodoEvaluados['Subperiodo']['id'];
		$subPeriodoNombre = $periodoEvaluados['Subperiodo']['etiqueta'];
		
		$arraySubperiodos = $this->Subperiodo->find('list', array('conditions' => 'Subperiodo.periodo_id = '.$periodoId
																,'fields' => array('Subperiodo.id') ) );
		$arraySubperiodosNombres = $this->Subperiodo->find('list', array('conditions' => 'Subperiodo.periodo_id = '.$periodoId
																,'fields' => array('Subperiodo.id', 'Subperiodo.etiqueta') ) );

		$datosSession = $this->viewVars;
		//echo '<br />datosSession:<pre>'.print_r($datosSession,1).'</pre>';
		$idPer = $datosSession['idPer'];
		/*** SOLO PARA DESARROLLO ***/
		 if($idPer == 444)$idPer = 391; //38; // 575; // 34; // 47; //166; //   
		 // if($idPer == 6)$idPer = 391;
		
		$this->Persona->recursive = -1;
		$condiciones = array('Persona.ID_PER' => $idPer);
		$datosUserFuncionario = $this->Persona->find('first', array('conditions'=> $condiciones ) );
		// echo 'datosUserFuncionario:<pre>'.print_r($datosUserFuncionario,1).'</pre>';
		//echo 'params:<pre>'.$periodoId.", ".$idPer.'</pre>';
		// $this->Notasubfactor->recursive = -1;
		$condiciones = array('conditions'=> array('Notasubfactor.periodo_id = '.$periodoId.' '
												, 'Notasubfactor.funcionario_id = '.$idPer.' ') );
		$listaNotasubfactor = $this->Notasubfactor->find('all', $condiciones);
		$lstNotas=array();
		if( count($listaNotasubfactor) <= 0){
			$listaNotasubfactor = $this->Evaluafuncionario->traePrecalificacionDelPeriodo($idPer, $periodoId, $subPerId);
			$arrayFactores = array_unique($Funcionespropias->arrayIn($listaNotasubfactor, 0, 'factore_id'));
			foreach($listaNotasubfactor as $lst){
				$lstNotas[$lst[0]['subfactore_id']] = $lst[0]['nota'];
			}
		}else{
			$arrayFactores = array_unique($Funcionespropias->arrayIn($listaNotasubfactor, 'Subfactor', 'factore_id'));
			foreach($listaNotasubfactor as $lst){
				$lstNotas[$lst['Notasubfactor']['subfactore_id']] = $lst['Notasubfactor']['nota'];
			}
		}
		// echo 'listaNotasubfactor:<pre>'.print_r($listaNotasubfactor,1).'</pre>';
		// echo 'arrayFactores:<pre>'.print_r($arrayFactores,1).'</pre>';
		// echo 'count:<pre>'.count($listaNotasubfactor).'</pre>';
		// echo 'subPerId:<pre>'.($subPerId).'</pre>';		
		// echo 'lstNotas:<pre>'.print_r($lstNotas,1).'</pre>';
		
		$options = array('conditions' => array('Factor.id' => $arrayFactores ) );
		$listaFactor = $this->Factor->find('all', $options );
		/***/
		$this->Justificacionsubperiodo->recursive = -1;
		$optionJustFunc = array('conditions'=>array('Justificacionsubperiodo.subperiodo_id' => $arraySubperiodos ,
													'Justificacionsubperiodo.funcionario_id' => $idPer)
								,'order'=> array('Justificacionsubperiodo.subfactore_id', 'Justificacionsubperiodo.subperiodo_id') );
		$listaJustFuncTmp = $this->Justificacionsubperiodo->find('all', $optionJustFunc );
		// echo 'listaJustFuncTmp:<pre>'.print_r($listaJustFuncTmp,1).'</pre>';
		/***/
		$this->set(compact('datosUserFuncionario', 'periodoNombre', 'subPeriodoNombre', 'subPerId', 'listaFactor', 'lstNotas', 'listaJustFuncTmp', 'arraySubperiodosNombres'));
	}
		
	public function precalificacionParcial(){
		$Funcionespropias = new Funcionespropias();
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(m, 2, Subperiodo.mesevalhasta)')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		// echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$periodoId = $periodoEvaluados['Periodo']['id'];
		$periodoNombre = $periodoEvaluados['Periodo']['etiqueta'];
		$subPerId = $periodoEvaluados['Subperiodo']['id'];
		$subPeriodoNombre = $periodoEvaluados['Subperiodo']['etiqueta'];
		
		$arraySubperiodos = $this->Subperiodo->find('list', array('conditions' => 'Subperiodo.periodo_id = '.$periodoId
																,'fields' => array('Subperiodo.id') ) );
		$arraySubperiodosNombres = $this->Subperiodo->find('list', array('conditions' => 'Subperiodo.periodo_id = '.$periodoId
																,'fields' => array('Subperiodo.id', 'Subperiodo.etiqueta') ) );

		$datosSession = $this->viewVars;
		//echo '<br />datosSession:<pre>'.print_r($datosSession,1).'</pre>';
		$idPer = $datosSession['idPer'];
		/*** SOLO PARA DESARROLLO ***/
		// if($idPer == 444)$idPer = 391; //38; // 575; // 34; // 47; //166; //   
		 // if($idPer == 6)$idPer = 391;
		
		$this->Persona->recursive = -1;
		$condiciones = array('Persona.ID_PER' => $idPer);
		$datosUserFuncionario = $this->Persona->find('first', array('conditions'=> $condiciones ) );
		// echo 'datosUserFuncionario:<pre>'.print_r($datosUserFuncionario,1).'</pre>';
		//echo 'params:<pre>'.$periodoId.", ".$idPer.'</pre>';
		// $this->Notasubfactor->recursive = -1;
		$condiciones = array('conditions'=> array('Notasubfactor.periodo_id = '.$periodoId.' '
												, 'Notasubfactor.funcionario_id = '.$idPer.' ') );
		$listaNotasubfactor = $this->Notasubfactor->find('all', $condiciones);
		$lstNotas=array();
		if( count($listaNotasubfactor) <= 0){
			$listaNotasubfactor = $this->Evaluafuncionario->traePrecalificacionDelPeriodo($idPer, $periodoId, $subPerId);
			$arrayFactores = array_unique($Funcionespropias->arrayIn($listaNotasubfactor, 0, 'factore_id'));
			foreach($listaNotasubfactor as $lst){
				$lstNotas[$lst[0]['subfactore_id']] = $lst[0]['nota'];
			}
		}else{
			$arrayFactores = array_unique($Funcionespropias->arrayIn($listaNotasubfactor, 'Subfactor', 'factore_id'));
			foreach($listaNotasubfactor as $lst){
				$lstNotas[$lst['Notasubfactor']['subfactore_id']] = $lst['Notasubfactor']['nota'];
			}
		}
		// echo 'listaNotasubfactor:<pre>'.print_r($listaNotasubfactor,1).'</pre>';
		// echo 'arrayFactores:<pre>'.print_r($arrayFactores,1).'</pre>';
		// echo 'count:<pre>'.count($listaNotasubfactor).'</pre>';
		// echo 'subPerId:<pre>'.($subPerId).'</pre>';		
		// echo 'lstNotas:<pre>'.print_r($lstNotas,1).'</pre>';
		
		$options = array('conditions' => array('Factor.id' => $arrayFactores ) );
		$listaFactor = $this->Factor->find('all', $options );
		/***/
		$this->Justificacionsubperiodo->recursive = -1;
		$optionJustFunc = array('conditions'=>array('Justificacionsubperiodo.subperiodo_id' => $arraySubperiodos ,
													'Justificacionsubperiodo.funcionario_id' => $idPer)
								,'order'=> array('Justificacionsubperiodo.subfactore_id', 'Justificacionsubperiodo.subperiodo_id') );
		$listaJustFuncTmp = $this->Justificacionsubperiodo->find('all', $optionJustFunc );
		// echo 'listaJustFuncTmp:<pre>'.print_r($listaJustFuncTmp,1).'</pre>';
		/***/
		$this->set(compact('datosUserFuncionario', 'periodoNombre', 'subPeriodoNombre', 'subPerId', 'listaFactor', 'lstNotas', 'listaJustFuncTmp', 'arraySubperiodosNombres'));
	}		
		
	public function precalificacionMantenedor($idFunc = null){
		$Funcionespropias = new Funcionespropias();
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(m, 1, Subperiodo.mesevalhasta)')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		$periodoId = $periodoEvaluados['Periodo']['id'];
		$periodoNombre = $periodoEvaluados['Periodo']['etiqueta'];
		$subPerId = $periodoEvaluados['Subperiodo']['id'];
		$subPeriodoId = $subPerId;
		$subPeriodoNombre = $periodoEvaluados['Subperiodo']['etiqueta'];
		
		$arraySubperiodos = $this->Subperiodo->find('list', array('conditions' => 'Subperiodo.periodo_id = '.$periodoId
																,'fields' => array('Subperiodo.id') ) );
		$arraySubperiodosNombres = $this->Subperiodo->find('list', array('conditions' => 'Subperiodo.periodo_id = '.$periodoId
																,'fields' => array('Subperiodo.id', 'Subperiodo.etiqueta') ) );
		
		$datosSession = $this->viewVars;
		$idPer = $datosSession['idPer'];
		/*** SOLO PARA DESARROLLO ***/
		// if($idPer == 444)$idPer = 676; // 166; //  $idPer = 47;
		 $idPer = $idFunc;
		
		$this->Persona->recursive = -1;
		$condiciones = array('Persona.ID_PER' => $idPer);
		$datosUserFuncionario = $this->Persona->find('first', array('conditions'=> $condiciones ) );
		
		// $this->Notasubfactor->recursive = -1;
		$condiciones = array('conditions'=> array('Notasubfactor.periodo_id = '.$periodoId.' '
												, 'Notasubfactor.funcionario_id = '.$idPer.' ') );
		$listaNotasubfactor = $this->Notasubfactor->find('all', $condiciones);
		$arrayFactores = array_unique($Funcionespropias->arrayIn($listaNotasubfactor, 'Subfactor', 'factore_id'));
		
		$lstNotas=array();
		foreach($listaNotasubfactor as $lst){
			$lstNotas[$lst['Notasubfactor']['subfactore_id']] = $lst['Notasubfactor']['nota'];
		}
		
		$options = array('conditions' => array('Factor.id' => $arrayFactores ) );
		$listaFactor = $this->Factor->find('all', $options );
		
		/***/
		$this->Justificacionsubperiodo->recursive = -1;
		$optionJustFunc = array('conditions'=>array('Justificacionsubperiodo.subperiodo_id' => $arraySubperiodos ,
													'Justificacionsubperiodo.funcionario_id' => $idPer)
								,'order'=>'Justificacionsubperiodo.subfactore_id');
		$listaJustFuncTmp = $this->Justificacionsubperiodo->find('all', $optionJustFunc );
		/***/
		$condiciones= array('conditions'=>array('Validaaceptaevaluac.funcionario_id = '.$idPer.' '
												,'Validaaceptaevaluac.subperiodo_id = '.$subPeriodoId.' ')
					);
		$datosValidaEvalucion = $this->Validaaceptaevaluac->find('first', $condiciones);
		$nroReg = count($datosValidaEvalucion['Validaaceptaevaluac']);
		
		$this->set(compact('datosUserFuncionario', 'periodoNombre', 'subPeriodoNombre', 'subPerId', 'listaFactor', 'lstNotas', 'listaJustFuncTmp', 'arraySubperiodosNombres', 'datosValidaEvalucion', 'nroReg'));
	}	
		
	public function informeprecalificacion(){
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND Subperiodo.mesevalhasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
			// mesevaldesde	mesevalhasta
		endif;
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		/*** NO SE ESTA USANDO EN ESTE METODO *** /
		$datosSession = $this->Session->read('personaDatos');
		$idPer = $datosSession['PersonaEstado']['id_per'];
		$rutPer = $datosSession['PersonaEstado']['per_rut'];
		////echo 'datosSession:<pre>'.print_r($datosSession,1).'</pre>';
		*/
		$listaFactor = $this->Factor->find('all');
		//echo 'listaFactor:<pre>'.print_r($listaFactor,1).'</pre>';
		
		$idPer = $this->data['Evaluafuncionarios']['funcionario_id'];
		/*** SOLO PARA DESARROLLO ***/
		//if($idPer == 444)$idPer=1;
		$this->Notasubfactor->recursive=-1;
		$condiciones= array('conditions'=> array('Notasubfactor.periodo_id = '.$perId.' '
												,'Notasubfactor.funcionario_id = '.$idPer.' ') );
		$listaNotasubfactor = $this->Notasubfactor->find('all', $condiciones);
		//echo 'listaNotasubfactor:<pre>'.print_r($listaNotasubfactor,1).'</pre>';
		
		$lstNotas=array();
		foreach($listaNotasubfactor as $lst){
			$lstNotas[$lst['Notasubfactor']['subfactore_id']] = $lst['Notasubfactor']['nota'];
		}
		//echo 'lstNotas:<pre>'.print_r($lstNotas,1).'</pre>';
		
		$this->Persona->recursive=-1;
		$condiciones = array('conditions'=> array('Persona.id_per'=>$idPer) );
		$arrayCero = $this->Persona->find('first', $condiciones);
		//echo 'arrayCero:<pre>'.print_r($arrayCero,1).'</pre>';
		$nombreFuncionario = $arrayCero['Persona']['NOMBRES'].' '.$arrayCero['Persona']['AP_PAT'].' '.$arrayCero['Persona']['AP_MAT'];
		
		$laData = $this->data;
		$this->set(compact('laData', 'nombreFuncionario', 'perNombre', 'subPerNombre', 'subPerId', 'listaFactor', 'lstNotas'));
	}
	
	public function informeprecalificacionPdf(){
		$Funcionespropias = new Funcionespropias();
		Configure::write('debug',0);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
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

		if( !isset($periodoEvaluados['Subperiodo']) && !isset($periodoEvaluados['Periodo']) ){
			$msgAlerta = "No existe rango de fecha para Precalificar.";
			echo '<script type="text/javascript">  alert("'.$msgAlerta.'"); this.close(); </script> ';
		}
		
		$perId = $periodoEvaluados['Periodo']['id'];
		$periodoId = $perId;
		$perNombre = $periodoEvaluados['Periodo']['etiqueta'];
		$subPerId = $periodoEvaluados['Subperiodo']['id'];
		$subPerNombre = $periodoEvaluados['Subperiodo']['etiqueta'];	
		$idPer = $this->data['Evaluafuncionarios']['funcionario_id'];
		
		$this->Subperiodo->recursive = -1;
		$options = array('conditions' => array('Subperiodo.periodo_id' => $periodoId), 'fields' => array('Subperiodo.id', 'Subperiodo.etiqueta') );
		$listaPeriodos = $this->Subperiodo->find('all', $options);

		$stringSubPeriodos = array();
		foreach($listaPeriodos as $lst){
			$stringSubPeriodos[] = $lst['Subperiodo']['id'];
		}
		
		$arraySubperiodos = $Funcionespropias->arrayInPuntero($listaPeriodos, 'id', 'Subperiodo', 'etiqueta');
				
		$options = array('conditions' => array("Factor.etiqueta not like '%direcc%'") );
		if($this->Evaluafuncionario->tieneAsignadoDireccion($idPer, $perId)){ 
			$options = array(); 
		}
		$listaFactor = $this->Factor->find('all', $options);
				
		$this->Notasubfactor->recursive=-1;
		$condiciones= array('conditions'=> array('Notasubfactor.periodo_id = '.$perId.' '
												,'Notasubfactor.funcionario_id = '.$idPer.' ') );
		$listaNotasubfactor = $this->Notasubfactor->find('all', $condiciones);
		$lstNotas=array();
		foreach($listaNotasubfactor as $lst){
			$lstNotas[$lst['Notasubfactor']['subfactore_id']] = $lst['Notasubfactor']['nota'];
		}

		$promedioNotas = $this->Evaluafuncionario->promedioPrecalificacion($idPer, $perId);
		$lstPromedioNotas = array();
		foreach($promedioNotas as $lst){
			$lstPromedioNotas[$lst[0]['factore_id']] = array('sumaNota' => $lst[0]['sumaNota'], 'nroNotas' => $lst[0]['nroNotas']);
		}
		
		$this->Justificacionsubperiodo->recursive = -1;
		$optionJustFunc = array('conditions'=>array('Justificacionsubperiodo.subperiodo_id IN ('.implode(',',$stringSubPeriodos).')' ,
													'Justificacionsubperiodo.funcionario_id'=>$idPer)
								,'order'=>'Justificacionsubperiodo.subfactore_id') ;
		$listaJustFuncTmp = $this->Justificacionsubperiodo->find('all', $optionJustFunc );
		
		$this->Persona->recursive=-1;
		$condiciones = array('conditions'=> array('Persona.id_per'=>$idPer) );
		$arrayCero = $this->Persona->find('first', $condiciones);
		$nombreFuncionario = $arrayCero['Persona']['NOMBRES'].' '.$arrayCero['Persona']['AP_PAT'].' '.$arrayCero['Persona']['AP_MAT'];
		
		$this->set('nombreFuncionario', $nombreFuncionario);
		$this->set('perNombre', $perNombre);
		$this->set('subPerNombre', $subPerNombre);
		$this->set('arraySubperiodos', $arraySubperiodos);
		$this->set('subPerId', $subPerId);
		$this->set('listaFactor', $listaFactor);
		$this->set('lstNotas', $lstNotas);
		$this->set('lstPromedioNotas', $lstPromedioNotas);
		$this->set('listaJustFuncTmp', $listaJustFuncTmp);
		$this->render();
	}
	
	public function hojaDeCalificacionPdf($funcionario_id = null){		
		$Funcionespropias = new Funcionespropias();
		Configure::write('debug', 0);
		$this->layout = 'pdf';
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => /*array(' getdate() BETWEEN Subperiodo.mesdesde AND Subperiodo.meshasta')*/
					array(' getdate() BETWEEN Subperiodo.mesevaldesde AND DATEADD(m, 2, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;

		if( !isset($periodoEvaluados['Subperiodo']) && !isset($periodoEvaluados['Periodo']) ){
			$msgAlerta = "No existe rango de fecha para Precalificar.";
			echo '<script type="text/javascript">  alert("'.$msgAlerta.'"); this.close(); </script> ';
		}
		
		$perId = $periodoEvaluados['Periodo']['id'];
		$periodoId = $perId;
		$perNombre = $periodoEvaluados['Periodo']['etiqueta'];
		$subPerId = $periodoEvaluados['Subperiodo']['id'];
		$subPerNombre = $periodoEvaluados['Subperiodo']['etiqueta'];	
		$idPer = $this->data['Evaluafuncionarios']['funcionario_id'];
		
		$this->Subperiodo->recursive = -1;
		$options = array('conditions' => array('Subperiodo.periodo_id' => $periodoId), 'fields' => array('Subperiodo.id', 'Subperiodo.etiqueta') );
		$listaPeriodos = $this->Subperiodo->find('all', $options);

		$stringSubPeriodos = array();
		foreach($listaPeriodos as $lst){
			$stringSubPeriodos[] = $lst['Subperiodo']['id'];
		}
		
		$arraySubperiodos = $Funcionespropias->arrayInPuntero($listaPeriodos, 'id', 'Subperiodo', 'etiqueta');
		
		$options = array('conditions' => array("Factor.etiqueta not like '%direcc%'") );
		if($this->Evaluafuncionario->tieneAsignadoDireccion($funcionario_id, $perId)){ 
			$options = array(); 
		}
		$listaFactor = $this->Factor->find('all', $options);
				
		$this->Notasubfactor->recursive=-1;
		$condiciones= array('conditions'=> array('Notasubfactor.periodo_id = '.$perId.' '
												,'Notasubfactor.funcionario_id = '.$funcionario_id.' ') );
		$listaNotasubfactor = $this->Notasubfactor->find('all', $condiciones);
		
		$this->Calificacionfuncionario->recursive=-1;
		$calificacionFuncionarios = $this->Calificacionfuncionario->find('all', array( 'conditions' =>
																		array('Calificacionfuncionario.periodo_id = '.$perId.' '
																				,'Calificacionfuncionario.funcionario_id in ('.$funcionario_id.') '																		
																		)
																)
														);
		$lstNotas=array();
		foreach($calificacionFuncionarios as $lst){
			$lstNotas[$lst['Calificacionfuncionario']['subfactore_id']] = $lst['Calificacionfuncionario']['nota'];
		}

		$promedioNotas = $this->Evaluafuncionario->promedioCalificacion($funcionario_id, $perId);
		$lstPromedioNotas = array();
		foreach($promedioNotas as $lst){
			$lstPromedioNotas[$lst[0]['factore_id']] = array('sumaNota' => $lst[0]['sumaNota'], 'nroNotas' => $lst[0]['nroNotas']);
		}
		
		$this->Justificacionsubperiodo->recursive = -1;
		$optionJustFunc = array('conditions'=>array('Justificacionsubperiodo.subperiodo_id IN ('.implode(',',$stringSubPeriodos).')' ,
													'Justificacionsubperiodo.funcionario_id' => $funcionario_id)
								, 'order'=>'Justificacionsubperiodo.subfactore_id') ;
		$listaJustFuncTmp = $this->Justificacionsubperiodo->find('all', $optionJustFunc );
		
		$this->Persona->recursive=0;
		$this->Persona->bindModel(array('hasOne' =>  array('FirmasHojacalifica' => array('className' => 'FirmasHojacalifica'
																						, 'foreignKey' => 'funcionario_id'))) );
		$this->Persona->primaryKey = 'ID_PER';
		$condiciones = array('conditions'=> array('Persona.id_per'=>$funcionario_id) );
		$arrayCero = $this->Persona->find('first', $condiciones);		
		$nombreFuncionario = utf8_encode($arrayCero['Persona']['NOMBRES'].' '.$arrayCero['Persona']['AP_PAT'].' '.$arrayCero['Persona']['AP_MAT']);
		//echo 'nombreFuncionario<pre>'.print_r($nombreFuncionario, 1).'</pre><hr>';
		
		unset($arrayCero['FirmasHojacalifica']['id']
			, $arrayCero['FirmasHojacalifica']['funcionario_id']
			, $arrayCero['FirmasHojacalifica']['created'] );
		$firmasIn = implode(',', $arrayCero['FirmasHojacalifica']);
		$this->Persona->recursive = -1;
		$listaFirmantes = $this->Persona->find('all', array('conditions' => array('Persona.ID_PER in ('.($firmasIn).')' )) );
		$nombreFirmantes = array();
		foreach($listaFirmantes as $lista){
			$nombreFirmantes[$lista['Persona']['ID_PER']] = $lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT'];
		}
		
		$historiaFuncionario = $this->Historia->traeFuncionariosHistoria($funcionario_id);
		$codCargo = $historiaFuncionario[0][0]['COD_CARGO'];
		$listaCoeficientesTmp = $this->FactorCalificacion->find('all', array('conditions' => array('FactorCalificacion.cargo_id' => $codCargo)) );
		$listaCoeficientes = $Funcionespropias->arrayInPuntero($listaCoeficientesTmp, 'factore_id', 'FactorCalificacion', 'valor');


		$this->set('nombreFuncionario', $nombreFuncionario);
		$this->set('nombreFirmantes', $nombreFirmantes);
		$this->set('listaFirmantes', $arrayCero['FirmasHojacalifica']);
		$this->set('perNombre', $perNombre);
		$this->set('subPerNombre', $subPerNombre);
		$this->set('arraySubperiodos', $arraySubperiodos);
		$this->set('subPerId', $subPerId);
		$this->set('listaFactor', $listaFactor);
		$this->set('lstNotas', $lstNotas);
		$this->set('lstPromedioNotas', $lstPromedioNotas);
		$this->set('listaJustFuncTmp', $listaJustFuncTmp);
		$this->set('listaCoeficientes', $listaCoeficientes);

		$this->render();
	}	
	
	public function calificacion($idEscalafon){
		
		$precalifId = 0;
		if( isset($this->params['named']['precalifId']) ){
			//echo 'data <pre>'.print_r($this->params['named'], 1).'</pre>';
			//echo 'data <pre>'.print_r(isset($this->params['named']['precalifId']), 1).'</pre>';
			$precalifId = $this->params['named']['precalifId'];
		}

		$Funcionespropias = new Funcionespropias();
		
		switch ($idEscalafon){
			case 1:$escalafones ='1, 8, 9';break;
			case 3:$escalafones ='3';break;
			case 4:$escalafones ='4';break;
			case 5:$escalafones ='5';break;
			case 6:$escalafones ='6';break;
		}
		$nombreEscalafon = $this->arrayEscalfones[$idEscalafon];
		
		/******************************/
		/***** SECCION QUE GUARDA *****/
		/******************************/
		if( !empty($this->data) ){
			//echo 'data0 <pre>'.print_r($this->data, 1).'</pre>';
			$chkSNota= $this->data['chkNota'];
			$idRegs= $this->data['Evaluafuncionario']['id'];
			unset($this->data['chkNota']);
			$perId = $this->data['Evaluafuncionario']['perId'];
			unset($this->data['Evaluafuncionario']);
			$califFunc = $this->data['califFunc'];
			unset($this->data['califFunc']);
			$idCalifFunc= $this->data['califFuncId'];
			unset($this->data['califFuncId']);
			
			$arrayNotasubfactor=array();
			foreach($this->data as $pnt => $listaNotas){
				$posGuion = strpos($pnt, '_');
				$varFuncId = substr($pnt, 0,$posGuion);
				$varSubfId = substr($pnt, $posGuion+1);
				$arrayNotasubfactor[]['Notasubfactor']= array('id'=>$idRegs[$pnt]
															,'periodo_id'=>$perId
															,'subfactore_id'=>$varSubfId
															,'funcionario_id'=>$varFuncId
															,'nota' => $listaNotas
														 ); 
			}
			
			$arrayCalifFunc=array();
			foreach($califFunc as $pnt => $listaNotas){
				$posGuion = strpos($pnt, '_');
				$varFuncId = substr($pnt, 0,$posGuion);
				$varSubfId = substr($pnt, $posGuion+1);
				$arrayCalifFunc[]['Calificacionfuncionario']= array('id'=>$idCalifFunc[$pnt]
															,'periodo_id'=>$perId
															,'subfactore_id'=>$varSubfId
															,'funcionario_id'=>$varFuncId
															,'nota' => $listaNotas
														 ); 
			}
			
			/*** Llena array para el 'select in' ***/
			$arrayFuncs=array();
			foreach($this->data as $pnt => $listaFuncs){
				$posGuion = strpos($pnt, '_');
				$varFunc = substr($pnt, 0,$posGuion);
				$arrayFuncs[]=$varFunc;
			}
			/*** Elimina duplicados ***/
			$resultado = array_unique($arrayFuncs);
			//echo 'arrayFuncs<pre>'.print_r($resultado, 1).'</pre>';
			$idPersBorrar = '';
			/****************************/
			/*** Crea valores del in  ***/
			/****************************/
			foreach($resultado as $listaFuncId)$idPersBorrar .= $listaFuncId.', ';
			$idPersBorrar=substr($idPersBorrar, 0, strlen($idPersBorrar) -2);
			//echo '* idPersBorrar: '.$idPersBorrar.'<br />';
			$arrayChkNota=array();
			foreach($chkSNota as $pnt => $listaChks){
				//echo '* '.$pnt.', '.$listaChks.'<br />';
				if($listaChks > 0){
					$posGuion = strpos($pnt, '_');
					//echo 'posGuion '.$posGuion.'<br />';
					$varFunc = substr($pnt, 0,$posGuion);
					$varPeriodo = substr($pnt, $posGuion+1);
					//echo 'varFunc: '.$varFunc.', varPeriodo: '.$varPeriodo.'<br />';
					$arrayChkNota[]['Chknota'] = array('funcionario_id'=>$varFunc, 'periodo_id'=>$perId);
				}
			}
			/*** LIMPIA EL ARRAY Y DEJA SOLO LAS NOTAS CON VALORES ***/
			$arrayInDelete = array();
			foreach($arrayCalifFunc as $pnt => $lista){
				if( strlen($lista['Calificacionfuncionario']['nota']) == 0 ){
					unset($arrayCalifFunc[$pnt]);
				}else{
					// $arrayInDelete[$lista['Calificacionfuncionario']['funcionario_id']] = $lista['Calificacionfuncionario']['funcionario_id'];
					$arrayInDelete[] = $lista['Calificacionfuncionario']['funcionario_id'];
				}
			}
			$arrayInDelete = array_unique($arrayInDelete);
			$arrayInDelete = implode(',', $arrayInDelete);
			// $arrayCondicionDelete = array('conditions' => array('Calificacionfuncionario.funcionario_id' => array($arrayInDelete)) );
			$arrayCondicionDelete = 'Calificacionfuncionario.funcionario_id in ('.$arrayInDelete.') ';
			
			$arrayInsert = array();
			foreach($arrayCalifFunc as $pnt => $lista){
				$arrayInsert['Calificacionfuncionario'][$pnt+1] = $lista['Calificacionfuncionario'];
			}
			$arrayCalifFunc = $arrayInsert['Calificacionfuncionario'];
			if(0):
				//echo 'data3 <pre>'.print_r($this->data, 1).'</pre>';
				echo 'arrayCalifFunc<pre>'.print_r($arrayCalifFunc, 1).'</pre>';
				echo 'arrayInDelete<pre>'.print_r($arrayInDelete, 1).'</pre>';
				echo 'arrayCondicionDelete<pre>'.print_r($arrayCondicionDelete, 1).'</pre>';
			else:
				$varSwfBorrado = false;
				$this->Session->setFlash('1) NO SE REALIZARON CAMBIOS');
				if ($this->Chknota->deleteAll( array('Chknota.periodo_id = '.$perId.' ', 'Chknota.funcionario_id in ('.$idPersBorrar.') '), false )){
					$this->Session->setFlash('SIN REGISTRO DE FUNCIONARIOS CALIFICADOS PARA ESTE PERIODO');
					$varSwfBorrado = true;
				}
				$this->Session->setFlash('2) NO SE REALIZARON CAMBIOS EN LAS NOTAS');
				
				$this->Chknota->create();
				if($this->Chknota->saveAll($arrayChkNota)){
					$this->Session->setFlash('3). NO SE REALIZARON CAMBIOS EN LAS NOTAS'.'arrayCalifFunc<pre>'.print_r($arrayCalifFunc, 1).'</pre>');
					
					/*** POTENCIAL FUNCION EliminaItemArray() ***/
					foreach($arrayCalifFunc as $pnt => $listaAdd){
						if( isset($listaAdd['id']) ){
							unset($arrayCalifFunc[$pnt]['id']);
						}
					}
					/***/
					if( $this->Calificacionfuncionario->deleteAll(array($arrayCondicionDelete), false) ){
						if(0):
							$this->Session->setFlash('1) GRABADO<br />');
						else:
						 if( $this->Calificacionfuncionario->saveAll($arrayCalifFunc) ){
							$this->Session->setFlash('GRABADO '.date('H:i:s'));
						}else{
							$this->Session->setFlash('5) NO SE REALIZARON CAMBIOS EN LAS NOTAS');
						}
						endif;
					}else{
						$this->Session->setFlash('6) NO SE REALIZARON CAMBIOS EN LAS NOTAS');
						if( $this->Calificacionfuncionario->saveAll($arrayCalifFunc) ){
							$this->Session->setFlash('GRABADO.. '.date('H:i:s'));
						}
					}
				}else{
					$this->Session->setFlash('4) NO SE REALIZARON CAMBIOS EN LAS NOTAS');
				}
			 endif;
		}
		/******************************/
		/*** FIN SECCION QUE GUARDA ***/
		/******************************/
		
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND DATEADD(m, 2, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		$perId=$periodoEvaluados[0]['Periodo']['id'];
		$perNombre=$periodoEvaluados[0]['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados[0]['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados[0]['Subperiodo']['etiqueta'];
		
		$this->Evaluafuncionario->recursive=0;
		$arrayConditions = array('Evaluafuncionario.subperiodo_id' => $subPerId);
		if( $precalifId > 0 )$arrayConditions = array('Evaluafuncionario.subperiodo_id' => $subPerId, 'Evaluafuncionario.precalificadore_id' => $precalifId);
		$listaDelPeriodo = $this->Evaluafuncionario->find('all', array('conditions' => $arrayConditions
																		  ,'fields' => array('DISTINCT Evaluafuncionario.funcionario_id'
																		  					,'Evaluafuncionario.precalificadore_id'
																							,'Evaluafuncionario.factore_id'
																							,'Evaluafuncionario.subperiodo_id') 
																	  )
																);
		$idPers = '';
		foreach($listaDelPeriodo as $listaFuncId)$idPers .= $listaFuncId['Evaluafuncionario']['funcionario_id'].', ';
		$idPers=substr($idPers, 0, strlen($idPers) -2);
		
		$listaEscalafon = $this->Historia->traeFuncionariosCalificacion($idPers, $escalafones);
		
		$idPers = '';
		foreach($listaEscalafon as $listaFuncId)$idPers .= $listaFuncId[0]['ID_PER'].', ';
		$idPers=substr($idPers, 0, strlen($idPers) -2);
		
		if(strlen($idPers)<=0){
			$listaPersona =array();
			$listaNotasPersona =array();
			$listaFotoPersona0 =array();
		}else{
			$this->Persona->recursive=0;
			$listaPersona = $this->Persona->find('all', array('conditions' => 
																	array('PersonaEstado.calidadJuridica in (1, 2)'
																		 ,'Persona.ID_PER in ('.$idPers.') ')
																		 ,'fields' => array('DISTINCT Persona.NOMBRES'
																							, 'Persona.AP_PAT'
																							, 'Persona.AP_MAT'
																							) 
																		  )
													);
			$listaFotoPersona0 =$this->Persona->find('all', array('conditions' => 
																	array('Personaimagen.id_per in ('.$idPers.') ')
																		 ,'fields' => array('Personaimagen.id_per'
																							, 'Personaimagen.FOTO_PER'
																							) 
																		  )
													);
		}
		$listaFotoPersona=array();
		foreach($listaFotoPersona0 as $listaFotos){
			$listaFotoPersona[$listaFotos['Personaimagen']['id_per']]=$listaFotos['Personaimagen']['FOTO_PER'];
		}
		
		if(strlen($idPers)<=0){
			$listaPersona =array();
			$listaNotasPersona =array();
			$listaFotoPersona0 =array();
		}else{
			$this->Persona->recursive=0;
			$listaPersona = $this->Persona->find('all', array('conditions' => 
																	array('PersonaEstado.calidadJuridica in (1, 2)'
																		 ,'Persona.ID_PER in ('.$idPers.') ')
																		 ,'fields' => array('DISTINCT Persona.NOMBRES'
																							, 'Persona.AP_PAT'
																							, 'Persona.AP_MAT'
																							) 
																		  )
													);
			$listaFotoPersona0 =$this->Persona->find('all', array('conditions' => 
																	array('Personaimagen.id_per in ('.$idPers.') ')
																		 ,'fields' => array('Personaimagen.id_per'
																							, 'Personaimagen.FOTO_PER'
																							) 
																		  )
													);
		}
		$listaFotoPersona=array();
		foreach($listaFotoPersona0 as $listaFotos){
			$listaFotoPersona[$listaFotos['Personaimagen']['id_per']]=$listaFotos['Personaimagen']['FOTO_PER'];
		}
		
		if(strlen($idPers)<=0){
			$notasFuncionarios =array();
			$calificacionFuncionarios =array();
		}else{
			$notasFuncionarios = $this->Notasubfactor->find('all', array( 'conditions' =>
																			array('Notasubfactor.periodo_id = '.$perId.' '
																					,'Notasubfactor.funcionario_id in ('.$idPers.') '																		
																			)
																	)
															);
			$this->Calificacionfuncionario->recursive=-1;
			$calificacionFuncionarios = $this->Calificacionfuncionario->find('all', array( 'conditions' =>
																			array('Calificacionfuncionario.periodo_id = '.$perId.' '
																					,'Calificacionfuncionario.funcionario_id in ('.$idPers.') '																		
																			)
																	)
															);
		}
		$listaCalifFunc =array();
		foreach($calificacionFuncionarios as $listaCalFunc){
			$listaCalifFunc[$listaCalFunc['Calificacionfuncionario']['periodo_id']]
						   [$listaCalFunc['Calificacionfuncionario']['subfactore_id']]
						   [$listaCalFunc['Calificacionfuncionario']['funcionario_id']] = $listaCalFunc['Calificacionfuncionario']['nota'];
						   
			$listaCalifFunc[$listaCalFunc['Calificacionfuncionario']['periodo_id']]
						   [$listaCalFunc['Calificacionfuncionario']['subfactore_id']]
						   ['id'] = $listaCalFunc['Calificacionfuncionario']['id'];
		}
		
		$nroNotas0 = $this->Notasubfactor->find('all', array( 'conditions'=>array('Notasubfactor.periodo_id = '.$perId.' ')
															,'fields'=>array('count(Notasubfactor.funcionario_id) AS cnt'
																			, 'Notasubfactor.funcionario_id AS funcionario_id')
															,'group'=>'Notasubfactor.funcionario_id'
														)
		);
		$cntNotasSubfactor=array();
		foreach($nroNotas0 as $listaNroNotas0){
			$cntNotasSubfactor[$listaNroNotas0[0]['funcionario_id']]=$listaNroNotas0[0]['cnt'];
		}
		$arrayNoJefatura = array(6, 5, 4, 3);
		$totalSubfactor = (in_array($idEscalafon, $arrayNoJefatura) ? $this->Subfactor->find('count') - 4 : $this->Subfactor->find('count') );
		
		$options = array();
		if( $idEscalafon > 1 )$options = array('conditions' => array('Factor.id > 1') );
		$this->Factor->recursive=2;
		$factores = $this->Factor->find('all', $options);
		
		$arrayCntSub= array();
		foreach($factores as $listaFactores){
			$cntSub=0;
			foreach($listaFactores['Subfactor'] as $listaSubfactores)$cntSub++;
			$arrayCntSub[$listaFactores['Factor']['id']]=$cntSub;
		}
		
		$chkSNota = $this->Chknota->find('all'
							, array('conditions'=> 
								array('Chknota.periodo_id = '.$perId.' ')
							)
					);
		$arrayChkNotas= array();
		foreach($chkSNota as $listaChkSNota)$arrayChkNotas[]=$listaChkSNota['Chknota']['funcionario_id'];
		
		$preCalificadores = $this->Precalificadore->find('all');
		$arrayPrecalificadores = $Funcionespropias->arrayInPunteroNombrePersona($preCalificadores);

		
		$this->set(compact('nombreEscalafon', 'perId', 'perNombre', 'subPerNombre', 'idEscalafon', 'factores', 'arrayCntSub', 'listaPersona'
		, 'notasFuncionarios', 'arrayChkNotas', 'cntNotasSubfactor', 'totalSubfactor', 'listaCalifFunc', 'listaFotoPersona', 'precalifId'
		, 'arrayPrecalificadores'));
	}
	
	public function calificacionExcel($idEscalafon){
		
		// $this->autoRender = false;
		// $this->layout='excel';
		
		$precalifId = 0;
		if( isset($this->params['named']['precalifId']) ){
			//echo 'data <pre>'.print_r($this->params['named'], 1).'</pre>';
			//echo 'data <pre>'.print_r(isset($this->params['named']['precalifId']), 1).'</pre>';
			$precalifId = $this->params['named']['precalifId'];
		}

		$Funcionespropias = new Funcionespropias();
		//echo $idEscalafon.'<br />';
		switch ($idEscalafon){
			case 1:$escalafones ='1, 8, 9';break;
			case 3:$escalafones ='3';break;
			case 4:$escalafones ='4';break;
			case 5:$escalafones ='5';break;
			case 6:$escalafones ='6';break;
		}
		//echo $escalafones.'<br />';
		$nombreEscalafon = $this->arrayEscalfones[$idEscalafon];
		
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND DATEADD(m, 2, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		//echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		$perId=$periodoEvaluados[0]['Periodo']['id'];
		$perNombre=$periodoEvaluados[0]['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados[0]['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados[0]['Subperiodo']['etiqueta'];
		
		$this->Evaluafuncionario->recursive=0;
		$arrayConditions = array('Evaluafuncionario.subperiodo_id' => $subPerId);
		if( $precalifId > 0 )$arrayConditions = array('Evaluafuncionario.subperiodo_id' => $subPerId, 'Evaluafuncionario.precalificadore_id' => $precalifId);
		$listaDelPeriodo = $this->Evaluafuncionario->find('all', array('conditions' => $arrayConditions
																	 , 'fields' => array('DISTINCT Evaluafuncionario.funcionario_id'
																		  					,'Evaluafuncionario.precalificadore_id'
																							,'Evaluafuncionario.factore_id'
																							,'Evaluafuncionario.subperiodo_id') 
																	  )
																);
		// echo 'listaDelPeriodo:<pre>'.print_r($listaDelPeriodo, true).'</pre><hr>';
		$idPers = '';
		foreach($listaDelPeriodo as $listaFuncId)$idPers .= $listaFuncId['Evaluafuncionario']['funcionario_id'].', ';
		//echo 'idPers0: '.$idPers.'<br />';
		$idPers=substr($idPers, 0, strlen($idPers) -2);
		//echo strlen($idPers).' idPers: '.$idPers.'<br />';
		//echo 'idPers1: '.$idPers.'<br />';
		
		$listaEscalafon = $this->Historia->traeFuncionariosCalificacion($idPers, $escalafones);
		// echo 'listaEscalafon<pre>'.print_r($listaEscalafon, true).'</pre><hr>';
		
		$idPers = '';
		foreach($listaEscalafon as $listaFuncId)$idPers .= $listaFuncId[0]['ID_PER'].', ';
		$idPers = substr($idPers, 0, strlen($idPers) -2);
		//echo strlen($idPers).' idPers: '.$idPers.'<br />';
		
		if(strlen($idPers)<=0){
			$listaPersona =array();
			$listaNotasPersona =array();
			$listaFotoPersona0 =array();
		}else{
			$this->Persona->recursive=0;
			$listaPersona = $this->Persona->find('all', array('conditions' => 
																	array('PersonaEstado.calidadJuridica in (1, 2)'
																		 ,'Persona.ID_PER in ('.$idPers.') ')
																		 ,'fields' => array('DISTINCT Persona.NOMBRES'
																							, 'Persona.AP_PAT'
																							, 'Persona.AP_MAT'
																							) 
																		  )
													);
			$listaFotoPersona0 =$this->Persona->find('all', array('conditions' => 
																	array('Personaimagen.id_per in ('.$idPers.') ')
																		 ,'fields' => array('Personaimagen.id_per'
																							, 'Personaimagen.FOTO_PER'
																							) 
																		  )
													);
		}
		$listaFotoPersona=array();
		foreach($listaFotoPersona0 as $listaFotos){
			$listaFotoPersona[$listaFotos['Personaimagen']['id_per']]=$listaFotos['Personaimagen']['FOTO_PER'];
		}
		//echo 'listaFotoPersona<pre>'.print_r($listaFotoPersona,1).'</pre>';
		
		if(strlen($idPers)<=0){
			$notasFuncionarios =array();
			$calificacionFuncionarios =array();
		}else{
			//echo strlen($idPers).' idPers 2 : '.$idPers.'<br />';
			$notasFuncionarios = $this->Notasubfactor->find('all', array( 'conditions' =>
																			array('Notasubfactor.periodo_id = '.$perId.' '
																					,'Notasubfactor.funcionario_id in ('.$idPers.') '																		
																			)
																	)
															);
			
			//echo 'notasFuncionarios<pre>'.print_r($notasFuncionarios,1).'</pre>';
			
			$this->Calificacionfuncionario->recursive=-1;
			$calificacionFuncionarios = $this->Calificacionfuncionario->find('all', array( 'conditions' =>
																			array('Calificacionfuncionario.periodo_id = '.$perId.' '
																					,'Calificacionfuncionario.funcionario_id in ('.$idPers.') '																		
																			)
																	)
															);
		}
		//echo 'calificacionFuncionarios<pre>'.print_r($calificacionFuncionarios,1).'</pre>';
		$listaCalifFunc =array();
		foreach($calificacionFuncionarios as $listaCalFunc){
			$listaCalifFunc[$listaCalFunc['Calificacionfuncionario']['periodo_id']]
						   [$listaCalFunc['Calificacionfuncionario']['subfactore_id']]
						   [$listaCalFunc['Calificacionfuncionario']['funcionario_id']] = $listaCalFunc['Calificacionfuncionario']['nota'];
						   
			$listaCalifFunc[$listaCalFunc['Calificacionfuncionario']['periodo_id']]
						   [$listaCalFunc['Calificacionfuncionario']['subfactore_id']]
						   ['id'] = $listaCalFunc['Calificacionfuncionario']['id'];
		}
		$nroNotas0 = $this->Notasubfactor->find('all', array( 'conditions'=>array('Notasubfactor.periodo_id = '.$perId.' ')
															,'fields'=>array('count(Notasubfactor.funcionario_id) AS cnt'
																			, 'Notasubfactor.funcionario_id AS funcionario_id')
															,'group'=>'Notasubfactor.funcionario_id'
														)
		);
		//echo 'nroNotas0<pre>'.print_r($nroNotas0,1).'</pre>';
		$cntNotasSubfactor=array();
		foreach($nroNotas0 as $listaNroNotas0){
			$cntNotasSubfactor[$listaNroNotas0[0]['funcionario_id']]=$listaNroNotas0[0]['cnt'];
		}
		$arrayNoJefatura = array(6, 5, 4, 3);
		$totalSubfactor = (in_array($idEscalafon, $arrayNoJefatura) ? $this->Subfactor->find('count') - 4 : $this->Subfactor->find('count') );
		
		
		$options = array();
		if( $idEscalafon > 1 )$options = array('conditions' => array('Factor.id > 1') );
		$this->Factor->recursive=2;
		$factores = $this->Factor->find('all', $options);
		
		$arrayCntSub= array();
		foreach($factores as $listaFactores){
			$cntSub=0;
			foreach($listaFactores['Subfactor'] as $listaSubfactores)$cntSub++;
			$arrayCntSub[$listaFactores['Factor']['id']]=$cntSub;
		}
		
		$chkSNota = $this->Chknota->find('all', array('conditions'=> array('Chknota.periodo_id = '.$perId.' ')) );
		$arrayChkNotas= array();
		foreach($chkSNota as $listaChkSNota)$arrayChkNotas[]=$listaChkSNota['Chknota']['funcionario_id'];
		$preCalificadores = $this->Precalificadore->find('all');
		$arrayPrecalificadores = $Funcionespropias->arrayInPunteroNombrePersona($preCalificadores);
			
		/// ob_clean();	
		/// $this->export_xls($notasFuncionarios, 'Report Title', 'Report_FileName');
		
		
		$this->set(compact('nombreEscalafon', 'perId', 'perNombre', 'subPerNombre', 'idEscalafon', 'factores', 'arrayCntSub', 'listaPersona'
		, 'notasFuncionarios', 'arrayChkNotas', 'cntNotasSubfactor', 'totalSubfactor', 'listaCalifFunc', 'listaFotoPersona', 'precalifId'
		, 'arrayPrecalificadores'));
		
	}

	
/*******************************************************************************************************************************/
/************************************************** VACIO **********************************************************************/
/*******************************************************************************************************************************/
	
	public function pendientes(){
		$subPerEtiqueta = '';
		$subPerId = '';
		$elSubPeriodo = '';
		$valorsPeriodos = array();
		
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND DATEADD(m, 2, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesevaldesde'=> '')) );
				//mesevaldesde	mesevalhasta
		endif;
		if(count($this->passedArgs) > 0){
			$elSubPeriodo = $this->passedArgs['subperId'];
		}
		
		foreach($periodoEvaluados as $listaSubPeriodos){
			$valorsPeriodos[$listaSubPeriodos['Subperiodo']['id']] = $listaSubPeriodos['Subperiodo']['etiqueta'].' / '
				.date('Y', strtotime($listaSubPeriodos['Subperiodo']['mesdesde']));
			if( $listaSubPeriodos['Subperiodo']['id'] == $elSubPeriodo){
				$subPerEtiqueta=$listaSubPeriodos['Periodo']['etiqueta'].'<br />Subperiodo: '
						.$listaSubPeriodos['Subperiodo']['etiqueta']
						.' '.date('Y', strtotime($listaSubPeriodos['Subperiodo']['mesdesde']));
				$subPerId=$listaSubPeriodos['Subperiodo']['id'];
			}
		}
		$nroPreguntas = $this->Item->find('count');
		$this->Subfactor->recursive=-1;
		$optionsSubfactor = array('fields'=> array("COUNT(Subfactor.factore_id) AS 'nroSubFact'", "Subfactor.factore_id AS 'factore_id'")
								  ,'group'=>'Subfactor.factore_id' );
		$arrayCero = $this->Subfactor->find('all', $optionsSubfactor);
		$nroPregSubFactTotal=0;
		
		foreach($arrayCero as $listado)
			$nroPregSubFactTotal+=$listado[0]['nroSubFact'];
		
		foreach($arrayCero as $listado)
			$nroPregSubFact[$listado[0]['factore_id']] = $listado[0]['nroSubFact'];
		
		$this->Persona->recursive=0;
		$listaSelecEvaluados = $this->Persona->find('all', array('conditions' => 
																array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')')
																	  ,'fields' => array('DISTINCT Persona.NOMBRES'
																	  					, 'Persona.AP_PAT'
																						, 'Persona.AP_MAT'
																						, 'Persona.ID_PER') 
																	  ,'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																	  
																	  )
													);
		
		$listaDelPeriodo = $this->Evaluafuncionario->find('all', array('conditions' => 
																	array('Evaluafuncionario.subperiodo_id' => $subPerId)
																		  ,'fields' => array('DISTINCT Evaluafuncionario.funcionario_id'
																		  					,'Evaluafuncionario.precalificadore_id' ) 
																	  )
																);

		$arrayFuncSeleccionadosPeriodo=array();
		foreach($listaDelPeriodo as $listaFuncPer){
			$idFuncionario = $listaFuncPer['Evaluafuncionario']['funcionario_id'];
			$precalificadore_id = $listaFuncPer['Evaluafuncionario']['precalificadore_id'];
			foreach($listaSelecEvaluados as $listaFunc){
				if($idFuncionario == $listaFunc['Persona']['ID_PER']){
					$nroPreguntasFunc = $this->Evaluafuncionario->cntFactorAsignado($idFuncionario, $subPerId);
					$nroPreguntasFuncJust = $this->Evaluafuncionario->nroPreguntasFuncSubfactJust($idFuncionario, $subPerId);
					$nroRespuestasFunc = $this->Calificafuncionario->find('count', array('conditions'=> 
																		array('Calificafuncionario.funcionario_id'=>$idFuncionario
																			,'Calificafuncionario.subperiodo_id'=>$subPerId) ) );
					
					$nroRespuestasFuncJust = $this->Evaluafuncionario->nroRespuestasJustificacion($idFuncionario, $subPerId);

					$cntRespuestasFuncJust = 0;
					if(is_array($nroRespuestasFuncJust) && count($nroRespuestasFuncJust) > 0 ){
						foreach($nroRespuestasFuncJust as $lista){
							$cntRespuestasFuncJust += $lista[0]['NroJustif'];
						}
					}
					$arrayFuncSeleccionadosPeriodo[] = array('funcionario_id'=>$idFuncionario
															 ,'Nombre' =>utf8_encode($listaFunc['Persona']['NOMBRES'].' '.$listaFunc['Persona']['AP_PAT']
															 				.' '.$listaFunc['Persona']['AP_MAT'])
															 ,'preguntas'=> $nroPreguntasFunc[0][0]['count'] + $nroPreguntasFuncJust[0][0]['nroRespJust']
															 ,'respuestas'=> $nroRespuestasFunc + $cntRespuestasFuncJust
															 ,'precalificadore_nombre'=> $precalificadore_id.' '.$this->
															 								Evaluafuncionario->
																								buscarPrecalificador($precalificadore_id, $listaSelecEvaluados)
					);
				}
			}
		}
		
		$this->set(compact('nroPreguntas', 'subPerEtiqueta', 'arrayFuncSeleccionadosPeriodo', 'valorsPeriodos', 'elSubPeriodo'));
	}

	public function ListaEvaluafuncionario(){
		$elPeriodo = '';
		$arraFuncActuals= array();
		if(count($this->passedArgs) > 0){
			//echo '<pre>'.print_r($this->passedArgs, true).'</pre><hr>';
			$elPeriodo = $this->passedArgs['perId'];
			$this->Evaluafuncionario->recursive = 2;
			$periodoEvaluadosActuales = $this->Evaluafuncionario->find('all', array('conditions' => array('Evaluafuncionario.subperiodo_id' => $elPeriodo) ) );
			//echo 'lista actuales:<pre>'.print_r($periodoEvaluadosActuales, true).'</pre><hr>';
			$tmpArrayFuncActuals= array();
			foreach($periodoEvaluadosActuales as $listaFuncActuals){
				$tmpArrayFuncActuals[]=$listaFuncActuals['Evaluafuncionario']['funcionario_id'];
			}
			//echo 'tmp lista actuales:<pre>'.print_r($tmpArrayFuncActuals, true).'</pre><hr>';
			$arraFuncActuals = array_unique($tmpArrayFuncActuals);
			///echo 'lista actuales:<pre>'.print_r($arraFuncActuals, true).'</pre><hr>';
		}
		
		if( !isset($this->Auth) ){
			$this->redirect( array('controller'=>'users', 'action' => 'logout'));
		}
		$authUser = $this->Auth->user();
		// echo 'authUser: '.print_r($authUser, true).'</pre><hr>';
		/// echo '$this->Session<pre>'.print_r($this->Session, true).'</pre><hr>';  
		$elPrecalificador = $this->PersonaEstado->find('all', array('conditions'=> 'PersonaEstado.usuario = \''.$authUser['User']['username'].'\'') );
		// echo count($elPrecalificador).'elPrecalificador: <pre>'.print_r($elPrecalificador, 1).'</pre><hr>';
		if(count($elPrecalificador) <= 0){
			$this->here='';
			//$this->redirect( array('controller'=>'users', 'action' => 'logout'));
			$this->Session->setFlash(print_r($authUser, 1));
		}
		$idelPrecalificador=$elPrecalificador[0]['PersonaEstado']['id_per'];
		/*** SOLO DESARRO Y PRUEBAS ***/
		// if($idelPrecalificador == 6)$idelPrecalificador=693;
		if($idelPrecalificador == 444)$idelPrecalificador = 14; //120; // $idelPrecalificador = 30; // $idelPrecalificador = 10; // $idelPrecalificador=6;
		// if($idelPrecalificador == 22)$idelPrecalificador = 693;
		//echo 'Iddeusuario: '.$idelPrecalificador.'<br />--';
		
	    if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' Subperiodo.periodo_id = 23')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(d, 10, Subperiodo.mesevalhasta)')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		//echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, 1).'</pre><hr>';
		$ponenota=$periodoEvaluados[0]['Subperiodo']['ponenota'];
		$periodXId=$periodoEvaluados[0]['Periodo']['id'];
		$this->Persona->recursive = 0;
		$listaSelecEvaluados = $this->Persona->find('all', array('conditions' => 
																array('PersonaEstado.calidadJuridica in (1, 2)'),
																	  'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')));
		//echo 'listaSelecEvaluados<pre>'.print_r($listaSelecEvaluados, 1).'</pre><hr>';
		if(strlen($idelPrecalificador) == 0)$this->redirect( array('controller' => 'users', 'action' => 'login') );
		if(0): /*** PARA TODOS LOS PRECALIFICADORES ***/
			$listaDelPeriodo = $this->Evaluafuncionario->find('all', array('conditions' => 
																		array('Evaluafuncionario.subperiodo_id' => $elPeriodo),
																			  'fields' => array('DISTINCT Evaluafuncionario.funcionario_id') 
																		  )
																	);
		else: /*** POR PRECALIFICADOR ***/
			$listaDelPeriodo = $this->Evaluafuncionario->find('all', array('conditions' => 
																		array('Evaluafuncionario.subperiodo_id' => $elPeriodo,
																			  'Evaluafuncionario.precalificadore_id'=> $idelPrecalificador),
																			  'fields' => array('DISTINCT Evaluafuncionario.funcionario_id') 
																		  )
																	);
		endif;
		// echo 'listaDelPeriodo<pre>'.print_r($listaDelPeriodo, 1).'</pre>'.$elPeriodo.', '.$idelPrecalificador.'<hr>';
		$arrayFuncSeleccionadosPeriodo = array();
		foreach($listaDelPeriodo as $listaFuncPer){
			$idPer = $listaFuncPer['Evaluafuncionario']['funcionario_id'];
			foreach($listaSelecEvaluados as $listaFunc){
				if($idPer == $listaFunc['Persona']['ID_PER']){
					$arrayFuncSeleccionadosPeriodo[] = array('funcionario_id'=>$idPer,
															 'Nombre' =>$listaFunc['Persona']['NOMBRES']
																 .' '.$listaFunc['Persona']['AP_PAT']
																 .' '.$listaFunc['Persona']['AP_MAT']);
				}
			}
		}
		//echo 'arrayFuncSeleccionadosPeriodo<pre>'.print_r($arrayFuncSeleccionadosPeriodo, 1).'</pre><hr>';
		$arrayNroRespFunc=array();
		foreach($arrayFuncSeleccionadosPeriodo as $listaF){
			$nroPreguntasFunc = $this->Calificafuncionario->find('count', array('conditions'=> 
																			array('Calificafuncionario.funcionario_id'=>$listaF['funcionario_id']
																				, 'Calificafuncionario.subperiodo_id'=>$elPeriodo) ) );
			$arrayNroRespFunc[$listaF['funcionario_id']]=$nroPreguntasFunc;
		}
		///echo 'arrayNroRespFunc<pre>'.print_r($arrayNroRespFunc, true).'</pre><hr>';
		
		$this->Justificacionsubperiodo->recursive = -1;
		$optionsNRFJ = array('conditions'=> array('Justificacionsubperiodo.subperiodo_id'=>$elPeriodo)
							, 'fields'=> array('COUNT(subfactore_id) AS NroJustif', 'funcionario_id AS funcionario_id')
							, 'group' => 'Justificacionsubperiodo.funcionario_id' );
		$arrayCero = $this->Justificacionsubperiodo->find('all', $optionsNRFJ);
		foreach($arrayCero as $listado)$nroRespuestasFuncJustificacion[$listado[0]['funcionario_id']] = $listado[0]['NroJustif'];
		///echo 'nroRespuestasFuncJustificacion<pre>'.print_r($nroRespuestasFuncJustificacion, true).'</pre><hr>';
		
		
		$this->Evaluafuncionario->recursive = -1;
		$arrayNroItemsFuncPre = $this->Evaluafuncionario->find('all', array('conditions'=>array('Evaluafuncionario.subperiodo_id'=>$elPeriodo)
																		, 'fields' =>array('COUNT(funcionario_id) AS nroItems'
																						 , 'funcionario_id as funcionario_id') 
																		, 'group'=> 'Evaluafuncionario.funcionario_id'
																	)
		);
		//echo 'arrayNroItemsFuncPre<pre>'.print_r($arrayNroItemsFuncPre, 1).'</pre><hr>';
		$arrayNroItemsFunc = array();
		foreach($arrayNroItemsFuncPre as $listaNroItem){
			$arrayNroItemsFunc[$listaNroItem[0]['funcionario_id']]=$listaNroItem[0]['nroItems'];
			//echo '<pre>'.print_r($listaNroItem[0],1).'</pre>';
		}
		//echo 'arrayNroItemsFunce<pre>'.print_r($arrayNroItemsFunc, 1).'</pre><hr>';		
		$nroPreguntas = $this->Item->find('count');
		///echo 'nroPreguntas<pre>'.print_r($nroPreguntas, true).'</pre><hr>';
		$this->Subfactor->recursive = -1;
		$optionsSubfactor = array('fields'=> array("COUNT(Subfactor.factore_id) AS 'nroSubFact'", "Subfactor.factore_id AS 'factore_id'")
								, 'group'=>'Subfactor.factore_id' );
		$arrayCero = $this->Subfactor->find('all', $optionsSubfactor);
		$nroPregSubFactTotal = 0;
		foreach($arrayCero as $listado)$nroPregSubFactTotal+=$listado[0]['nroSubFact'];
		foreach($arrayCero as $listado)$nroPregSubFact[$listado[0]['factore_id']] = $listado[0]['nroSubFact'];
		
		/*** NOTAS DE CADA SUBFACTOR SEPARADO POR FUNCIONARIO, SON 11 EN TOTAL ***/
		$arrayOptions = array('conditions'=> array('Notasubfactor.periodo_id' => $periodXId) 
							, 'fields'=> array("COUNT(funcionario_id) as nroNotaSubFact", "funcionario_id as funcionario_id")
							, 'group'=>'Notasubfactor.funcionario_id' );
		$arrayCero = $this->Notasubfactor->find('all', $arrayOptions);
		foreach($arrayCero as $listado)$nroNotaSubFact[$listado[0]['funcionario_id']] = $listado[0]['nroNotaSubFact'];
		
		$hoy = date('d/m/Y');
		$evaluaDesde = date('d/m/Y', strtotime($periodoEvaluados[0]['Subperiodo']['mesevaldesde']));
		$evaluaHasta = date('d/m/Y', strtotime($periodoEvaluados[0]['Subperiodo']['mesevalhasta']));		

		$EvaluaDesde = $this->Evaluafuncionario->compararFechas($hoy,$evaluaDesde);
		$EvaluaHasta = $this->Evaluafuncionario->compararFechas($hoy,$evaluaHasta);

		$varDis=1;
		if( (($EvaluaDesde >= 0 ? '+' : '-') == '+') && (($EvaluaHasta >= 0 ? '+' : '-') == '-') )$varDis=0;
		if( (($EvaluaDesde == 0 ? '+' : '-') == '+') && (($EvaluaHasta == 0 ? '+' : '-') == '+') )$varDis=0;
		if( (($EvaluaDesde == 1 ? '+' : '-') == '+') && (($EvaluaHasta == 0 ? '+' : '-') == '+') )$varDis=0;
		if( (($EvaluaDesde >= 0 ? '+' : '-') == '+') && (($EvaluaHasta == 0 ? '+' : '-') == '+') )$varDis=0;
		if($varDis==1)$this->Session->setFlash(__('Proceso Cerrado', true));
				
				
		// echo '<pre>elPeriodo: '.print_r($periodoEvaluados, 1).'</pre>';
		$this->set(compact('periodoEvaluados', 'elPeriodo','arrayFuncSeleccionadosPeriodo', 'nroPreguntas', 'arrayNroRespFunc'
		, 'varDis', 'arrayNroItemsFunc', 'nroRespuestasFuncJustificacion', 'nroPregSubFactTotal', 'nroNotaSubFact', 'ponenota'));
	}

	public function ListaEvaluafuncionarioTodos(){
		//echo '<pre>'.print_r($this->passedArgs, true).'</pre><hr>';
		$elPeriodo = '';
		$arraFuncActuals= array();
		
		
		if( !isset($this->Auth) ){
			$this->redirect( array('controller'=>'users', 'action' => 'logout'));
		}
		$authUser = $this->Auth->user();
/***	echo '$this->Session<pre>'.print_r($this->Session, true).'</pre><hr>';  ***/
		$elPrecalificador = $this->PersonaEstado->find('all', array('conditions'=> 'PersonaEstado.usuario = \''.$authUser['User']['username'].'\'') );
		//echo count($elPrecalificador).'elPrecalificador: <pre>'.print_r($elPrecalificador, 1).'</pre><hr>';
		if(count($elPrecalificador) <= 0){
			$this->here='';
			//$this->redirect( array('controller'=>'users', 'action' => 'logout'));
			$this->Session->setFlash('authUser: '.print_r($authUser, 1));
		}
		$idelPrecalificador=$elPrecalificador[0]['PersonaEstado']['id_per'];
		/*** SOLO DESARRO Y PRUEBAS ***/
		// if($idelPrecalificador == 6)$idelPrecalificador=693;
		if($idelPrecalificador == 444)$idelPrecalificador=6;
		//echo 'Iddeusuario: '.$idelPrecalificador.'<br />--';
		
		
		
		$this->Periodo->recursive = -1;
		$periodoEvaluados = $this->Periodo->find('list', array('order' => array('Periodo.desde'=> 'desc')) );
		// echo 'periodoEvaluados<pre>'.print_r($periodoEvaluados, 1).'</pre><hr>';

		$arrayFuncSeleccionadosPeriodo = array();
		if(count($this->passedArgs) > 0){
			$Funcionespropias = new Funcionespropias();
			$elPeriodo = $this->passedArgs['perId'];
			
			$this->Persona->recursive = 0;
			$listaSelecEvaluados = $this->Persona->find('all', array('conditions' => 
																	array('PersonaEstado.calidadJuridica in (1, 2)')
																	, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																	, 'fields' => array('Persona.*', 'PersonaEstado.*') ));
			// echo 'listaSelecEvaluados<pre>'.print_r($listaSelecEvaluados, 1).'</pre><hr>';
			
			if(strlen($idelPrecalificador) == 0)$this->redirect( array('controller' => 'users', 'action' => 'login') );
			
			//echo 'elPeriodo: '.$elPeriodo.'<br />';
			/*
			$this->Periodo->recursive = 1;
			$losSubperiodos = $this->Periodo->find('first', array('conditions' => array('Periodo.id' => $elPeriodo) 
																, 'fields' => array('Subperiodo.*') ) );
			*/
			$losSubperiodos = $this->Subperiodo->find('all', array('conditions' => array('periodo_id' => $elPeriodo) 
																, 'fields' => array('Subperiodo.id') ) );
			$arrayInSubperiodo = $Funcionespropias->arrayIn($losSubperiodos, 'Subperiodo', 'id');
			$stringInSubperiodo = implode(',', $arrayInSubperiodo);
			
			//echo 'arrayInSubperiodo<pre>'.print_r($arrayInSubperiodo, 1).'</pre><hr>';
			$this->Evaluafuncionario->recursive = 1;
			if(1): /*** PARA TODOS LOS PRECALIFICADORES ***/
				$listaDelPeriodo = $this->Evaluafuncionario->find('all', array('conditions' => 
																			array('Evaluafuncionario.subperiodo_id' => $arrayInSubperiodo),
																				  'fields' => array('DISTINCT Evaluafuncionario.funcionario_id') 
																			  )
																		);
			else: /*** POR PRECALIFICADOR ***/
				$listaDelPeriodo = $this->Evaluafuncionario->find('all', array('conditions' => 
																			array('Evaluafuncionario.subperiodo_id' => $elPeriodo,
																				  'Evaluafuncionario.precalificadore_id'=> $idelPrecalificador),
																				  'fields' => array('DISTINCT Evaluafuncionario.funcionario_id') 
																			  )
																		);
			endif;
			//echo 'listaDelPeriodo<pre>'.print_r($listaDelPeriodo, 1).'</pre>'.$elPeriodo.', '.$idelPrecalificador.'<hr>';
			
			$arrayListaDelPeriodo = $Funcionespropias->arrayIn($listaDelPeriodo, 'Evaluafuncionario', 'funcionario_id');
			//echo 'arrayListaDelPeriodo<pre>'.print_r($arrayListaDelPeriodo, 1).'</pre><hr>';
			
			//echo 'listaSelecEvaluados<pre>'.print_r($listaSelecEvaluados, 1).'</pre><hr>';
			if(0):
				foreach($listaDelPeriodo as $listaFuncPer){
					$idPer = $listaFuncPer['Evaluafuncionario']['funcionario_id'];
					foreach($listaSelecEvaluados as $listaFunc){
						if($idPer == $listaFunc['Persona']['ID_PER']){
							$arrayFuncSeleccionadosPeriodo[] = array('funcionario_id'=>$idPer,
																	 'Nombre' =>$listaFunc['Persona']['NOMBRES']
																		 .' '.$listaFunc['Persona']['AP_PAT']
																		 .' '.$listaFunc['Persona']['AP_MAT']);
						}
					}
				}
			else:
				foreach($listaSelecEvaluados as $listaFunc){
					if( in_array($listaFunc['Persona']['ID_PER'], $arrayListaDelPeriodo) ){
						$arrayFuncSeleccionadosPeriodo[] = array('funcionario_id'=>$listaFunc['Persona']['ID_PER'],
																 'Nombre' =>$listaFunc['Persona']['NOMBRES']
																	 .' '.$listaFunc['Persona']['AP_PAT']
																	 .' '.$listaFunc['Persona']['AP_MAT']);
					}
				}
			endif;
			
			//echo 'arrayFuncSeleccionadosPeriodo<pre>'.print_r($arrayFuncSeleccionadosPeriodo, 1).'</pre><hr>';
			/**********************************************************/
		}

		$this->set(compact('periodoEvaluados', 'elPeriodo','arrayFuncSeleccionadosPeriodo', 'nroPreguntas', 'arrayNroRespFunc'
		, 'varDis', 'arrayNroItemsFunc', 'nroRespuestasFuncJustificacion', 'nroPregSubFactTotal', 'nroNotaSubFact', 'ponenota', 'stringInSubperiodo'));
	}	

	public function hojaDeCalificacion($idFunc = null){
		$Funcionespropias = new Funcionespropias();
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(m, 2, Subperiodo.mesevalhasta)')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		$periodoId = $periodoEvaluados['Periodo']['id'];
		$periodoNombre = $periodoEvaluados['Periodo']['etiqueta'];
		$subPeriodoId = $periodoEvaluados['Subperiodo']['id'];
		$subPeriodoNombre = $periodoEvaluados['Subperiodo']['etiqueta'];
		
		$this->Persona->unbindModel(array('hasOne' => array('Personaimagen', 'Precalificadore', 'Calificadore')));
		$this->Persona->unbindModel(array('hasMany' => array('Evaluafuncionario', 'Justificacionsubperiodo', 'Notasubfactor'
															, 'Calificafuncionario', 'Historia')));
		$this->Persona->unbindModel(array('belongsTo' => array('PersonaEstado') ) );
		
		$this->Persona->bindModel(array('hasOne' =>  array('FirmasHojacalifica' => array('className' => 'FirmasHojacalifica'
																						, 'foreignKey' => 'funcionario_id'))) );
		$this->Persona->primaryKey = 'ID_PER';
	
		$condiciones = array('Persona.ID_PER' => $idFunc);
		$datosUserFuncionario = $this->Persona->find('first', array('conditions'=> $condiciones ) );
		$historiaFuncionario = $this->Historia->traeFuncionariosHistoria($idFunc);
		// echo 'historiaFuncionario<pre>'.print_r($historiaFuncionario, 1).'</pre><hr>';
		$codCargo = $historiaFuncionario[0][0]['COD_CARGO'];
		// echo 'codCargo<pre>'.$codCargo.'</pre><hr>';
		$listaCoeficientesTmp = $this->FactorCalificacion->find('all', array('conditions' => array('FactorCalificacion.cargo_id' => $codCargo)) );
		// echo 'listaCoeficientesTmp<pre>'.print_r($listaCoeficientesTmp, 1).'</pre><hr>';
		$listaCoeficientes = $Funcionespropias->arrayInPuntero($listaCoeficientesTmp, 'factore_id', 'FactorCalificacion', 'valor');
		//echo 'listaCoeficientes<pre>'.print_r($listaCoeficientes, 1).'</pre><hr>';
		
		unset($datosUserFuncionario['FirmasHojacalifica']['id']
			, $datosUserFuncionario['FirmasHojacalifica']['funcionario_id']
			, $datosUserFuncionario['FirmasHojacalifica']['created'] );
			
		$firmasIn = implode(',', $datosUserFuncionario['FirmasHojacalifica']);
		
		$this->Persona->recursive = -1;
		$listaFirmantes = $this->Persona->find('all', array('conditions' => array('Persona.ID_PER in ('.($firmasIn).')' )) );
		$nombreFirmantes = array();
		foreach($listaFirmantes as $lista){
			$nombreFirmantes[$lista['Persona']['ID_PER']] = $lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT'];
		}

		$condiciones = array('conditions'=> array('Notasubfactor.periodo_id = '.$periodoId.' '
												, 'Notasubfactor.funcionario_id = '.$idFunc.' ') );
		$listaNotasubfactor = $this->Notasubfactor->find('all', $condiciones);

		$this->Calificacionfuncionario->recursive=-1;
		$calificacionFuncionarios = $this->Calificacionfuncionario->find('all', array( 'conditions' =>
																		array('Calificacionfuncionario.periodo_id = '.$periodoId.' '
																				,'Calificacionfuncionario.funcionario_id in ('.$idFunc.') '																		
																		)
																)
														);
		
		$arrayFactores = array_unique($Funcionespropias->arrayIn($listaNotasubfactor, 'Subfactor', 'factore_id'));
		
		$options = array('conditions' => array('Factor.id' => $arrayFactores) );
		$listaFactor = $this->Factor->find('all', $options );
		
		$lstNotas=array();
		foreach($calificacionFuncionarios as $lst){
			$lstNotas[$lst['Calificacionfuncionario']['subfactore_id']] = $lst['Calificacionfuncionario']['nota'];
		}
		
		$this->set(compact('datosUserFuncionario', 'periodoNombre', 'subPeriodoNombre', 'listaFactor', 'lstNotas', 'nombreFirmantes', 'listaCoeficientes'));
	}
	
	public function Factorfuncionario(){
		if(isset($this->params['data'])){
			$parametrs = $this->params['data'];
			$subperiodo_id = $parametrs['Evaluafuncionario']['elPeriodo'];
			$funcionario_id = $parametrs['Evaluafuncionario']['funcionario_id'];
		}elseif(isset($this->params['named'])){
			$parametrs = $this->params['named'];
			$subperiodo_id = $parametrs['elPeriodo'];
			$funcionario_id = $parametrs['funcionario_id'];
		}else{
			$this->redirect( array('action'=>'ListaEvaluafuncionario') );
		}	
		$this->Persona->recursive=0;
		$elFuncionario = $this->Persona->find('first', array('conditions' => 
			array('PersonaEstado.calidadJuridica in (1, 2)',
				  'Persona.id_per'=>$funcionario_id) ));
		$evaluacionFuncionario = $this->Evaluafuncionario->find('all', 
				array( 'conditions' => array('Evaluafuncionario.subperiodo_id' => $subperiodo_id, 'Evaluafuncionario.funcionario_id' => $funcionario_id) )
		);
		$arraynomSubPeriodo = $this->Subperiodo->find('first', array( 'conditions' => array('Subperiodo.id' => $subperiodo_id) ));
		$idPeriodo = $arraynomSubPeriodo['Periodo']['id'];
		$idSubPeriodo = $arraynomSubPeriodo['Subperiodo']['id'];
		$nomPeriodo = $arraynomSubPeriodo['Periodo']['etiqueta'];
		$nomSubPeriodo = $arraynomSubPeriodo['Subperiodo']['etiqueta'];
		$ponenota= $arraynomSubPeriodo['Subperiodo']['ponenota'];
		
		$arrayNroRespFunc=array();
		$nroPreguntasFunc = $this->Calificafuncionario->find('first', array('conditions' => 
																		array('Calificafuncionario.funcionario_id' => $funcionario_id
																			, 'Calificafuncionario.subperiodo_id' => $subperiodo_id) 
																			, 'fields' => array('COUNT(funcionario_id) AS nroPregs'
																							 , 'funcionario_id as funcionario_id')  
																			, 'group' => 'Calificafuncionario.funcionario_id'
																		) 
		);
		$this->Justificacionsubperiodo->recursive=-1;
		$optionsNRFJ = array('conditions'=> array('Justificacionsubperiodo.subperiodo_id' => $subperiodo_id
												, 'Justificacionsubperiodo.funcionario_id' => $funcionario_id)
							, 'fields'=> array('count(subfactore_id) AS NroJustif', 'funcionario_id AS funcionario_id')
							, 'group' => 'Justificacionsubperiodo.funcionario_id' );
		$arrayCero = $this->Justificacionsubperiodo->find('all', $optionsNRFJ);
		foreach($arrayCero as $listado)$nroRespuestasFuncJustificacion[$listado[0]['funcionario_id']] = $listado[0]['NroJustif'];
		$this->Subfactor->recursive=-1;
		$optionsSubfactor = array('fields'=> array("COUNT(Subfactor.factore_id) AS 'nroSubFact'", "Subfactor.factore_id AS 'factore_id'")
								  ,'group'=>'Subfactor.factore_id' );
		$arrayCero = $this->Subfactor->find('all', $optionsSubfactor);
		$nroPregSubFactTotal=0;
		foreach($arrayCero as $listado)$nroPregSubFactTotal+=$listado[0]['nroSubFact'];
		foreach($arrayCero as $listado)$nroPregSubFact[$listado[0]['factore_id']] = $listado[0]['nroSubFact'];
		
		$arrayNroPreguntasFactor= $this->Evaluafuncionario->nroPreguntasItem();
		$nroPreguntasFactor=array();
		foreach($arrayNroPreguntasFactor as $listado){
			$nroPreguntasFactor[$listado[0]['factore_id']]=$listado[0]['nroPreg'];
		}
		
		$nroPreguntas = $this->Item->find('count');
		
		$this->Subfactor->recursive=-1;
		$optionsSubfactor = array('fields'=> array("COUNT(Subfactor.factore_id) AS 'nroSubFact'", "Subfactor.factore_id AS 'factore_id'")
								  ,'group'=>'Subfactor.factore_id' );
		$arrayCero = $this->Subfactor->find('all', $optionsSubfactor);
		$nroPregSubFactTotal=0;
		foreach($arrayCero as $listado)$nroPregSubFactTotal+=$listado[0]['nroSubFact'];
		foreach($arrayCero as $listado)$nroPregSubFact[$listado[0]['factore_id']] = $listado[0]['nroSubFact'];
		
		$arrayNroItemsFuncPre = $this->Evaluafuncionario->nroRespuestas($funcionario_id, $subperiodo_id);
		$arrayNroRespFactorFunc=array();
		foreach($arrayNroItemsFuncPre as $listaNros){
			$arrayNroRespFactorFunc[$listaNros[0]['factore_id']]=$listaNros[0]['nroResp'];
		}
		
		$arrayCero = $this->Evaluafuncionario->nroRespuestasSubfactores($funcionario_id, $idPeriodo);
		$arrayNroRespSubfacFunc=array();
		foreach($arrayCero as $listado)$arrayNroRespSubfacFunc[$listado[0]['factore_id']]=$listado[0]['nroResp'];
		
		$arrayNroRespFuncJust = array();
		$arrayCero = $this->Evaluafuncionario->nroRespuestasJustificacion($funcionario_id, $subperiodo_id);
		foreach($arrayCero as $listaNros){
			$arrayNroRespFuncJust[$listaNros[0]['factore_id']]=$listaNros[0]['NroJustif'];
		}
		
		$flgNroPreguntas=$nroPreguntas;
		if($arrayNroItemsFuncPre[0]['nroItems'] == 3)$flgNroPreguntas=$nroPreguntas-4;
		
		$this->set(compact('idSubPeriodo', 'idPeriodo', 'nomPeriodo', 'nomSubPeriodo', 'evaluacionFuncionario', 'elFuncionario', 'arrayNroRespSubfacFunc'
							, 'arrayNroRespFactorFunc', 'nroPreguntasFactor', 'arrayNroRespFuncJust', 'nroPregSubFact', 'ponenota'));
	}

	public function FactorfuncionarioTodos(){
		//echo "<pre>params:".print_r($this->params, 1)."</pre>";
		if(isset($this->params['data'])){
			$parametrs = $this->params['data'];
			$subperiodo_id = explode(',', $parametrs['Evaluafuncionario']['elPeriodo']);
			$funcionario_id = $parametrs['Evaluafuncionario']['funcionario_id'];
		}elseif(isset($this->params['named'])){
			$parametrs = $this->params['named'];
			$subperiodo_id = explode(',', $parametrs['elPeriodo']);
			$funcionario_id = $parametrs['funcionario_id'];
		}else{
			$this->redirect( array('action'=>'ListaEvaluafuncionario') );
		}	
		
		$periodoEvaluados = $this->Subperiodo->find('all', 
		array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(d, 10, Subperiodo.mesevalhasta)')
		, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		$elSubperiodoActual = $periodoEvaluados[0]['Subperiodo']['id'];
		
		//echo '<pre>periodoEvaluados: '.print_r($periodoEvaluados, 1).'</pre>';
		
		//$funcionario_id = 432;
		$this->Persona->recursive = 0;
		$elFuncionario = $this->Persona->find('first', array('conditions' => 
			array('PersonaEstado.calidadJuridica in (1, 2)',
				  'Persona.id_per'=>$funcionario_id) ));
				  
		
		/*
		$evaluacionFuncionario = $this->Evaluafuncionario->find('all', 
				array( 'conditions' => array('Evaluafuncionario.subperiodo_id' => $subperiodo_id[0], 'Evaluafuncionario.funcionario_id' => $funcionario_id) )
		);
		*/
		$evaluacionFuncionario = $this->Evaluafuncionario->find('all', 
				array( 'conditions' => array('Evaluafuncionario.subperiodo_id' => $elSubperiodoActual, 'Evaluafuncionario.funcionario_id' => $funcionario_id) )
		);
		// echo '<pre>evaluacionFuncionario: '.print_r($evaluacionFuncionario, 1).'</pre>';
		
		
		
		//$arraynomSubPeriodo = $this->Subperiodo->find('first', array( 'conditions' => array('Subperiodo.id' => $subperiodo_id[0]) ));
		$arraynomSubPeriodo = $this->Subperiodo->find('first', array( 'conditions' => array('Subperiodo.id' => $elSubperiodoActual) ));

		$idPeriodo = $arraynomSubPeriodo['Periodo']['id'];
		$idSubPeriodo = $arraynomSubPeriodo['Subperiodo']['id'];
		$nomPeriodo = $arraynomSubPeriodo['Periodo']['etiqueta'];
		$nomSubPeriodo = $arraynomSubPeriodo['Subperiodo']['etiqueta'];
		$ponenota= $arraynomSubPeriodo['Subperiodo']['ponenota'];
		
		$arrayNroRespFunc=array();
		$nroPreguntasFunc = $this->Calificafuncionario->find('first', array('conditions' => 
																		array('Calificafuncionario.funcionario_id' => $funcionario_id
																			, 'Calificafuncionario.subperiodo_id' => $elSubperiodoActual) 
																			, 'fields' => array('COUNT(funcionario_id) AS nroPregs'
																							 , 'funcionario_id as funcionario_id')  
																			, 'group' => 'Calificafuncionario.funcionario_id'
																		) 
		);
		// , 'Calificafuncionario.subperiodo_id' => $subperiodo_id[0])  ***/

		$this->Justificacionsubperiodo->recursive=-1;
		$optionsNRFJ = array('conditions'=> array('Justificacionsubperiodo.subperiodo_id' => $elSubperiodoActual
												, 'Justificacionsubperiodo.funcionario_id' => $funcionario_id)
							, 'fields'=> array('count(subfactore_id) AS NroJustif', 'funcionario_id AS funcionario_id')
							, 'group' => 'Justificacionsubperiodo.funcionario_id' );
		$arrayCero = $this->Justificacionsubperiodo->find('all', $optionsNRFJ);
		foreach($arrayCero as $listado)$nroRespuestasFuncJustificacion[$listado[0]['funcionario_id']] = $listado[0]['NroJustif'];

		$this->Subfactor->recursive=-1;
		$optionsSubfactor = array('fields'=> array("COUNT(Subfactor.factore_id) AS 'nroSubFact'", "Subfactor.factore_id AS 'factore_id'")
								  ,'group'=>'Subfactor.factore_id' );
		$arrayCero = $this->Subfactor->find('all', $optionsSubfactor);
		$nroPregSubFactTotal=0;
		foreach($arrayCero as $listado)$nroPregSubFactTotal+=$listado[0]['nroSubFact'];
		foreach($arrayCero as $listado)$nroPregSubFact[$listado[0]['factore_id']] = $listado[0]['nroSubFact'];
		
		$arrayNroPreguntasFactor= $this->Evaluafuncionario->nroPreguntasItem();
		$nroPreguntasFactor=array();
		foreach($arrayNroPreguntasFactor as $listado){
			$nroPreguntasFactor[$listado[0]['factore_id']]=$listado[0]['nroPreg'];
		}
		
		$nroPreguntas = $this->Item->find('count');
		
		$this->Subfactor->recursive=-1;
		$optionsSubfactor = array('fields'=> array("COUNT(Subfactor.factore_id) AS 'nroSubFact'", "Subfactor.factore_id AS 'factore_id'")
								  ,'group'=>'Subfactor.factore_id' );
		$arrayCero = $this->Subfactor->find('all', $optionsSubfactor);
		$nroPregSubFactTotal=0;
		foreach($arrayCero as $listado)$nroPregSubFactTotal+=$listado[0]['nroSubFact'];
		foreach($arrayCero as $listado)$nroPregSubFact[$listado[0]['factore_id']] = $listado[0]['nroSubFact'];
		$this->set(compact('idSubPeriodo', 'idPeriodo', 'nomPeriodo', 'nomSubPeriodo', 'evaluacionFuncionario', 'elFuncionario', 'arrayNroRespSubfacFunc'
							, 'arrayNroRespFactorFunc', 'nroPreguntasFactor', 'arrayNroRespFuncJust', 'nroPregSubFact', 'ponenota'));
	}

	public function Evaluacionfuncionario(){
		/***************************/
		//*** SECCION QUE GUARDA ***
		if( !empty($this->data) && isset($this->data['Evaluar']['swSave']) ){
			$arrayForm = $this->params['form'];
			$funcionario_id = $this->data['Evaluar']['funcionario_id'];
			$elPeriodo = $this->data['Evaluar']['elPeriodo'];
			$elFactor = $this->data['Evaluar']['elFactor'];			
			unset($this->data['Evaluar']['swSave']);
			
			/*** JUSTIFICACION PERIODO ***/
			$txtJust = array();
			$txtNota = array();
			foreach($this->data['Evaluar'] as $pnt =>$evalFunc){
				$nomPnt= substr($pnt, 0, 7);
				$nomValPnt= substr($pnt, 7);
				if($nomPnt == 'txtJust'){
					$subPerPos = strpos($nomValPnt, '_');
					$subPerId = substr($nomValPnt, 0, $subPerPos);
					$itemId = substr($nomValPnt, $subPerPos+1);
					if($evalFunc != ''){
						$txtJust[] = array('subperiodo_id'=>$subPerId, 'subfactore_id'=>$itemId, 'funcionario_id'=>$funcionario_id, 'texto'=>$evalFunc);
					}
					unset($this->data['Evaluar'][$pnt]);
				}
				/*** NOTA SUBFACTOR ***/
				if($nomPnt == 'txtNota'){
					$subPerPos = strpos($nomValPnt, '_');
					$subPerId = substr($nomValPnt, 0, $subPerPos);
					$itemId = substr($nomValPnt, $subPerPos+1);
					if($evalFunc != ''){
						$txtNota[] = array('periodo_id'=>$subPerId, 'subfactore_id'=>$itemId, 'funcionario_id'=>$funcionario_id, 'nota'=>$evalFunc);
					}
					unset($this->data['Evaluar'][$pnt]);
				}
			}
			
			/*** CALIFICACION A FUNCIONARIOS/VALORACION PERIODOS ***/
			$valorEvaluacion = array();
			$varItemTmp=0;
			$varItem=0;
			$strIn=array();
			foreach($this->data['Evaluar'] as $pnt =>$evalFunc){
				$nomPnt= substr($pnt, 0, 4);
				$varItem=$evalFunc;
				if($nomPnt == 'Item'){
					if($varItem != $varItemTmp){
						$varItemTmp = $varItem;
					}
				}
				if( $pnt != 'funcionario_id' && $pnt != 'elPeriodo' && $pnt != 'elFactor' && $nomPnt != 'Item'){
					if($evalFunc){
						$valorEvaluacion[] = array('item_id'=>$varItemTmp, 'pregunta_id'=>$evalFunc, 'subperiodo_id'=>$elPeriodo, 'funcionario_id'=>$funcionario_id);
						$strIn[]= $varItemTmp;
					}
				}
			}
			if(1):
				
			if(0){
				//************ PARA NOTA-SUBFACTORES ************				
				echo 'txtJust: <pre>'.print_r($txtNota, true).'</pre><hr>';
			}else{
				if(is_array($txtNota) && count($txtNota)>0){
					foreach($txtNota as $listaNota){
						$periodo_id = $listaNota['periodo_id'];
						$subfactore_id = $listaNota['subfactore_id'];
						$funcionario_id = $listaNota['funcionario_id'];
						//************ BORRADO DE REGISTROS, PARA JUSTIFICA-FUNCIONARIO ************
						if($this->Notasubfactor->deleteAll(array('Notasubfactor.periodo_id'=>$periodo_id,
																		   'Notasubfactor.subfactore_id'=>$subfactore_id,
																		   'Notasubfactor.funcionario_id'=>$funcionario_id))){
						}else{$this->Session->setFlash(__('Error:<br>'.print_r(error_get_last(),true), true)); break;}
					}
					//************ INSERSION DE REGISTROS, PARA JUSTIFICA-FUNCIONARIO ************
					$this->Notasubfactor->create();
					if(!$this->Notasubfactor->saveAll($txtNota)){
						$arrayErrores =$this->Notasubfactor->invalidFields();
						$strErrors='';
						foreach($arrayErrores as $listaErrors){ if(!is_array($listaErrors)){$strErrors .= '<br />'.$listaErrors;}}
						$this->Session->setFlash('Error: <br>Sin registros para esta Nota/subfactor:<br>'.$strErrors.'<br><pre>'.print_r(error_get_last(),true).'</pre>');
					}else{
						$this->Session->setFlash(__('Grabado', true));
					}
				}
			}
				
				//************ PARA JUSTIFICA-FUNCIONARIO ************
				if(is_array($txtJust) && count($txtJust)>0){
					foreach($txtJust as $listaJust){
						$subperiodo_id = $listaJust['subperiodo_id'];
						$subfactore_id = $listaJust['subfactore_id'];
						$funcionario_id = $listaJust['funcionario_id'];
						//************ BORRADO DE REGISTROS, PARA JUSTIFICA-FUNCIONARIO ************
						if($this->Justificacionsubperiodo->deleteAll(array('Justificacionsubperiodo.subperiodo_id'=>$subperiodo_id,
																		   'Justificacionsubperiodo.subfactore_id'=>$subfactore_id,
																		   'Justificacionsubperiodo.funcionario_id'=>$funcionario_id))){
						}else{$this->Session->setFlash(__('Error:<br>'.print_r(error_get_last(),true), true)); break;}
					}
					//************ INSERSION DE REGISTROS, PARA JUSTIFICA-FUNCIONARIO ************
					$this->Justificacionsubperiodo->create();
					if(!$this->Justificacionsubperiodo->saveAll($txtJust)){
						$arrayErrores =$this->Calificafuncionario->invalidFields();
						$strErrors='';
						foreach($arrayErrores as $listaErrors){ if(!is_array($listaErrors)){$strErrors .= '<br />'.$listaErrors;}}
						$this->Session->setFlash('Error: <br>Sin registros para este periodo/funcionario:<br>'.$strErrors.'<br><pre>'.print_r(error_get_last(),true).'</pre>');
					}else{
						$this->Session->setFlash(__('Grabado', true));
					}
				}
				
			if(1){
				//***************************** BORRADO DE REGISTROS, PARA EL ITEM, PERIODO, FUNCIONARIO ******************************
				if(count($strIn) > 0){
					if($this->Calificafuncionario->deleteAll(array('Calificafuncionario.item_id' =>$strIn,
																   'Calificafuncionario.subperiodo_id' =>$elPeriodo,
																   'Calificafuncionario.funcionario_id' =>$funcionario_id))){
						$this->Session->setFlash(__('borrado', true));
					}else{$this->Session->setFlash(__('Error: <br>'.print_r(error_get_last(),true), true)); break;}
				}
				if(count($valorEvaluacion) > 0){
					//***************************** NUEVA INSERSION *****************************
					$this->Calificafuncionario->create();
					if(!$this->Calificafuncionario->saveAll($valorEvaluacion)){
						$arrayErrores =$this->Calificafuncionario->invalidFields();
						$strErrors='';
						foreach($arrayErrores as $listaErrors){ if(!is_array($listaErrors)){$strErrors .= '<br />'.$listaErrors;}}
						$this->Session->setFlash(__('Ocurrio un Error: '.$strErrors, true));
					}else{
						$this->Session->setFlash('Grabado...');
						if( isset($arrayForm['GyV']) ){
							$this->redirect( array('action' => 'Factorfuncionario', 'funcionario_id'=>$funcionario_id, 'elPeriodo' => $elPeriodo) );
						}
					}
				//*** FIN GUARDA LISTADO CON FUNCIONARIOS A EVALUAR ***
				}else{
					$this->Session->setFlash('Sin registros de VALORACION para este periodo/funcionario.');
				}
			}
		    endif;
		}
		/*** FIN SECCION GUARDAR ***/
		/***************************/		
		$funcionario_id =$this->data['Evaluar']['funcionario_id'];
		$elPeriodo = $this->data['Evaluar']['elPeriodo']; /******* <---- ESTE ES SUBPERIODO, NO CONFUNDIRSE POR EL NOMBRE QUE POSEE ---------***/
		$this->Periodo->recursive=2;
		$periodoIdent = $this->Subperiodo->find('first', array('conditions' => array('Subperiodo.id' => $elPeriodo)) );
		if(is_array($periodoIdent)){
			$periodoIdent = $periodoIdent['Periodo']['id'];
		}

		$periodo = $this->Periodo->Subperiodo->find('all', array('conditions'=> array('Subperiodo.periodo_id'=>$periodoIdent) ) );

		$Subperiodo = $this->Subperiodo->find('all', array('conditions' => array('Subperiodo.id' => $elPeriodo)) );
		$subperiodoId=$Subperiodo[0]['Subperiodo']['id'];
		$ponenota=$Subperiodo[0]['Subperiodo']['ponenota'];

		$elFactor = $this->data['Evaluar']['elFactor'];
		$elIdFuncionario = $this->data['Evaluar']['funcionario_id'];
		$this->Persona->recursive=0;
		$elFuncionario = $this->Persona->find('first', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')',
																				  'Persona.id_per'=>$elIdFuncionario) ));
		$this->Factor->recursive=4;
		$listaPreguntas = $this->Factor->find('first', array('conditions' => array('Factor.id' => $elFactor)) );

		$colorConcepto = $this->colorConcepto;

		$listaRespEval = $this->Calificafuncionario->find('all', array('conditions' => array('Calificafuncionario.subperiodo_id' =>$elPeriodo,
																							 'Calificafuncionario.funcionario_id' =>$funcionario_id)));
		$listaRespEval2= array();
		foreach($listaRespEval as $valPregunta)$listaRespEval2[]=$valPregunta['Calificafuncionario'];

		foreach($listaRespEval2 as $pnt=>$valPregunta){
			$clave = array_search('1', $listaRespEval2[$pnt]);
			$pregunta_id=$listaRespEval2[$pnt]['pregunta_id'];
			if($clave)$elPnt = $pnt;
		}
		$listaRespEvalOtroPeriodos = $this->Calificafuncionario->find('all', array('conditions' => array('Calificafuncionario.subperiodo_id <>' =>$elPeriodo,
																										 'Calificafuncionario.funcionario_id' =>$funcionario_id)));
		$listaRespEvalOp2= array();
		foreach($listaRespEvalOtroPeriodos as $valPregunta)$listaRespEvalOp2[]=$valPregunta['Calificafuncionario'];

		foreach($listaRespEvalOp2 as $pnt=>$valPregunta){
			$clave = array_search('1', $listaRespEvalOp2[$pnt]);
			$pregunta_id=$listaRespEvalOp2[$pnt]['pregunta_id'];
			if($clave)$elPnt = $pnt;
		}

		$lstSupPer = array();
		foreach($periodo as $lstSubper){$lstSupPer[] = $lstSubper['Subperiodo']['id'];}
		$optionJustFunc = array('conditions'=>array('Justificacionsubperiodo.subperiodo_id'=> $lstSupPer ,
													'Justificacionsubperiodo.funcionario_id'=>$funcionario_id)) ;
		$listaJustFuncTmp = $this->Justificacionsubperiodo->find('all', $optionJustFunc );
		$listaJustFunc=array();
		foreach($listaJustFuncTmp as $lista)$listaJustFunc[] = $lista['Justificacionsubperiodo'];
		
		$preguntaValorTmp = $this->PreguntaValor->find('all');
		$preguntaValor=array();
		foreach($preguntaValorTmp as $pnt => $valorPregval)$preguntaValor[$valorPregval['PreguntaValor']['valor']] = $valorPregval['PreguntaValor']['etiqueta'];
		
		$preguntaValorTmpnota = $this->PreguntaValornota->find('all');
		$preguntaValornota=array();
		foreach($preguntaValorTmpnota as $pnt => $valorPregval)$preguntaValornota[$valorPregval['PreguntaValornota']['valor']] = $valorPregval['PreguntaValornota']['etiqueta'];

		$optionSubfac = array('conditions'=>array('Notasubfactor.periodo_id'=> $periodoIdent ,
													'Notasubfactor.funcionario_id'=>$funcionario_id)) ;
		$this->Notasubfactor->recursive=-1;
		$notasSubfacTmp = $this->Notasubfactor->find('all', $optionSubfac);
		foreach($notasSubfacTmp as $valorSubfac)$notasSubfac[(int)$valorSubfac['Notasubfactor']['subfactore_id']] = (int)$valorSubfac['Notasubfactor']['nota'];
		
		$arraySubPeriodos = array();
		foreach($periodo as $lista){
			$arraySubPeriodos[$lista['Subperiodo']['id']] = $lista['Subperiodo']['etiqueta'];
		}
		
		$this->set(compact('colorConcepto', 'listaRespEval', 'listaRespEval2', 'listaRespEvalOp2', 'arraySubPeriodos', 'periodo', 'elPeriodo', 'subperiodoId', 
		'elIdFuncionario', 'elFuncionario', 'elFactor', 'listaPreguntas', 'listaJustFunc', 'preguntaValornota', 'preguntaValor', 'notasSubfac', 'ponenota'));
	}

	public function EvaluacionfuncionarioTodos(){
		/***************************/
		//*** SECCION QUE GUARDA ***
		if( !empty($this->data) && isset($this->data['Evaluar']['swSave']) ){
			$arrayForm = $this->params['form'];
			$funcionario_id = $this->data['Evaluar']['funcionario_id'];
			$elPeriodo = $this->data['Evaluar']['elPeriodo'];
			$elFactor = $this->data['Evaluar']['elFactor'];			
			unset($this->data['Evaluar']['swSave']);
			
			/*** JUSTIFICACION PERIODO ***/
			$txtJust = array();
			$txtNota = array();
			foreach($this->data['Evaluar'] as $pnt =>$evalFunc){
				$nomPnt= substr($pnt, 0, 7);
				$nomValPnt= substr($pnt, 7);
				if($nomPnt == 'txtJust'){
					$subPerPos = strpos($nomValPnt, '_');
					$subPerId = substr($nomValPnt, 0, $subPerPos);
					$itemId = substr($nomValPnt, $subPerPos+1);
					if($evalFunc != ''){
						$txtJust[] = array('subperiodo_id'=>$subPerId, 'subfactore_id'=>$itemId, 'funcionario_id'=>$funcionario_id, 'texto'=>$evalFunc);
					}
					unset($this->data['Evaluar'][$pnt]);
				}
				/*** NOTA SUBFACTOR ***/
				if($nomPnt == 'txtNota'){
					$subPerPos = strpos($nomValPnt, '_');
					$subPerId = substr($nomValPnt, 0, $subPerPos);
					$itemId = substr($nomValPnt, $subPerPos+1);
					if($evalFunc != ''){
						$txtNota[] = array('periodo_id'=>$subPerId, 'subfactore_id'=>$itemId, 'funcionario_id'=>$funcionario_id, 'nota'=>$evalFunc);
					}
					unset($this->data['Evaluar'][$pnt]);
				}
			}
			
			/*** CALIFICACION A FUNCIONARIOS/VALORACION PERIODOS ***/
			$valorEvaluacion = array();
			$varItemTmp=0;
			$varItem=0;
			$strIn=array();
			foreach($this->data['Evaluar'] as $pnt =>$evalFunc){
				$nomPnt= substr($pnt, 0, 4);
				$varItem=$evalFunc;
				if($nomPnt == 'Item'){
					if($varItem != $varItemTmp){
						$varItemTmp = $varItem;
					}
				}
				if( $pnt != 'funcionario_id' && $pnt != 'elPeriodo' && $pnt != 'elFactor' && $nomPnt != 'Item'){
					if($evalFunc){
						$valorEvaluacion[] = array('item_id'=>$varItemTmp, 'pregunta_id'=>$evalFunc, 'subperiodo_id'=>$elPeriodo, 'funcionario_id'=>$funcionario_id);
						$strIn[]= $varItemTmp;
					}
				}
			}
				
			if(0){
				//************ PARA NOTA-SUBFACTORES ************				
				echo 'txtJust: <pre>'.print_r($txtNota, true).'</pre><hr>';
			}else{
				if(is_array($txtNota) && count($txtNota)>0){
					foreach($txtNota as $listaNota){
						$periodo_id = $listaNota['periodo_id'];
						$subfactore_id = $listaNota['subfactore_id'];
						$funcionario_id = $listaNota['funcionario_id'];
						//************ BORRADO DE REGISTROS, PARA JUSTIFICA-FUNCIONARIO ************
						if($this->Notasubfactor->deleteAll(array('Notasubfactor.periodo_id'=>$periodo_id,
																		   'Notasubfactor.subfactore_id'=>$subfactore_id,
																		   'Notasubfactor.funcionario_id'=>$funcionario_id))){
						}else{$this->Session->setFlash(__('Error:<br>'.print_r(error_get_last(),true), true)); break;}
					}
					//************ INSERSION DE REGISTROS, PARA JUSTIFICA-FUNCIONARIO ************
					$this->Notasubfactor->create();
					if(!$this->Notasubfactor->saveAll($txtNota)){
						$arrayErrores =$this->Notasubfactor->invalidFields();
						$strErrors='';
						foreach($arrayErrores as $listaErrors){ if(!is_array($listaErrors)){$strErrors .= '<br />'.$listaErrors;}}
						$this->Session->setFlash('Error: <br>Sin registros para esta Nota/subfactor:<br>'.$strErrors.'<br><pre>'.print_r(error_get_last(),true).'</pre>');
					}else{
						$this->Session->setFlash(__('Grabado', true));
					}
				}
			}
				
			//************ PARA JUSTIFICA-FUNCIONARIO ************
			if(is_array($txtJust) && count($txtJust)>0){
				foreach($txtJust as $listaJust){
					$subperiodo_id = $listaJust['subperiodo_id'];
					$subfactore_id = $listaJust['subfactore_id'];
					$funcionario_id = $listaJust['funcionario_id'];
					//************ BORRADO DE REGISTROS, PARA JUSTIFICA-FUNCIONARIO ************
					if($this->Justificacionsubperiodo->deleteAll(array('Justificacionsubperiodo.subperiodo_id'=>$subperiodo_id,
																	   'Justificacionsubperiodo.subfactore_id'=>$subfactore_id,
																	   'Justificacionsubperiodo.funcionario_id'=>$funcionario_id))){
					}else{$this->Session->setFlash(__('Error:<br>'.print_r(error_get_last(),true), true)); break;}
				}
				//************ INSERSION DE REGISTROS, PARA JUSTIFICA-FUNCIONARIO ************
				$this->Justificacionsubperiodo->create();
				if(!$this->Justificacionsubperiodo->saveAll($txtJust)){
					$arrayErrores =$this->Calificafuncionario->invalidFields();
					$strErrors='';
					foreach($arrayErrores as $listaErrors){ if(!is_array($listaErrors)){$strErrors .= '<br />'.$listaErrors;}}
					$this->Session->setFlash('Error: <br>Sin registros para este periodo/funcionario:<br>'.$strErrors.'<br><pre>'.print_r(error_get_last(),true).'</pre>');
				}else{
					$this->Session->setFlash(__('Grabado', true));
				}
			}
				
			//***************************** BORRADO DE REGISTROS, PARA EL ITEM, PERIODO, FUNCIONARIO ******************************
			if(count($strIn) > 0){
				if($this->Calificafuncionario->deleteAll(array('Calificafuncionario.item_id' =>$strIn,
															   'Calificafuncionario.subperiodo_id' =>$elPeriodo,
															   'Calificafuncionario.funcionario_id' =>$funcionario_id))){
					$this->Session->setFlash(__('borrado', true));
				}else{
					$this->Session->setFlash(__('Error: <br>'.print_r(error_get_last(),true), true)); break;
				}
			}
			if(count($valorEvaluacion) > 0){
				//***************************** NUEVA INSERSION *****************************
				$this->Calificafuncionario->create();
				if(!$this->Calificafuncionario->saveAll($valorEvaluacion)){
					$arrayErrores =$this->Calificafuncionario->invalidFields();
					$strErrors='';
					foreach($arrayErrores as $listaErrors){ if(!is_array($listaErrors)){$strErrors .= '<br />'.$listaErrors;}}
					$this->Session->setFlash(__('Ocurrio un Error: '.$strErrors, true));
				}else{
					$this->Session->setFlash('Grabado...');
					if( isset($arrayForm['GyV']) ){
						$this->redirect( array('action' => 'Factorfuncionario', 'funcionario_id'=>$funcionario_id, 'elPeriodo' => $elPeriodo) );
					}
				}
			//*** FIN GUARDA LISTADO CON FUNCIONARIOS A EVALUAR ***
			}else{
				$this->Session->setFlash('Sin registros de VALORACION para este periodo/funcionario.');
			}
		} 
		/*** FIN SECCION GUARDAR ***/
		/***************************/		
		$funcionario_id =$this->data['Evaluar']['funcionario_id'];
		$elPeriodo = $this->data['Evaluar']['elPeriodo']; /******* <---- ESTE ES SUBPERIODO, NO CONFUNDIRSE POR EL NOMBRE QUE POSEE ---------***/
		$this->Periodo->recursive=2;
		$periodoIdent = $this->Subperiodo->find('first', array('conditions' => array('Subperiodo.id' => $elPeriodo)) );
		if(is_array($periodoIdent)){
			$periodoIdent = $periodoIdent['Periodo']['id'];
		}
		$periodo = $this->Periodo->Subperiodo->find('all', array('conditions'=> array('Subperiodo.periodo_id'=>$periodoIdent) ) );
		$Subperiodo = $this->Subperiodo->find('all', array('conditions' => array('Subperiodo.id' => $elPeriodo)) );
		$subperiodoId=$Subperiodo[0]['Subperiodo']['id'];
		$ponenota=$Subperiodo[0]['Subperiodo']['ponenota'];
		$elFactor = $this->data['Evaluar']['elFactor'];
		$elIdFuncionario = $this->data['Evaluar']['funcionario_id'];
		$this->Persona->recursive=0;
		$elFuncionario = $this->Persona->find('first', array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')',
																				  'Persona.id_per'=>$elIdFuncionario) ));
		$this->Factor->recursive=4;
		$listaPreguntas = $this->Factor->find('first', array('conditions' => array('Factor.id' => $elFactor)) );
		$colorConcepto = $this->colorConcepto;
		$listaRespEval = $this->Calificafuncionario->find('all', array('conditions' => array('Calificafuncionario.subperiodo_id' =>$elPeriodo,
																							 'Calificafuncionario.funcionario_id' =>$funcionario_id)));
		$listaRespEval2= array();
		foreach($listaRespEval as $valPregunta)$listaRespEval2[]=$valPregunta['Calificafuncionario'];
		foreach($listaRespEval2 as $pnt=>$valPregunta){
			$clave = array_search('1', $listaRespEval2[$pnt]);
			$pregunta_id=$listaRespEval2[$pnt]['pregunta_id'];
			if($clave)$elPnt = $pnt;
		}

		$listaRespEvalOtroPeriodos = $this->Calificafuncionario->find('all', array('conditions' => array('Calificafuncionario.subperiodo_id <>' =>$elPeriodo,
																										 'Calificafuncionario.funcionario_id' =>$funcionario_id)));
		$listaRespEvalOp2= array();
		foreach($listaRespEvalOtroPeriodos as $valPregunta)$listaRespEvalOp2[]=$valPregunta['Calificafuncionario'];

		foreach($listaRespEvalOp2 as $pnt=>$valPregunta){
			$clave = array_search('1', $listaRespEvalOp2[$pnt]);
			$pregunta_id=$listaRespEvalOp2[$pnt]['pregunta_id'];
			if($clave)$elPnt = $pnt;
		}
		$lstSupPer = array();
		foreach($periodo as $lstSubper){$lstSupPer[] = $lstSubper['Subperiodo']['id'];}
		$optionJustFunc = array('conditions'=>array('Justificacionsubperiodo.subperiodo_id'=> $lstSupPer ,
													'Justificacionsubperiodo.funcionario_id'=>$funcionario_id)) ;
		$listaJustFuncTmp = $this->Justificacionsubperiodo->find('all', $optionJustFunc );
		$listaJustFunc=array();
		foreach($listaJustFuncTmp as $lista)$listaJustFunc[] = $lista['Justificacionsubperiodo'];
		
		$preguntaValorTmp = $this->PreguntaValor->find('all');
		$preguntaValor=array();
		foreach($preguntaValorTmp as $pnt => $valorPregval)$preguntaValor[$valorPregval['PreguntaValor']['valor']] = $valorPregval['PreguntaValor']['etiqueta'];
		
		$preguntaValorTmpnota = $this->PreguntaValornota->find('all');
		$preguntaValornota=array();
		foreach($preguntaValorTmpnota as $pnt => $valorPregval)$preguntaValornota[$valorPregval['PreguntaValornota']['valor']] = $valorPregval['PreguntaValornota']['etiqueta'];
		
		$optionSubfac = array('conditions'=>array('Notasubfactor.periodo_id'=> $periodoIdent ,
													'Notasubfactor.funcionario_id'=>$funcionario_id)) ;
		$this->Notasubfactor->recursive=-1;
		$notasSubfacTmp = $this->Notasubfactor->find('all', $optionSubfac);
		foreach($notasSubfacTmp as $valorSubfac)$notasSubfac[(int)$valorSubfac['Notasubfactor']['subfactore_id']] = (int)$valorSubfac['Notasubfactor']['nota'];
		
		$arraySubPeriodos = array();
		foreach($periodo as $lista){
			$arraySubPeriodos[$lista['Subperiodo']['id']] = $lista['Subperiodo']['etiqueta'];
		}
			
		$this->set(compact('colorConcepto', 'listaRespEval', 'listaRespEval2', 'listaRespEvalOp2', 'arraySubPeriodos', 'periodo', 'elPeriodo', 'subperiodoId', 
		'elIdFuncionario', 'elFuncionario', 'elFactor', 'listaPreguntas', 'listaJustFunc', 'preguntaValornota', 'preguntaValor', 'notasSubfac', 'ponenota'));
	}
	
	public function setEvaluacion($listaEvaluacion, $idItem){
		$lanota=0;
		foreach($listaEvaluacion as $nota){if($nota['item_id'] == $idItem)$lanota=$nota['pregunta_id'];}
		return $lanota;
	}

	public function Evaluafuncionario(){
		$parametrs = $this->params['data'];
		$this->Persona->recursive=0;
		$elFuncionario = $this->Persona->find('first', array('conditions' => 
														array('PersonaEstado.calidadJuridica in (1, 2)',
															  'Persona.id_per'=>$parametrs['Evaluafuncionario']['funcionario_id']) ));
		$evaluacionFuncionario = $this->Evaluafuncionario->find('all', 
				array('conditions' => 
					array('Evaluafuncionario.subperiodo_id'=>$parametrs['Evaluafuncionario']['elPeriodo']
						, 'Evaluafuncionario.funcionario_id'=> $parametrs['Evaluafuncionario']['funcionario_id'])
					  )
			);

		$this->set(compact('evaluacionFuncionario', 'elFuncionario'));
	}

	function pdf(){
		Configure::write('debug',0);
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
		$funcionario_id = $this->data['Evaluafuncionarios']['funcionario_id'];
		$idPer = $this->data['Evaluafuncionarios']['periodo_id']; //  $this->params; /******* <---- ESTE ES PERIODO, NO CONFUNDIRSE ---------***/
		
		$this->Persona->recursive=0;
		$elFuncionario = $this->Persona->find('first', array('conditions' => array('PersonaEstado.calidadJuridica in (1, 2)',
																				  'Persona.id_per'=>$funcionario_id) ));
		$listaPeriodos = $this->Periodo->find('first', array('conditions' => array('Periodo.id'=>$idPer) ) );
		$vecSupPers = array();
		foreach($listaPeriodos['Subperiodo'] as $subPer)$vecSupPers[]=$subPer['id'];
		if(isset($listaPeriodos[0]['Periodo']['id'])){
			$idPer = $listaPeriodos[0]['Periodo']['id'];
		}else{
			$idPer = $listaPeriodos['Periodo']['id'];	
		}
		$listaSubPeriodos = $this->Subperiodo->find('all', array('conditions' => array('Subperiodo.periodo_id'=>$idPer) ));
		$this->Factor->recursive=3;
		$paramFactor= array('order'=> 'Factor.id' );
		$listaFactores = $this->Factor->find('all'); 
		$preguntaValorTmp = $this->PreguntaValor->find('all');
		$vecPreguntaValor=array();
		$preguntaValor=array();
		foreach($preguntaValorTmp as $pnt => $valorPregval)$vecPreguntaValor[$valorPregval['PreguntaValor']['id']] = $valorPregval['PreguntaValor']['etiqueta'];
		foreach($preguntaValorTmp as $pnt => $valorPregval)$preguntaValor[$valorPregval['PreguntaValor']['valor']] = $valorPregval['PreguntaValor']['etiqueta'];
		$colorConcepto = $this->colorConceptoPdf;
		/*** VALORACION, JUSTIFICACION, NOTA SUBFACTOR ***/
		/*************************************************************************************************/
		$listaCalificafuncionario = $this->Calificafuncionario->find('all', array('conditions' => array('Calificafuncionario.subperiodo_id' =>$vecSupPers,
																							 'Calificafuncionario.funcionario_id' =>$funcionario_id)));
		$listaRespEval2= array();
		foreach($listaCalificafuncionario as $valPregunta)$listaRespEval2[]=$valPregunta['Calificafuncionario'];
		$lstSupPer = array();
		foreach($periodo as $lstSubper){$lstSupPer[]= $lstSubper['Subperiodo']['id'];}
		$optionJustFunc = array('conditions'=>array('Justificacionsubperiodo.subperiodo_id'=> $vecSupPers,
													'Justificacionsubperiodo.funcionario_id'=>$funcionario_id)) ;
		$listaJustFuncTmp = $this->Justificacionsubperiodo->find('all', $optionJustFunc );
		$listaJustFunc=array();
		foreach($listaJustFuncTmp as $lista)$listaJustFunc[] = $lista['Justificacionsubperiodo'];
		$optionSubfac = array('conditions'=>array('Notasubfactor.periodo_id'=> $idPer ,
													'Notasubfactor.funcionario_id'=>$funcionario_id)) ;
		$this->Notasubfactor->recursive=-1;
		$notasSubfacTmp = $this->Notasubfactor->find('all', $optionSubfac);
		foreach($notasSubfacTmp as $valorSubfac)$notasSubfac[$valorSubfac['Notasubfactor']['subfactore_id']] = $valorSubfac['Notasubfactor']['nota'];
		/*************************************************************************************************/
		
		$preguntaValorTmpnota = $this->PreguntaValornota->find('all');
		$preguntaValornota=array();
		foreach($preguntaValorTmpnota as $pnt => $valorPregval)$preguntaValornota[$valorPregval['PreguntaValornota']['valor']] = $valorPregval['PreguntaValornota']['etiqueta'];
		
		$this->set('colorConcepto', $colorConcepto);
		$this->set('listaPeriodos', $listaPeriodos);
		$this->set('listaSubPeriodos', $listaSubPeriodos);
		$this->set('listaFactores', $listaFactores);
		$this->set('vecPreguntaValor', $vecPreguntaValor);
		$this->set('preguntaValor', $preguntaValor);
		$this->set('preguntaValornota', $preguntaValornota);
		$this->set('listaCalificafuncionario', $listaCalificafuncionario);
		$this->set('listaJustFunc', $listaJustFunc);
		$this->set('notasSubfac', $notasSubfac);
		$this->set('elFuncionario', $elFuncionario);
		$this->render();
	}
	
	function emailPrecalifica($idFunc=null){
		if(0): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND dateadd(m, 1, Subperiodo.mesevalhasta)')	

				, 'order' => array('Subperiodo.mesdesde'=> '')) );
		endif;
		$idPeriodo = $periodoEvaluados['Periodo']['id'];
		$perNombre = $periodoEvaluados['Periodo']['etiqueta'];
		if($periodoEvaluados['Subperiodo']['id']){
			$subPerId = $periodoEvaluados['Subperiodo']['id'];
		}else{
			$subPerId = $this->data['Evaluafuncionarios']['subPeriodo_id']; 
		}
		$subPerNombre = $periodoEvaluados['Subperiodo']['etiqueta'];
		
		$this->Persona->recursive=0;
		$elFuncionario = $this->Persona->find('first', array('conditions' => 
			array('PersonaEstado.calidadJuridica in (1, 2)',
				  'Persona.id_per'=>$this->data['Evaluafuncionarios']['funcionario_id']) ));
		
		$lugarReunion= array('Oficina de Jefatura'=>'Oficina de Jefatura', 'Otro lugar'=>'Otro lugar');
		$texto='Junto con saludarlo, le informamos que ha sido citado a una reunion para revisar la Pre-calificación periodo '.$subPerNombre.' que se ha realizado.';
		
		if(!empty($this->data)){
			if(isset($this->data['msg'])){
				$para=$this->data['msg']['email'];
				//$para='jaracena@gorecoquimbo.cl';
				//$nombreF=utf8_decode($this->data['msg']['nombreF']);
				$nombreF=($this->data['msg']['nombreF']);
				$lugar=$this->data['msg']['lugar'];
				if($lugar == 'Otro lugar'){
					$lugar=$this->data['msg']['otrolugar'];
				}
				$fecha=$this->data['msg']['fecha'];
				$hora=$this->data['msg']['hora']['hour'];
				$minuto=$this->data['msg']['minuto']['min'];
				
				$body = 'Estimado(a) '.$nombreF.' <br /><br />'.$texto.'<br /><br />El dia '.$fecha.', a las '.$hora.':'.$minuto.' hrs., en '.$lugar
				.'.<br /><br />Sin otro particular.<br />Me despido.<br />Atentamente.';
				
				$Funciones = new Funcionespropias();
				if($Funciones->enviaCorreo($body, 'Citación Pre-evaluación', $para )){
					$this->Session->setFlash('Email Enviado a ...&nbsp;'.$para);
				}else{
					$this->Session->setFlash('Ocurrio un error, no pudo enviarse el correo:<pre>'.print_r(error_get_last(), 1).'</pre>');
					$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'idPeriodo', 'elFuncionario', 'lugarReunion', 'texto'));
				}
			}
		}
		
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'idPeriodo', 'elFuncionario', 'lugarReunion', 'texto'));
	}
	
}
