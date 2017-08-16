<?=$session->flash('auth'); 
//if($_SERVER['REMOTE_ADDR'] == '192.168.200.52'){
if(1){
	echo $form->create('User', array('action'=>'login') );
	//echo $form->create('User', array('action'=>'verldap') );
	echo $form->input('username');
	echo $form->input('password').'(Ingrese la contraseña que utiliza para acceder a la red)';
	echo $form->end('Login');
}else{
	echo "<h2>En mantención</h2>";
}
?>
<? if(0): ?><nav class="flotaDerecha" ><?=$this->Html->link('Volver.', array('action' => '../') );?></nav><? endif; ?>
