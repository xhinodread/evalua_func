<?php
class PersonasController extends AppController {
	public $uses = array('Persona', 'Historia', 'Subperiodo', 'Factor', 'Evaluafuncionario', 'Calificadore', 'Precalificadore'
					, 'Funcsinprecalif', 'JuntaEvaluadore', 'Chknota', 'FirmasHojacalifica', 'Calificacionfuncionario');
	var $helpers = array('Html', 'Form');
	//var $componets = array('Session');
	var $componets = array('Session', 'Auth');
	//var $name = 'Personas';
	//var $scaffold;
	var $paginate = array(
        'limit' => 5,
        'order' => array(
            array('persona.rut' => 'asc')
        )
    );
	
	public $arrayEscalfones = array(
								 1=>'Directivo'
								,3=>'Profesional'
								,4=>'Técnico'
								,5=>'Administrativo'
								,6=>'Auxiliar'
							);
	
	function index() {
		//$this->Persona->recursive = 1;
		//$this->set('personas', $this->paginate());
		$listaPersonas = $this->Persona->find('all');
		$this->set('listaPersonas', $listaPersonas);
	}
	
	function ver($id = null){
		if(!$id){
			throw new NotFoundException('Datos no validos'); }
		$detallePers = $this->Persona->find(array('persona.id_per'=>$id));
		//$detalleEstPers = $this->PersonaEstado->find(array('PersonaEstado.id_per' => $id));
		if(!$detallePers){
			throw new NotFoundException('Sin info'); }
		$this->set('detallePers', $detallePers);
	}

	function seleccionaEvaluados(){
		$listaFuncSinPrecConObs = array();
		//*** SECCION QUE GUARDA ***
		if(!empty($this->data)){
			//echo '$this->data: <pre>'.print_r($this->data, true).'</pre><hr>';
			$PeriodoEvalua= $this->data['Persona']['PeriodoEvalua'];
			///echo '->PeriodoEvalua: '.$PeriodoEvalua.'<br />';
			unset($this->data['Persona']['PeriodoEvalua']);
			$arrayPrecal=array();
			////echo '$this->data: <pre>'.print_r($this->data['precalif'], true).'</pre><hr>';
			foreach($this->data['precalif'] as $pntr => $chks){
				//echo '-> '.$pntr.' => '.$chks.'<br />';
				if($chks){
					//echo '-> '.$pntr.' => '.$chks.'<br />';
					$arrayPrecal[$pntr]=$chks;
				}
			}
			//echo 'arrayPrecal: <pre>'.print_r($arrayPrecal, true).'</pre><hr>';
			
			//*** LISTADO DE FUNCIONARIOS SELECCIONADOS PARA EVALUACION ***
			////foreach($this->data['Persona'] as $datoFuncionarios){if($datoFuncionarios > 0)echo $datoFuncionarios.'<br />';}
			$this->Factor->recursive=-1;
			$listaFactores = $this->Factor->find('all');
			/**************************************************************************/
			//*** CREA array() CON LISTADO DE FUNCIONARIOS SELECCIONADOS PARA EVALUACION ***
			$arrayGeneral = array();
			$arrayGeneralAUX = array();
			$arrayPntAUX = array();
			$listaParaEvaluar = array();
			//*** array() CON LISTADO DE FUNCIONARIOS SELECCIONADOS PARA EVALUACION ***
			//echo '$this->data[Persona]: <pre>'.print_r($this->data['Persona'], true).'</pre><hr>';
			$auxIDFunc = -1;
			$cntAux = 0;
			foreach($this->data['Persona'] as $datoFuncionarios){
				//echo '* '.$datoFuncionarios.'<br />';
				if($datoFuncionarios > 0){
					$arrayPntAUX[$datoFuncionarios]=$datoFuncionarios;
					$arrayGeneralAUX[]=$datoFuncionarios;
				}
			}
			$valAux=0;
			sort($arrayPntAUX);
			foreach($arrayPntAUX as $lisPntFunc){
				foreach($arrayGeneralAUX as $lisGenAux){
					if($lisPntFunc == $lisGenAux){
						$valAux++;
						
					}
				}
				//echo $lisPntFunc.', '.$valAux.'<br />';
				
				if($valAux == 1){
					foreach($listaFactores as $datoFactores){
						if($datoFactores['Factor']['id'] != 1){
							$listaParaEvaluar['funcionario_id'] = $lisPntFunc;
							$listaParaEvaluar['factore_id'] = $datoFactores['Factor']['id'];
							$listaParaEvaluar['subperiodo_id'] = $PeriodoEvalua;
							////$listaParaEvaluar['precalificadore_id'] = '1';
							if( array_key_exists($lisPntFunc, $arrayPrecal) ){
								$listaParaEvaluar['precalificadore_id'] = $arrayPrecal[$lisPntFunc];
							}else{
								$listaParaEvaluar['precalificadore_id'] = '';
							}
							$arrayGeneral[]['Evaluafuncionario'] = $listaParaEvaluar;
						}	
					}
				}else{
					foreach($listaFactores as $datoFactores){
							$listaParaEvaluar['funcionario_id'] = $lisPntFunc;
							$listaParaEvaluar['factore_id'] = $datoFactores['Factor']['id'];
							$listaParaEvaluar['subperiodo_id'] = $PeriodoEvalua;
							if( array_key_exists($lisPntFunc, $arrayPrecal) ){
								$listaParaEvaluar['precalificadore_id'] = $arrayPrecal[$lisPntFunc];
							}else{
								$listaParaEvaluar['precalificadore_id'] = '';
							}
							$arrayGeneral[]['Evaluafuncionario'] = $listaParaEvaluar;
					}
				}
				$valAux=0;
			}
			
		   if(0):
			echo 'arrayGeneral: <pre>'.print_r($arrayGeneral, true).'</pre><hr>';
		   else:
			//***************************** BORRADO DE REGISTRO, PARA NUEVA INSERSION *****************************
			$dataSource = $this->Evaluafuncionario->getDataSource(); //Dudoso***Tal vez se puede eliminaes esta linea***
			//$this->Evaluafuncionario->read('subperiodo_id', $PeriodoEvalua);
			$dataSource->begin($this->Evaluafuncionario);//Dudoso***Tal vez se puede eliminaes esta linea***
			//echo '<br />Array a borrar <pre>'.print_r($dataSource, true).'</pre><hr>';
			if($this->Evaluafuncionario->deleteAll(array('Evaluafuncionario.subperiodo_id' =>$PeriodoEvalua))){
			//if ($this->Evaluafuncionario->delete()) {
				//$dataSource->commit($this->Evaluafuncionario);
				$this->Evaluafuncionario->commit();//Dudoso***Tal vez se puede eliminaes esta linea***
				$this->Session->setFlash(__('borrado', true));
			}else{
				//$dataSource->rollback($this->Evaluafuncionario);
				$this->Evaluafuncionario->rollback();//Dudoso***Tal vez se puede eliminaes esta linea***
				$this->Session->setFlash(__('cancelado', true));
			}

			if(count($arrayGeneral) > 0){
				//***************************** NUEVA INSERSION *****************************
				$this->Evaluafuncionario->create();
				if(!$this->Evaluafuncionario->saveAll($arrayGeneral)){
					///$this->Session->setFlash('Error');
					///$arrayErrores = print_r($this->Evaluafuncionario->invalidFields(), true);
					$arrayErrores =$this->Evaluafuncionario->invalidFields();
					$strErrors='';
					foreach($arrayErrores as $listaErrors){ if(!is_array($listaErrors)){$strErrors .= '<br />'.$listaErrors;} }
					$this->Session->setFlash(__('Ocurrio un Error: '.$strErrors, true));
				}else{
					$this->Session->setFlash('Grabado. '.print_r($arrayGeneral[150] ,1));
					$this->redirect( array('action' => 'seleccionaEvaluados', 'perId' => $PeriodoEvalua) );
				}
			//*** FIN GUARDA LISTADO CON FUNCIONARIOS A EVALUAR ***
			}else{
				$this->Session->setFlash('Sin registros para este periodo.');
				$this->redirect( array('action' => 'seleccionaEvaluados', 'perId' => $PeriodoEvalua) );
			}
		   endif;
		}
		//******************************** FIN SECCION QUE GUARDA ********************************
		
		$elPeriodo =null;
		$arraFuncActuals= array();
		if(count($this->passedArgs) > 0){
			//echo '<pre>'.print_r($this->passedArgs, true).'</pre><hr>';
			$elSubPeriodo =$this->passedArgs['perId'];
			$this->Evaluafuncionario->recursive=-1;
			$periodoEvaluadosActuales = $this->Evaluafuncionario->find('all', array('conditions' => array('Evaluafuncionario.subperiodo_id' => $elSubPeriodo) ) );
			//echo 'lista actuales:<pre>'.print_r($periodoEvaluadosActuales, true).'</pre><hr>';
			$tmpArrayFuncActuals= array();
			//$arraFuncActuals= array();
			foreach($periodoEvaluadosActuales as $listaFuncActuals){
				$tmpArrayFuncActuals[]=$listaFuncActuals['Evaluafuncionario']['funcionario_id'];
			}
			//echo 'tmp lista actuales:<pre>'.print_r($tmpArrayFuncActuals, true).'</pre><hr>';
			$arraFuncActuals = array_unique($tmpArrayFuncActuals);
			///echo 'lista actuales:<pre>'.print_r($arraFuncActuals, true).'</pre><hr>';
		}
		
		 if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
	    else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND Subperiodo.mesevalhasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
		endif;
		
		$listaSelecEvaluados = $this->Persona->find('all', 
			array('conditions' => array('PersonaEstado.calidadJuridica in ('.$this->calidJuridSinHonorarios.')', 'PersonaEstado.per_estado = 1')
			, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')));
				
		$preCalificadores = $this->Precalificadore->find('all');
		///echo 'preCalificadores: <pre>'.print_r($preCalificadores, 1).'</pre><hr>';
		
		$listaPrecalificadores=array();
		//$listaPrecalificadores['empty'] = 'Seleccione uno';
		foreach($preCalificadores as $listaPre){
			$listaPrecalificadores[$listaPre['Precalificadore']['funcionario_id']] = utf8_encode($listaPre['Persona']['NOMBRES'].' '.$listaPre['Persona']['AP_PAT'].' '.$listaPre['Persona']['AP_MAT']);
		}
		//echo 'listaPrecalificadores: <pre>'.print_r($listaPrecalificadores, 1).'</pre><hr>';
		
		//echo 'elPeriodo: <pre>'.print_r($elPeriodo, 1).'</pre><hr>';
		
		$asignacionSubPeriodoAnterior = array();
		if($elSubPeriodo){
			$nroAsignaciones = $this->Evaluafuncionario->find('count', 
				array('conditions' => array('Evaluafuncionario.subperiodo_id' => $elSubPeriodo)
			 ));
		    //$asignacionSubPeriodoAnterior[0] = $nroAsignaciones;
			//if($nroAsignaciones == 0)
				$asignacionSubPeriodoAnterior = $this->Subperiodo->traeAsignacionSubPerAnterior($elSubPeriodo);
		}

		//echo 'listaSelecEvaluados: <pre>'.print_r($listaSelecEvaluados, 1).'</pre><hr>';
		
		$listaFuncSinPrecConObsTmp = $this->Funcsinprecalif->find('all', array('conditions' => array('Funcsinprecalif.subperiodo_id' => $elSubPeriodo) ) );
		// echo 'listaFuncSinPrecConObsTmp<pre>'.print_r($listaFuncSinPrecConObsTmp, 1).'</pre>';
		foreach($listaFuncSinPrecConObsTmp as $lista)
			$listaFuncSinPrecConObs[$lista['Funcsinprecalif']['funcionario_id']] = ($lista['Funcsinprecalif']['observacion']);
		// echo 'listaFuncSinPrecConObs<pre>'.print_r($listaFuncSinPrecConObs, 1).'</pre>';
		

		$this->set(compact('listaSelecEvaluados', 'periodoEvaluados', 'elSubPeriodo', 'arraFuncActuals', 'listaPrecalificadores', 'asignacionSubPeriodoAnterior', 'nroAsignaciones', 'listaFuncSinPrecConObs'));
	}

	function funcionariosAsignados(){
		$valorsPeriodos = array();
		$listaPrecalificadores = array();
		$listaFuncSinPrecConObs = array();
		$elSubPeriodo = null;
		$elPrecalificador = null;
		$listaPreEvaluadosActuales = array();
		$listaPreEvaluadosActualesOtrosPrecal = array();
		$listaPersonas=array();
		
		//echo 'this->passedArgs<br />'.$this->printR($this->passedArgs);
		//****** SECCION QUE GUARDA ******
		$varFlash ='';
		if(!empty($this->data)){
			$this->Factor->recursive = -1;
			$listaFactores = $this->Factor->find('all', array('fields' => 'Factor.id', 'order' => 'Factor.id') );
			if(0):
				$varFlash .= 'this->data<br />'.$this->printR($this->data);
			else:
				$elSubPeriodo = $this->data['Persona']['subperiodo_id'];
				$elPrecalificador = $this->data['Persona']['precalificadore_id'];
				//$varFlash .= 'elSubPeriodo:'.$elSubPeriodo.'<br />elPrecalificador: '.$elPrecalificador.'<br />';
				
				$arrayCompara = array(0=>0);
				$resultadoChkIn = array_diff($this->data['chkIn'], $arrayCompara);
				//$varFlash .= 'chkIn: '.print_r($resultadoChkIn,1).'<br />';
				
				if( isset($this->data['chkOut']) ){
					$resultadoChkOut = array_diff($this->data['chkOut'], $arrayCompara);
					//$varFlash .= 'chkOut: '.print_r($resultadoChkOut,1).'<br />';
				}
				
				$resultadoChklIdOut=null;
				if( isset($this->data['chklIdOut']) ){
					$resultadoChklIdOut = array_diff($this->data['chklIdOut'], $arrayCompara);
					//$varFlash .= 'chklIdOut: '.print_r($resultadoChklIdOut,1).'<br />';
				}
				
				$varDeleteAll = array('conditions' => array('Evaluafuncionario.precalificadore_id' => $elPrecalificador
									, 'Evaluafuncionario.subperiodo_id' => $elSubPeriodo) );
				if(count($resultadoChkIn) > 0){
					$varDeleteAll = array('conditions' => array('Evaluafuncionario.precalificadore_id' => $elPrecalificador
														, 'Evaluafuncionario.subperiodo_id' => $elSubPeriodo 
														, 'NOT' => array('Evaluafuncionario.funcionario_id' => $resultadoChkIn) ) );
				}
				$varFlash .= 'varDeleteAll: '.print_r($varDeleteAll['conditions'],1).'<br />';
				
				
				//$varFlash .= 'listaFactores: '.print_r($listaFactores,1).'<br />';
				
				$arrayInserta = array();
				foreach($resultadoChkOut as $listaChkOutIdFunc){
					if( count($resultadoChklIdOut) > 0 && in_array($listaChkOutIdFunc, $resultadoChklIdOut) ){
						foreach($listaFactores as $lista){
							$arrayInserta[]['Evaluafuncionario'] = array( 'funcionario_id' => $listaChkOutIdFunc
																		, 'factore_id' => $lista['Factor']['id']
																		, 'subperiodo_id' => $elSubPeriodo
																		, 'precalificadore_id' => $elPrecalificador);
						}
					}else{
						for($x=1; $x<=count($listaFactores)-1; $x++){
							$arrayInserta[]['Evaluafuncionario'] = array( 'funcionario_id' => $listaChkOutIdFunc
																		, 'factore_id' => $listaFactores[$x]['Factor']['id']
																		, 'subperiodo_id' => $elSubPeriodo
																		, 'precalificadore_id' => $elPrecalificador);
						}
					}
				}
				$varFlash .= 'arrayInserta: '.count($arrayInserta).' <pre>'.print_r($arrayInserta,1).'</pre>';
				
				/*
				/// PARA BORRAR LOS EXISTENTES ///
				$this->Evaluafuncionario->recursive=-1;
				$varDeleteAll = $this->Evaluafuncionario->find('all', $varDeleteAll);
				$varFalsh .= '<br />tmpListaPreEvaluadosActuales: <pre>'.print_r($varDeleteAll,1).'</pre>';
				*/
				
				$varFlash .= 'Hecho...'.'<br />';
				
				$varFlash .= 'ErrorMsg: '.print_r($varDeleteAll['conditions'],1).'<br />';
				$varFlash .= 'ErrorLast: '.print_r(error_get_last(),1).'<br />';
				
				$varFlash = 'Sin cambios.'; //'Error el borrado';
				if( $this->Evaluafuncionario->deleteAll($varDeleteAll['conditions'], false) ){
					$varFlash = 'Cambio hecho...'.'<br />';
				}
				
				if( count($arrayInserta) > 0 ){
					$varFlash = 'Sin cambios al agregar'; //'Error al agregar';
					if( $this->Evaluafuncionario->saveAll($arrayInserta) ){
						$varFlash = 'Funcionario/Precalificador aplicado...'.'<br />';
					}
				}
			endif;
			$this->Session->setFlash($varFlash);
			$this->redirect(array('controller' => 'personas', 'action' => 'funcionariosAsignados', 'subperId'=> $elSubPeriodo, 'precalifId'=> $elPrecalificador) );
		}
		//****** FIN SECCION QUE GUARDA ******
		if(count($this->passedArgs) > 0){
			$elSubPeriodo = $this->passedArgs['subperId'];
			$elPrecalificador = ( isset($this->passedArgs['precalifId']) ? $this->passedArgs['precalifId'] : null );
		}elseif(!empty($this->data)){
			$elSubPeriodo = $this->data['Persona']['subperiodo_id'];
			$elPrecalificador = $this->data['Persona']['precalificadore_id'];
		}
		if( $elSubPeriodo && $elPrecalificador){
			$this->Evaluafuncionario->recursive=-1;
			$tmpListaPreEvaluadosActuales = $this->Evaluafuncionario->find('all', array('fields' => ' DISTINCT Evaluafuncionario.funcionario_id'
																					,'conditions' => array('Evaluafuncionario.subperiodo_id' => $elSubPeriodo
																						, 'Evaluafuncionario.precalificadore_id' => $elPrecalificador) 
																					 ) );
																					 
			$tmpListaPreEvaluadosOtrosPrecal = $this->Evaluafuncionario->find('all', array('fields' => ' DISTINCT Evaluafuncionario.funcionario_id'
																					,'conditions' => array('Evaluafuncionario.subperiodo_id' => $elSubPeriodo
																					, 'NOT' => array('Evaluafuncionario.precalificadore_id' => $elPrecalificador) )																					
																					 ) );

			$this->Persona->recursive = 1;
			$this->Persona->order = 'Persona.AP_PAT ';
			$tmpListaPersonas = $this->Persona->find('all', array('order' => 'Persona.AP_PAT'
																,'conditions' => array('PersonaEstado.per_estado' => 1
																					,'PersonaEstado.calidadJuridica' => ( explode(',',$this->calidJuridSinHonorarios) ) ) ) );
			foreach($tmpListaPersonas as $lista){
				$listaPersonas[$lista['Persona']['ID_PER']] = $lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT'];
			}
			
			foreach($tmpListaPreEvaluadosActuales as $lista){
				if( array_key_exists($lista['Evaluafuncionario']['funcionario_id'], $listaPersonas) ){
					$listaPreEvaluadosActuales[$lista['Evaluafuncionario']['funcionario_id']] = $listaPersonas[$lista['Evaluafuncionario']['funcionario_id']];
				}
			}
			
			foreach($tmpListaPreEvaluadosOtrosPrecal as $lista){
				if( array_key_exists($lista['Evaluafuncionario']['funcionario_id'], $listaPersonas) ){
					$listaPreEvaluadosActualesOtrosPrecal[$lista['Evaluafuncionario']['funcionario_id']] = $listaPersonas[$lista['Evaluafuncionario']['funcionario_id']];
				}
			}
		}
	    if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
	    else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND Subperiodo.mesevalhasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
		endif;
	    foreach($periodoEvaluados as $listaSubPeriodos)
			$valorsPeriodos[$listaSubPeriodos['Subperiodo']['id']] = $listaSubPeriodos['Subperiodo']['etiqueta'].' / '
				.date('Y', strtotime($listaSubPeriodos['Subperiodo']['mesdesde']));
		
		$this->Precalificadore->order = 'Precalificadore.funcionario_id ';
		$preCalificadores = $this->Precalificadore->find('all');
		foreach($preCalificadores as $listaPre)
			$listaPrecalificadores[$listaPre['Precalificadore']['funcionario_id']] = utf8_encode($listaPre['Persona']['NOMBRES'].' '
				.$listaPre['Persona']['AP_PAT'].' '.$listaPre['Persona']['AP_MAT']);
				
		$listaFuncSinPrecConObsTmp = $this->Funcsinprecalif->find('all', array('conditions' => array('Funcsinprecalif.subperiodo_id' => $elSubPeriodo) ) );
		//echo 'listaFuncSinPrecConObsTmp<pre>'.print_r($listaFuncSinPrecConObsTmp, 1).'</pre>';
		foreach($listaFuncSinPrecConObsTmp as $lista)
			$listaFuncSinPrecConObs[$lista['Funcsinprecalif']['funcionario_id']] = ($lista['Funcsinprecalif']['observacion']);
		//echo 'listaFuncSinPrecConObs<pre>'.print_r($listaFuncSinPrecConObs, 1).'</pre>';

		$this->set(compact('listaPrecalificadores', 'valorsPeriodos', 'elSubPeriodo', 'elPrecalificador', 'listaPreEvaluadosActuales'
					, 'listaPersonas', 'listaPreEvaluadosActualesOtrosPrecal', 'listaFuncSinPrecConObs'));
	}
	
	function funcionariosSinprecalificador(){
		$valorsPeriodos = array();
		$varFlash = 'Sin Cambios.';
		$elSubPeriodo = '';
		$listaPreEvaluadosSolos = array();
		$listaPersonas=array();
		$listaFuncSinPrecConObs = array();
		//echo 'this->passedArgs<br />'.$this->printR($this->passedArgs);
		//****** SECCION QUE GUARDA ******
		if(!empty($this->data)){
			$elSubPeriodo = $this->data['funcionariosSinprecalificador']['subperiodo_id'];
			$this->Factor->recursive = -1;
			$listaFactores = $this->Factor->find('all', array('fields' => 'Factor.id', 'order' => 'Factor.id') );
			if(0):
				$varFlash .= 'funcionariosSinprecalificador: <br />this->data<br />'.$this->printR($this->data);
			else:
				$arrayGuarda = array();
				$arrayFuncId = $this->data['funcionario_id'];
				$arrayFuncObservacion = ( isset($this->data['observacion']) ? $this->data['observacion'] : array() );

				foreach($arrayFuncId as $lista){
					if( isset($arrayFuncObservacion[$lista]) && strlen($arrayFuncObservacion[$lista]) > 0 ){
						$arrayGuarda[]['Funcsinprecalif'] = array ( 'subperiodo_id' => $elSubPeriodo
																				, 'funcionario_id' => $lista
																				, 'observacion' => $arrayFuncObservacion[$lista] );
					}
				}
				
				$swfFlash = 0;
				if(count($arrayGuarda)>0){
					foreach($arrayGuarda as $lista){
						
						$nroRgistros = count($this->Funcsinprecalif->findAllBySubperiodo_idAndFuncionario_id($lista['Funcsinprecalif']['subperiodo_id']
																											, $lista['Funcsinprecalif']['funcionario_id']));
																											
						if($nroRgistros > 0 ){
							$this->Funcsinprecalif->updateAll( array('Funcsinprecalif.observacion' => "'".$lista['Funcsinprecalif']['observacion']."'" )
															 , array('Funcsinprecalif.subperiodo_id' => $lista['Funcsinprecalif']['subperiodo_id']
															 	   , 'Funcsinprecalif.funcionario_id' => $lista['Funcsinprecalif']['funcionario_id']) );
							$varFlash .= 'Actualizado <br />';
							$swfFlash = 1;

						}else{
							if( $this->Funcsinprecalif->saveAll($lista) ){
								//$varFlash .= 'S>: '.print_r($lista, 1).'<br />';
								$varFlash .= 'Agregado <br />';
								$swfFlash = 1;
							}else{
								$varFlash .= $lista['Funcsinprecalif']['funcionario_id'].'<br />';
								$swfFlash = 0;
							}
						}
					}
					$varFlash .= 'ATENCIÓN!!!: No se pudo registrar el cambio.<br />';
					if($swfFlash == 1){
						$varFlash = 'Registro Actualizado <br />';
					}
				}
			endif;
			$this->Session->setFlash($varFlash);
			$this->redirect(array('controller' => 'personas', 'action' => 'funcionariosSinprecalificador'
																, 'subperId'=> $elSubPeriodo) );
		}
		//****** FIN SECCION QUE GUARDA ******
		if(count($this->passedArgs) > 0){
			$elSubPeriodo = $this->passedArgs['subperId'];
		}elseif(!empty($this->data)){
			$elSubPeriodo = $this->data['Persona']['subperiodo_id'];
		}
		if( $elSubPeriodo ){
			$this->Evaluafuncionario->recursive=-1;
			$tmpListaPreEvaluadosSolos = $this->Evaluafuncionario->find('all', array('fields' => ' DISTINCT Evaluafuncionario.funcionario_id'
																					,'conditions' => array('Evaluafuncionario.subperiodo_id' => $elSubPeriodo)) );
			$this->Persona->recursive = 1;
			$this->Persona->order = 'Persona.AP_PAT ';
			$tmpListaPersonas = $this->Persona->find('all', array('order' => 'Persona.AP_PAT'
																,'conditions' => array('PersonaEstado.per_estado' => 1
																					,'PersonaEstado.calidadJuridica' =>
																							 (explode(',' ,$this->calidJuridSinHonorarios)) ) ) );
			
			foreach($tmpListaPersonas as $lista){
				$listaPersonas[$lista['Persona']['ID_PER']] = $lista['Persona']['NOMBRES'].' '.$lista['Persona']['AP_PAT'].' '.$lista['Persona']['AP_MAT'];
			}
			
			foreach($tmpListaPreEvaluadosSolos as $lista){
				if( array_key_exists($lista['Evaluafuncionario']['funcionario_id'], $listaPersonas) ){
					$listaPreEvaluadosSolos[$lista['Evaluafuncionario']['funcionario_id']] = $listaPersonas[$lista['Evaluafuncionario']['funcionario_id']];
				}
			}	
		}
	    if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
	    else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('all', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND Subperiodo.mesevalhasta')
				, 'order' => array('Subperiodo.mesdesde'=> '')) );
		endif;
	    foreach($periodoEvaluados as $listaSubPeriodos)
			$valorsPeriodos[$listaSubPeriodos['Subperiodo']['id']] = $listaSubPeriodos['Subperiodo']['etiqueta'].' / '
				.date('Y', strtotime($listaSubPeriodos['Subperiodo']['mesdesde']));
		
		$listaFuncSinPrecConObsTmp = $this->Funcsinprecalif->find('all', array('conditions' => array('Funcsinprecalif.subperiodo_id' => $elSubPeriodo) ) );
		// echo 'listaFuncSinPrecConObsTmp<pre>'.print_r($listaFuncSinPrecConObsTmp, 1).'</pre>';
		 foreach($listaFuncSinPrecConObsTmp as $lista)
			$listaFuncSinPrecConObs[$lista['Funcsinprecalif']['funcionario_id']] = ($lista['Funcsinprecalif']['observacion']);
		/// echo 'listaFuncSinPrecConObs<pre>'.print_r($listaFuncSinPrecConObs, 1).'</pre>';


		$this->set(compact('valorsPeriodos', 'elSubPeriodo', 'listaPersonas', 'listaPreEvaluadosSolos', 'listaFuncSinPrecConObs'));
	}

	function listaEvaluados(){
		//$this->Persona->recursive = 2;
		$listaEvaluados = $this->Persona->find('all', array(
			'conditions' => array(
				'PersonaEstado.calidadJuridica in (1, 2 ,4 ,5)',
				'Persona.ID_PER in (676) ')));
		$this->set('listaEvaluados', $listaEvaluados);
	}
	
	function pdf(){
		  Configure::write('debug',1);
		  $this->layout = 'pdf'; //this will use the pdf.ctp layout
		  // Operaciones que deseamos realizar y variables que pasaremos a la vista.
		  $this->render();
	}
	
	function hojaDeVida(){
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND DATEADD(m, 2, Subperiodo.mesevalhasta)')
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
		// $datosSession = $this->Session->read('personaDatos');
		$idPer = $datosSession['PersonaEstado']['id_per'];		
		$rutPer = $datosSession['PersonaEstado']['per_rut'];
		//echo 'datosSession:<pre>'.print_r($datosSession,1).'</pre>'.'periodoEvaluados<pre>'.print_r($periodoEvaluados, true).'</pre><hr>';
		
		// echo 'idPer: '.$idPer;
		// if( $idPer == 444)$rutPer = 13949959;
		$this->Persona->recursive=-1;
		$condiciones= array('conditions'=>array('Persona.rut = '.$rutPer ) );
		$datosPersona = $this->Persona->find('first', $condiciones);
		//echo 'datosPersona:<pre>'.print_r($datosPersona,1).'</pre>';
		
		$precalificadorNombre = 'Sin Precalificador asignado.';
		//if( $idPer == 444)$idPer = 17;
		//echo 'idPer:<pre>'.print_r($idPer,1).'</pre>'.'subPerId<pre>'.print_r($subPerId, true).'</pre><hr>';
		$this->Evaluafuncionario->recursive = -1;
		$precalificadorFuncionario = $this->Evaluafuncionario->find('first', array('conditions' => array('Evaluafuncionario.funcionario_id' => $idPer
																								, 'Evaluafuncionario.subperiodo_id' => $subPerId) ) );
		//echo 'precalificadorFuncionario<pre>'.print_r($precalificadorFuncionario, true).'</pre><hr>';
		$this->PersonaEstado->recursive = 2;
		$precalificadorDatos = $this->Persona->find('first', array('conditions'=> array('Persona.id_per' => $precalificadorFuncionario['Evaluafuncionario']['precalificadore_id'] ) ) );
		//echo count($precalificadorDatos).'precalificadorDatos<pre>'.print_r($precalificadorDatos, true).'</pre><hr>';
		
	
		if( isset($precalificadorDatos['Persona']['NOMBRES']) ){
			$precalificadorNombre = substr($precalificadorDatos['Persona']['NOMBRES'], 0, 10).'. '
									.$precalificadorDatos['Persona']['AP_PAT'].' '.$precalificadorDatos['Persona']['AP_MAT'];
		}
		//echo 'precalificadorNombre<pre>'.print_r($precalificadorNombre, true).'</pre><hr>';

		/*** SOLO PARA DESARROLLO ***/
		// if($idPer == 444)$idPer=17;
		// echo 'idPer: '.$idPer;
		$listaHistoria = $this->Historia->traeFuncionariosHistoria($idPer);
		$listaHistoria = $listaHistoria[0][0];
		$listaHistoria['NOMBRE'] = $datosPersona['Persona']['NOMBRES'].' '.$datosPersona['Persona']['AP_PAT'].' '.$datosPersona['Persona']['AP_MAT'];
		//echo 'listaHistoria:<pre>'.print_r($listaHistoria,1).'</pre>';
		
		//echo 'datosSession:<pre>'.print_r($perNombre.', '.$subPerNombre.', '.$subPerId, 1).'</pre>';
		//echo 'listaHistoria:<pre>'.print_r($listaHistoria, 1).'</pre>';
		
		$this->set(compact('perNombre', 'subPerNombre', 'subPerId', 'listaHistoria', 'precalificadorNombre'));
	}
	
	public function miembrosJuntacalificadora(){
		/***** SECCION GUARDA *****/
		if(!empty($this->data) ){
			$miembrosJunta = array();
			
			foreach( $this->data['slc_comision'] as $ind => $lista ){
				if( strlen($lista) > 0  ){
					$miembrosJunta['JuntaEvaluadore'][] = array('funcionario_id' => $ind, 'tipo_id' => $lista );
				}
			}
			$cuentaRegistrosJuntaCalif = $this->JuntaEvaluadore->find('count');
			echo $this->Session->setFlash('No se ha Grabado aun'.'JuntaEvaluadore:<pre>'.print_r($miembrosJunta,1).'</pre> ->'
				.$this->JuntaEvaluadore->useTable
				.'<br /> - '.$cuentaRegistrosJuntaCalif 
			);
			
			if( $cuentaRegistrosJuntaCalif > 0 ){
				$this->JuntaEvaluadore->query('TRUNCATE TABLE '.$this->JuntaEvaluadore->useTable);
			}
			if( $this->JuntaEvaluadore->saveAll($miembrosJunta['JuntaEvaluadore']) ){
				echo $this->Session->setFlash('Grabado...');
			}
		}
		/***** FIN SECCION GUARDA *****/
		
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND DATEADD(m, 10, Subperiodo.mesevalhasta)')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		$listaFuncionariosCalificadores = $this->Calificadore->find('all');
		
		//$miembrosJunta = array(1 => 'Integrante Junta', 2 => 'Representante Personal', 3 => 'Representante Asociación', 4 => 'Secretario(a) Junta');
		
		$Funcionespropias = new Funcionespropias();
		$listamiembrosJuntaTmp = $this->JuntaEvaluadore->find('all');
		$listamiembrosJunta = $Funcionespropias->arrayInPuntero($listamiembrosJuntaTmp, 'funcionario_id', 'JuntaEvaluadore', 'tipo_id');
		
		// $this->set(compact('perNombre', 'subPerNombre', 'idTipoIntegrante', 'miembrosJunta', 'listaFuncionariosCalificadores', 'listamiembrosJunta'));
		
		$this->set(compact('perNombre', 'subPerNombre', 'idTipoIntegrante', 'listaFuncionariosCalificadores', 'listamiembrosJunta'));
	}


	public function listaMiembroFuncionario(){
		$Funcionespropias = new Funcionespropias();
		
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND DATEADD(m, 10, Subperiodo.mesevalhasta)')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		$listaFuncionariosCheck = $Funcionespropias->arrayIn($this->Chknota->find('all', array('conditions' => array('Chknota.periodo_id' => $perId) ) )
															, 'Chknota', 'funcionario_id');
		// echo 'listaFuncionariosCheck<pre>'.print_r($listaFuncionariosCheck, 1).'</pre><hr>';
		
		$this->Persona->recursive = 0;
		$listaFuncionarios = $this->Persona->find('all', array('conditions' => 
																array('PersonaEstado.calidadJuridica in (1, 2)', 'PersonaEstado.per_estado = 1'
																		, 'Persona.ID_PER' => $listaFuncionariosCheck )
																, 'order' => array('Persona.NOMBRES', 'Persona.AP_PAT', 'Persona.AP_MAT')
																, 'fields' => array('Persona.*', 'PersonaEstado.*') ));
		// echo 'listaSelecEvaluados<pre>'.print_r($listaFuncionarios, 1).'</pre><hr>';
		
		$this->set(compact('perNombre', 'subPerNombre', 'listaFuncionarios' ));
	}
	
	public function asignaMiembroFuncionario($funcionario_id = null){
		//echo 'data<pre>'.(empty($this->data)).'</pre><hr>';
		//echo 'data<pre>'.print_r($this->data, 1).'</pre><hr>';
		//echo 'funcionario_id<pre>'.print_r($funcionario_id, 1).'</pre><hr>';
		
		//*** SECCION QUE GUARDA ***
		if( !empty($this->data) ){
		//	echo 'dataPost<hr>';
		}
		if($this->swPeriodoEvaluados): /*** POR PERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Periodo.desde AND Periodo.hasta')
				, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		else: /*** POR SUBPERIODO ***/
			$periodoEvaluados = $this->Subperiodo->find('first', 
				array('conditions' => array(' getdate() BETWEEN Subperiodo.mesevaldesde AND DATEADD(m, 3, Subperiodo.mesevalhasta)')
					, 'order' => array('Subperiodo.mesdesde'=> 'DESC')) );
		endif;
		$perId=$periodoEvaluados['Periodo']['id'];
		$perNombre=$periodoEvaluados['Periodo']['etiqueta'];
		$subPerId=$periodoEvaluados['Subperiodo']['id'];
		$subPerNombre=$periodoEvaluados['Subperiodo']['etiqueta'];
		
		$traePersona = $this->Persona->find('first', array('conditions' => array('Persona.ID_PER' => $funcionario_id ) ) );
		// echo 'traePersona<pre>'.print_r($traePersona, 1).'</pre><hr>';
		
		$this->JuntaEvaluadore->bindModel(array('hasOne' =>  array('Persona' => array('className' => 'Persona', 'foreignKey' => 'ID_PER'))) );
		$this->JuntaEvaluadore->primaryKey = 'funcionario_id';
		$listamiembrosJuntaTmp = $this->JuntaEvaluadore->find('all', array('order' => array('JuntaEvaluadore.tipo_id' => 'DESC') ) );
		
		// echo 'miembrosJunta<pre>'.print_r($this->miembrosJunta, 1).'</pre><hr>';
		$losMiembrosJunta = array();
		foreach($listamiembrosJuntaTmp as $lista){
			if( array_key_exists($lista['JuntaEvaluadore']['tipo_id'], $this->miembrosJunta) ){
				$losMiembrosJunta[$this->miembrosJunta[$lista['JuntaEvaluadore']['tipo_id']]][$lista['Persona']['ID_PER']] = $lista['Persona']['NOMBRES']
																															.' '.$lista['Persona']['AP_PAT']
																															.' '.$lista['Persona']['AP_MAT'];
			}
		}
		
		$miembrosFirma = $this->FirmasHojacalifica->find('first', array('conditions' => array('FirmasHojacalifica.funcionario_id' => $funcionario_id)) );
		//echo 'miembrosFirma<pre>'.print_r($miembrosFirma, 1).'</pre><hr>';
		
		$this->set(compact('perNombre', 'subPerNombre', 'traePersona', 'listamiembrosJuntaTmp', 'losMiembrosJunta', 'miembrosFirma' ));
	}
	
	public function setAsignarMiembrosFuncionario(){
	 if( !empty($this->data) ){	

		$this->autoRender = false;
		ksort($this->data['Persona']);
		$var1 = 'data empty <pre>'.(empty($this->data)).'</pre><hr>';
		$var2 = 'data<pre>'.print_r($this->data, 1).'</pre><hr>';
		
		$funcionario_id = $this->data['Persona']['funcionario_id'];
		$firmasHojacalifica['FirmasHojacalifica'] = $this->data['Persona'];
		
		$var2 = 'data<pre>'.print_r($firmasHojacalifica, 1).'</pre><hr>';

		$this->FirmasHojacalifica->set($firmasHojacalifica);
		$this->Session->setFlash(implode('<br>', $this->FirmasHojacalifica->invalidFields()) );
		//$this->Session->setFlash(print_r($this->FirmasHojacalifica->invalidFields(), 1) );
		if ($this->FirmasHojacalifica->validates()) {
			if( $this->FirmasHojacalifica->saveAll($firmasHojacalifica) ){
				// $this->Session->setFlash('grabar: '.$var1.' - '.$var2);
				$this->Session->setFlash('Guardado...');
			}
		}else{
			if( implode('<br>', $this->FirmasHojacalifica->invalidFields()) == 'ya existe funcionario' ){
				// $this->Session->setFlash('Update...'.$var2);
				$this->Session->setFlash(implode('<br>', $this->FirmasHojacalifica->invalidFields()) );
				$arrayUpdate = array('FirmasHojacalifica.slc_integrante1' => $firmasHojacalifica['FirmasHojacalifica']['slc_integrante1']
									,'FirmasHojacalifica.slc_integrante2' => $firmasHojacalifica['FirmasHojacalifica']['slc_integrante2']
									,'FirmasHojacalifica.slc_integrante3' => $firmasHojacalifica['FirmasHojacalifica']['slc_integrante3']
									,'FirmasHojacalifica.slc_integrante4' => $firmasHojacalifica['FirmasHojacalifica']['slc_integrante4']
									,'FirmasHojacalifica.slc_presi' => $firmasHojacalifica['FirmasHojacalifica']['slc_presi']
									,'FirmasHojacalifica.slc_representante' => $firmasHojacalifica['FirmasHojacalifica']['slc_representante']
									,'FirmasHojacalifica.slc_secretario' => $firmasHojacalifica['FirmasHojacalifica']['slc_secretario'] 
									,'FirmasHojacalifica.slc_asociacion' => $firmasHojacalifica['FirmasHojacalifica']['slc_asociacion'] );
				if( $this->FirmasHojacalifica->updateAll($arrayUpdate, array('FirmasHojacalifica.funcionario_id' => $funcionario_id) ) ){
					$this->Session->setFlash('Actualizado...');
				}
			}
		}
		$this->redirect('asignaMiembroFuncionario/'.$funcionario_id );
	 }
	}
	
	public function funcprecal_OLD(){
		//echo '<br />this->viewVars:<pre>'.print_r($this->viewVars, 1).'</pre>';
		$datosSession = $this->viewVars;
		$idPer = $datosSession['idPer'];
		 if($idPer == 444)$idPer = 620; //166; //166; // 391; // 34; //  575; // 38; // 47; //  
		//$this->render(false);
		$Funcionespropias = new Funcionespropias();
		$options = array( 'conditions' => array('funcionario_id' => $idPer)
							,'order '=>array('periodo_id', 'subperiodo_id', 'factore_id')
						 );
		//$lista = $this->Factor->find('all');
		$this->Evaluafuncionario->unbindModel(array('belongsTo' => array('Factor'))	);
		$this->Evaluafuncionario->recursive=1;
		$listaPrecalificaciones = $this->Evaluafuncionario->find('all', $options);

		//$laListaDos = $Funcionespropias->PrecalificacionesArrayUnico($listaPrecalificaciones);
		$laListaDos = $this->Persona->PrecalFuncArrayUnico($listaPrecalificaciones);
		
		// echo '<pre>laListaDos: '.print_r($laListaDos, 1).'</pre>';
		$iDPreevaluadores = $Funcionespropias->arrayIn($listaPrecalificaciones, 'Evaluafuncionario', 'precalificadore_id');		
		$iDPreevaluadores = array_unique($iDPreevaluadores);
		// echo '<pre>iDPreevaluadores: '.print_r($iDPreevaluadores, 1).'</pre>';
		 
		$idPeriodos = $Funcionespropias->arrayIn($listaPrecalificaciones, 'Subperiodo', 'periodo_id');		
		$idPeriodos = array_unique($idPeriodos);
		// echo '<pre>idPeriodos: '.print_r($idPeriodos, 1).'</pre>';
		$losPeriodos = $this->Periodo->find('all', array( 'conditions' => array('id' => $idPeriodos)) );
		// echo '<pre>losPeriodos: '.print_r($losPeriodos, 1).'</pre>';
		$losPeriodos = $Funcionespropias->arrayInPuntero($losPeriodos, 'id', 'Periodo', 'etiqueta');
		// echo '<pre>losPeriodos: '.print_r($losPeriodos, 1).'</pre>';
		$this->Persona->recursive=-1;
		$losPreevaluadores = $this->Persona->find('all', array( 'conditions' => array('id_per' => $iDPreevaluadores)) );
		$losPreevaluadores = $Funcionespropias->arrayInPunteroNombrePersona($losPreevaluadores);
		// echo '<pre>laListaDos: '.print_r($laListaDos, 1).'</pre>';
		$this->set(compact('listaPrecalificaciones', 'losPreevaluadores', 'losPeriodos', 'laListaDos'));
	}
	

	public function funcprecal(){
		//echo '<br />this->viewVars:<pre>'.print_r($this->viewVars, 1).'</pre>';
		$datosSession = $this->viewVars;
		$idPer = $datosSession['idPer'];
		 if($idPer == 444)$idPer = 620; //6; //34; //166; // 391; //  166; // 575; // 38; // 47; //  
		//$this->render(false);
		$Funcionespropias = new Funcionespropias();
		$options = array( 'conditions' => array('funcionario_id' => $idPer)
							,'order '=>array('periodo_id', 'subperiodo_id', 'factore_id')
						 );
		//$lista = $this->Factor->find('all');
		$this->Evaluafuncionario->unbindModel(array('belongsTo' => array('Factor'))	);
		$this->Evaluafuncionario->recursive=1;
		$listaPrecalificaciones = $this->Evaluafuncionario->find('all', $options);
		// echo '<pre>listaPrecalificaciones: '.print_r($listaPrecalificaciones, 1).'</pre>';
		
		$laLista = array();
		foreach($listaPrecalificaciones as $pnt => $lista){
			$indiceArray = $lista['Subperiodo']['periodo_id'].$lista['Subperiodo']['etiqueta'].$lista['Evaluafuncionario']['precalificadore_id'];
			//echo $indiceArray.'<br />';
			$laLista[$indiceArray] = array( $lista['Subperiodo']['periodo_id'], $lista['Subperiodo']['etiqueta'], $lista['Evaluafuncionario']['precalificadore_id'] );
			// $laLista[$lista['Subperiodo']['periodo_id'].$lista['Subperiodo']['etiqueta'].$lista['Evaluafuncionario']['precalificadore_id']] = array( $lista['Subperiodo']['periodo_id'], $lista['Subperiodo']['etiqueta'], $lista['Evaluafuncionario']['precalificadore_id'] );
		}
		//$laListaDos = $Funcionespropias->PrecalificacionesArrayUnico($listaPrecalificaciones);
		$laListaDos = $this->Persona->PrecalFuncArrayUnico($listaPrecalificaciones);
		
		// echo '<pre>laListaDos: '.print_r($laListaDos, 1).'</pre>';
		$iDPreevaluadores = $Funcionespropias->arrayIn($listaPrecalificaciones, 'Evaluafuncionario', 'precalificadore_id');		
		$iDPreevaluadores = array_unique($iDPreevaluadores);
		// echo '<pre>iDPreevaluadores: '.print_r($iDPreevaluadores, 1).'</pre>';
		 
		$idPeriodos = $Funcionespropias->arrayIn($listaPrecalificaciones, 'Subperiodo', 'periodo_id');		
		$idPeriodos = array_unique($idPeriodos);
		// echo '<pre>idPeriodos: '.print_r($idPeriodos, 1).'</pre>';
		$losPeriodos = $this->Periodo->find('all', array( 'conditions' => array('id' => $idPeriodos)) );
		// echo '<pre>losPeriodos: '.print_r($losPeriodos, 1).'</pre>';
		$losPeriodos = $Funcionespropias->arrayInPuntero($losPeriodos, 'id', 'Periodo', 'etiqueta');
		// echo '<pre>losPeriodos: '.print_r($losPeriodos, 1).'</pre>';
		$this->Persona->recursive=-1;
		$losPreevaluadores = $this->Persona->find('all', array( 'conditions' => array('id_per' => $iDPreevaluadores)) );
		$losPreevaluadores = $Funcionespropias->arrayInPunteroNombrePersona($losPreevaluadores);
		//echo '<pre>losPreevaluadores: '.print_r($losPreevaluadores, 1).'</pre>';
		// echo '<pre>laListaDos: '.print_r($laListaDos, 1).'</pre>';
		$this->set(compact('idPer', 'listaPrecalificaciones', 'losPreevaluadores', 'losPeriodos', 'laLista', 'laListaDos'));
	}	
	
	public function funcMicalificacion(){
		// echo '<br />Vars:<pre>'.print_r($this, 1).'</pre>';
		//echo '<br />this->viewVars:<pre>'.print_r($this->viewVars, 1).'</pre>';
		//echo '<br />this->data:<pre>'.print_r($this->data, 1).'</pre>';
		$idPeriodo = 0;
		$funcId = 0;
		$calificacionFuncionario = array();
		$notaCalificaciones = array();
		$factores = array();
		$arrayCntSub = array();
		if( isset($this->data['Personas']['per_id']) && isset($this->data['Personas']['func_id']) ){
			$idPeriodo = $this->data['Personas']['per_id'];
			$funcId = $this->data['Personas']['func_id'];
			//echo '<br />this->data:<pre>'.print_r($idPeriodo.' - - '.$funcId , 1).'</pre>';
			$this->Calificacionfuncionario->recursive=-1;
			$calificacionFuncionario = $this->Calificacionfuncionario->find('all', array( 'conditions' =>
																			array('Calificacionfuncionario.periodo_id = '.$idPeriodo.' '
																					,'Calificacionfuncionario.funcionario_id in ('.$funcId.') '																		
																			)
																	)
															);
			// echo '<br />calificacionFuncionario:<pre>'.print_r($calificacionFuncionario, 1).'</pre>';
			
			foreach($calificacionFuncionario as $lista){
				$notaCalificaciones[$lista['Calificacionfuncionario']['subfactore_id']] = $lista['Calificacionfuncionario']['nota'];
			}
			
			$subfactoresId = array();
			foreach($calificacionFuncionario as $lista){
				$subfactoresId[] = $lista['Calificacionfuncionario']['subfactore_id'];
			}
			//echo '<br />subfactoresId:<pre>'.print_r($subfactoresId, 1).'</pre>';
			
			$options = array();
			// if( $idEscalafon > 1 )$options = array('conditions' => array('Factor.id > 1') );
			$this->Factor->recursive=1;
			$factores = $this->Factor->find('all', $options);
			//echo '<br />factores:<pre>'.print_r($factores, 1).'</pre>'; 
			
			$historiaFunc = $this->Historia->traeFuncionariosHistoria($funcId);
			//echo '<br />historiaFunc:<pre>'.print_r($historiaFunc, 1).'</pre>'; 
			
			$nombreEscalafon = $this->arrayEscalfones[$historiaFunc[0][0]['COD_CARGO']];
			//echo '<br />nombreEscalafon:<pre>'.print_r($nombreEscalafon, 1).'</pre>'; 
			
			
			$arrayCntSub= array();
			foreach($factores as $listaFactores){
				$cntSub=0;
				foreach($listaFactores['Subfactor'] as $listaSubfactores)$cntSub++;
				$arrayCntSub[$listaFactores['Factor']['id']]=$cntSub;
			}
			
			$cabezaFactores = $this->Persona->armarfactores($factores, $subfactoresId);
			// echo '<br />cabezaFactores:<pre>'.print_r($cabezaFactores, 1).'</pre>';
			
		}else{
			$this->redirect( array('controller' => 'Personas', 'action'=>'funcprecal') );
		}
		$this->set(compact('calificacionFuncionario', 'notaCalificaciones', 'factores', 'arrayCntSub', 'cabezaFactores', 'nombreEscalafon'));
	}	
	
	
	
	
	
	
	
	
	
}
