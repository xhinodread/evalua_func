<? if(0): ?>
users<pre><?=print_r($users, true);?></pre>
listaPerfiles2<pre><?=print_r($listaPerfiles2, true);?></pre>
<? endif; ?>




<div class="users">
<h2><?=__('Usuarios');?></h2>
	<nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
    <ul class="actions flotaDerecha" style="padding-top:50px;" ><?=$this->Html->link(__('Nuevo Usuario', true), array('action' => 'add')); ?></ul>
	<table cellpadding="0" cellspacing="0" >
	<tr>
        <th><?=$this->Paginator->sort('id');?></th>
        <th><?=$this->Paginator->sort('username');?></th>
        <th><?=$this->Paginator->sort('Perfil', 'usersperfil_id');?></th>
        <th class="actions" style="text-align:center;"><?php __('Acciones');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($users as $user):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?=$class;?>>
		<td><?=$user['User']['id']; ?>&nbsp;</td>
		<td><?=$user['User']['username']; ?>&nbsp;</td>
		<td>
        	<? foreach($user['Usersperfils'] as $lstaIdPerf) echo $listaPerfiles2[$lstaIdPerf['userperfil_id']].'<br />'; ?>
			<? //=$user['Usersperfils'].', '.$listaPerfiles2[$user['User']['usersperfil_id']]; ?>
        	&nbsp;
        </td>
		<td class="actions">
			<? //=$this->Html->link(__('Ver', true), array('action' => 'view', $user['User']['id'])); ?>
			<?=$this->Html->link(__('Editar', true), array('action' => 'edit', $user['User']['id'])); ?>
			<?=$this->Html->link(__('Borrar', true), array('action' => 'delete', $user['User']['id']), null, sprintf(__('Seguro de Borrar definitivamente el registro #%s? \n'.$user['User']['username'], true), $user['User']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
    
    <nav class="flotaDerecha_" >
    <!-- Muestra los números de página -->
	<?='Hojas: '.$paginator->numbers(); ?>
    <!-- Muestra los enlaces para Anterior y Siguiente -->
    </nav>
    <nav class="flotaDerecha" >
    <?php
        echo $paginator->prev('« Previo ', null, null, array('class' => 'disabled'));
        echo $paginator->next(' Siguiente »', null, null, array('class' => 'disabled'));
    ?>
    <!-- Muestra X de Y, donde X es la página actual e Y el total del páginas -->
    <?php echo $paginator->counter(); ?>
    </nav>
    <br />
    <nav ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
</div>













<? if(0): ?>
	<h2><?php __('Users');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('username');?></th>
			<th><?php echo $this->Paginator->sort('password');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($users as $user):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $user['User']['id']; ?>&nbsp;</td>
		<td><?php //echo $user['User']['name']; ?>&nbsp;</td>
		<td><?php echo $user['User']['username']; ?>&nbsp;</td>
		<td><?php echo $user['User']['password']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $user['User']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $user['User']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $user['User']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $user['User']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New User', true), array('action' => 'add')); ?></li>
		<li><?php //echo $this->Html->link(__('List Books', true), array('controller' => 'books', 'action' => 'index')); ?> </li>
		<li><?php //echo $this->Html->link(__('New Book', true), array('controller' => 'books', 'action' => 'add')); ?> </li>
	</ul>
    <nav class="flotaDerecha" ><?=$this->Html->link('Volver', array('action' => '../') );?></nav>
<? endif; ?>
