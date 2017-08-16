<?php
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php __('EVALUACIÓN DE DESEMPEÑO '.date('Y').' - '); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('cake.generic');
		
		echo $this->Html->css('jquery-ui');
        echo $this->Html->css('style');
        echo $this->Html->script('jquery-ui', array('inline' => false));
        echo $this->Html->script('jquery-1.10.2');
		echo $this->Html->script('jquery-latest.min');
		echo $this->Html->script('mod01');

		echo $scripts_for_layout;
	?>
</head>
<body>
<? //=print_r($elIdDelFuncionario,1); ?>

	<? if( $_SERVER['SERVER_NAME'] == '192.168.200.113'){ ?>
		<label style="background-color:red; text-align:center; font-size:18px; ">DESARROLLO</label>
    <? } ?>
    <? //="muestraLink: ".$muestraLink?>
	<div id="container">
		<div id="header">
	        <h1><?=__('.:: EVALUACIÓN DE DESEMPEÑO - PRECALIFICACIÓN DE FUNCIONARIOS ::.');?></h1><?=date('d/m/Y')?>
            <?=' En: '.$ubicacionEn?>
		</div>
        
        <? 
		//echo '<pre>'.print_r($miembrosJunta,1).'</pre>';
	   	//if( count($listaMenuUsr0)>0 && count($this->Session->read('Nombredeusuario')) >= 0):
	   	if( count($current_user)>0 ):
	   	 echo 'Usr: '.$current_user['User']['username'].'<br /><br />';
	   ?>
       <a name="arriba" id="a"></a>
       <div id="menu">
        <ul class="mi-menu_">
          <? //if($user['usersperfil_id'] == 1 || $user['usersperfil_id'] == 3): ?>
          <? if( is_array($listaMenuUsr0) && in_array(1, $listaMenuUsr0) ): ?>
          <li class="nivel1"><a class="nivel1"> Mantenedores Anuales</a>
              <ul class="nivel2">
                  <li><?=$this->Html->link('Periodos', array('controller' => 'Periodos', 'action'=>'index') );?></li>
                  <li><?=$this->Html->link('Informes de desempeño', array('controller' => 'Evaluafuncionarios', 'action'=>'ListaEvaluafuncionarioTodos') );?></li>
                  <li><?=$this->Html->link('Funcionarios pendientes de precalificación', array('controller' => 'Evaluafuncionarios', 'action'=>'pendientes') );?></li>
                  <li><?=$this->Html->link('Funcionarios sin Precalificador', array('controller' => 'personas', 'action' => 'funcionariosSinprecalificador') );?>
                  <? /* Usersperfils */ ?>
                  <li><?=$this->Html->link('PreCalificadores', array('controller' => 'Precalificadores') );?></li>
                  <li class="nivel2">
						<?=$this->Html->link('Asignar Precalificadores »', 0, array('class'=>'nivel2') );?>
                        <ul class="nivel3">
                            <li>
                                <?=$this->Html->link('Por Precalificalificador', array('controller' => 'personas', 'action' => 'funcionariosAsignados'), array('class'=>'primera') );?>
                            </li>
                            <li>
                                <?=$this->Html->link('Por Subperiodo', array('controller' => 'personas', 'action' => 'seleccionaEvaluados') );?>
                            </li>
                        </ul>
                  </li>
                  <li><?=$this->Html->link('Calificadores', array('controller' => 'Calificadores') );?></li>
                  <li><?=$this->Html->link('Anotaciones de Merito', array('controller' => 'Anotaciones', 'action'=>'anotaMeritoTodos') );?></li>
                  <li><?=$this->Html->link('Anotaciones de Demerito', array('controller' => 'Anotaciones', 'action'=>'anotademeritoindex') );?></li>
                  <li><?=$this->Html->link('Miembros Junta Calificadora', array('controller' => 'Personas', 'action'=>'miembrosJuntacalificadora') );?> </li>
                  <li><?=$this->Html->link('Usuarios', array('controller' => 'Usuarios', 'action'=>'index') );?> </li>       
              </ul>
          </li>
          <? endif;?>
          <? if( is_array($listaMenuUsr0) && in_array(1, $listaMenuUsr0)): ?>
          <li class="nivel1"><a class="nivel1"> Mantenedores Permanentes</a>
	        <ul class="nivel2">
                <li><?=$this->Html->link('Lista Factor/Pregunta', array('controller' => 'Factores') );?></li>
                  <li class="nivel2">
				  	<?=$this->Html->link('Subfactores »', array('controller' => 'Subfactores'), array('class'=>'nivel2') );?>
                    <ul class="nivel3">
                    	<li>
	                        <?=$this->Html->link('Items', array('controller' => 'Items'), array('class'=>'primera') );?>
                        </li>
                    	<li>
	                        <?=$this->Html->link('Preguntas', array('controller' => 'Preguntas') );?>
                        </li>
                    </ul>
            	</li>
            </ul>
          </li>
         <? endif;?>
         <? if( is_array($listaMenuUsr0) && in_array(2, $listaMenuUsr0)): ?>
          <li class="nivel1">
            <a  class="nivel1"> Precalificación </a>
            <ul class="nivel2">
              <li><?=$this->Html->link('Precalificar funcionarios', array('controller' => 'Evaluafuncionarios', 'action'=>'ListaEvaluafuncionario') );?></li>
              <li><?=$this->Html->link('Anotaciones de Merito', array('controller' => 'Anotaciones', 'action'=>'index') );?></li>
            </ul>
          </li>
          <? endif;?>
          <? if( is_array($listaMenuUsr0) && in_array(3, $listaMenuUsr0)): ?>
			  <li class="nivel1"><a  class="nivel1"> Secretario<br />Junta </a>
                  <ul class="nivel2">
                    <li>
						<? //=$this->Html->link('Directivo', array('controller' => 'Evaluafuncionarios', 'action'=>'calificacion', 1), array('class'=>'primera') );?>
                        <?=$this->Html->link('Asignar Miembros a Funcionarios', array('controller' => 'Personas', 'action' => 'listaMiembroFuncionario'), array('class'=>'primera') );?>
                    </li>
                    <? //=$this->Html->link('Hoja de Calificación', array('controller' => 'Evaluafuncionarios', 'action'=>'hojaDeCalificacion', $IdFunc ) );?>

                  </ul>
              </li>
          <? endif;?>
          <? if( is_array($listaMenuUsr0) && in_array(3, $listaMenuUsr0)): ?>
			  <li class="nivel1"><a  class="nivel1"> Calificacion </a>
                  <ul class="nivel2">
                    <li><?=$this->Html->link('Directivo', array('controller' => 'Evaluafuncionarios', 'action'=>'calificacion', 1), array('class'=>'primera') );?></li>
                    <li><?=$this->Html->link('Profesional', array('controller' => 'Evaluafuncionarios', 'action'=>'calificacion', 3) );?></li> 
                    <li><?=$this->Html->link('Técnico', array('controller' => 'Evaluafuncionarios', 'action'=>'calificacion', 4) );?></li>
                    <li><?=$this->Html->link('Administrativo', array('controller' => 'Evaluafuncionarios', 'action'=>'calificacion', 5) );?></li>
                    <li><?=$this->Html->link('Auxiliar', array('controller' => 'Evaluafuncionarios', 'action'=>'calificacion', 6) );?></li>
                  </ul>
              </li>
          <? endif;?>
          <? if( is_array($listaMenuUsr0) && in_array(4, $listaMenuUsr0)): ?>
			  <li class="nivel1"><a  class="nivel1"> Funcionario </a>
                  <ul class="nivel2">
                    <li>
						<? $IdFunc = $elIdDelFuncionario; ?>
                        <?=$this->Html->link('Informes de desempeño', array('controller' => 'Evaluafuncionarios'
                                                                            , 'action'=>'FactorfuncionarioTodos'
                                                                                        , 'funcionario_id:'.$IdFunc
                                                                                        , 'elPeriodo:'.$subperiodosDelPeriodo)
                                                                        , array('class'=>'primera') );
						?>
					</li>
                    <? if($muestraLink): ?>
	                    <li><?=$this->Html->link('Precalificación', array('controller' => 'Evaluafuncionarios', 'action'=>'precalificacion'), array('class'=>'primera') );?></li>
                    <? endif; ?>
                    <? if(0): ?> <li><?=$this->Html->link('Observación a Precalificación', array('controller' => 'Obserbaprecalificas', 'action'=>'index') );?></li>  <? endif; ?>
                    <li><?=$this->Html->link( ( $muestraLink == 1 ? "Validar y Aceptar Precalificación" : "Validar y Aceptar Informe de desempeño"), array('controller' => 'Validaaceptaevaluacs') );?></li>
                    <li><?=$this->Html->link('Hoja de Vida', array('controller' => 'Personas', 'action'=>'hojaDeVida') );?></li>
                    <li><?=$this->Html->link('Anotaciones', array('controller' => 'Anotaciones', 'action'=>'misAnotaciones') );?></li> 
                    <li><?=$this->Html->link('Mis precalificadores', array('controller' => 'Personas', 'action'=>'funcprecal') );?></li> 
                  </ul>
              </li>
          <? endif;?>
          <li class="nivel1">
			  <? /*if(count($user) > 0)*/echo $this->Html->link('Cerrar Sesion', array('controller'=>'users', 'action' => 'logout') ); ?>
          </li>
        </ul>
       
       </div>
       <? endif; /**** if(count) ***/ ?>
        
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $content_for_layout; ?>
		</div>
		<div id="footer"><?= __('...GoreCoquimbo '.date('Y'));?></div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>