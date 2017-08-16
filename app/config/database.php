<?php
/**
 * This is core configuration file.
 *
 * Use it to configure core behaviour ofCake.
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
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * In this file you set up your database connection details.
 *
 * @package       cake
 * @subpackage    cake.config
 */
/**
 * Database configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * driver => The name of a supported driver; valid options are as follows:
 *		mysql 		- MySQL 4 & 5,
 *		mysqli 		- MySQL 4 & 5 Improved Interface (PHP5 only),
 *		sqlite		- SQLite (PHP5 only),
 *		postgres	- PostgreSQL 7 and higher,
 *		mssql		- Microsoft SQL Server 2000 and higher,
 *		db2			- IBM DB2, Cloudscape, and Apache Derby (http://php.net/ibm-db2)
 *		oracle		- Oracle 8 and higher
 *		firebird	- Firebird/Interbase
 *		sybase		- Sybase ASE
 *		adodb-[drivername]	- ADOdb interface wrapper (see below),
 *		odbc		- ODBC DBO driver
 *
 * You can add custom database drivers (or override existing drivers) by adding the
 * appropriate file to app/models/datasources/dbo.  Drivers should be named 'dbo_x.php',
 * where 'x' is the name of the database.
 *
 * persistent => true / false
 * Determines whether or not the database should use a persistent connection
 *
 * connect =>
 * ADOdb set the connect to one of these
 *	(http://phplens.com/adodb/supported.databases.html) and
 *	append it '|p' for persistent connection. (mssql|p for example, or just mssql for not persistent)
 * For all other databases, this setting is deprecated.
 *
 * host =>
 * the host you connect to the database.  To add a socket or port number, use 'port' => #
 *
 * prefix =>
 * Uses the given prefix for all the tables in this database.  This setting can be overridden
 * on a per-table basis with the Model::$tablePrefix property.
 *
 * schema =>
 * For Postgres and DB2, specifies which schema you would like to use the tables in. Postgres defaults to
 * 'public', DB2 defaults to empty.
 *
 * encoding =>
 * For MySQL, MySQLi, Postgres and DB2, specifies the character encoding to use when connecting to the
 * database.  Uses database default.
 *
 */
/// 'host' => 'srv03w2k3sql01.gorecoquimbo.cl',
/// 'host' => 'developers01.gorecoquimbo.cl',
 
class DATABASE_CONFIG {

	var $default = array(
		'driver' => 'mssql',
		'persistent' => false,
		'host' => 'SRV53BDDEV01.gorecoquimbo.cl',
		'port' => '',
		'login' => 'usr_cometida',
		'password' => 'pas_cometida',
		'database' => 'personal',
		'prefix' => '',
		//'encoding' => 'utf8',
	);
	
	var $dbEvaluaFunc = array(
		'driver' => 'mssql',
		'persistent' => true,
		/*** DEVELOP 2015:  'host' => 'developers01.gorecoquimbo.cl', // '192.168.200.122',  ***/
		/*** DEVELOP 2016: ***/
//		'host' => 'SRV53BDDEV01.gorecoquimbo.cl', //'192.168.200.122', 
		'host' => 'srv03w2k3sql01.gorecoquimbo.cl', //'192.168.200.201', 
		'port' => '1433',
		'login' => 'usr_remoto',
		'password' => '4321*-',
		'database' => 'DbEvalFunc',
		'prefix' => '',
		//'encoding' => 'utf8',
	);	
	
	var $Server2016 = array(
		'driver' => 'mssql',
		'persistent' => false,
//		'host' => 'SRV53BDDEV01.gorecoquimbo.cl', //'192.168.200.122', 
		'host' => 'SRV53BDDEV01.gorecoquimbo.cl', //'192.168.200.122', 
		'port' => '',
		'login' => 'usr_remoto',
		'password' => '4321*-',
		'database' => 'DbEvalFunc',
		'prefix' => '',
		//'encoding' => 'utf8',
	);	
	
	var $msSqlPersonas = array(
		'driver' => 'mssql',
		'persistent' => false,
//		'host' => 'SRV53BDDEV01.gorecoquimbo.cl', //'192.168.200.122', 
		'host' => 'srv03w2k3sql01.gorecoquimbo.cl',
		'port' => '1433',
		'login' => 'usr_remoto',
		'password' => '4321*-',
		'database' => 'personal',
		'prefix' => '',
		//'encoding' => 'utf8',
	);
	
	//'host' => 'srv03w2k3sql01.gorecoquimbo.cl',
	//'host' => 'developers01.gorecoquimbo.cl',
	var $msSqlBoletas = array(
		'driver' => 'mssql',
		'persistent' => false,
		'host' => 'srv03w2k3sql01.gorecoquimbo.cl',
		'port' => '',
		'login' => 'usr_remoto',
		'password' => '4321*-',
		'database' => 'dbDocGarantia',
		'prefix' => '',
		//'encoding' => 'utf8',
	);
	var $dbAcuerdos = array(
		'driver' => 'postgres',
		//'datasource' => 'Database/Postgres',
		'persistent' => false,
		'host' => '192.168.200.116', //'localhost',
		'port' => '5432',
		'login' => 'maraya',
		'password' => 'Gore*-Coqbo2014Y',
		'database' => 'acuerdos',
	);
	//'host' => '192.168.33.19',
	var $dbGore = array(
		'driver' => 'mysql',
		'persistent' => false,
		'host' => '192.168.33.19',
		'login' => 'cqbogore',
		'password' => 'bellsouth',
		'database' => 'grlicita',
		'prefix' => '',
		//'encoding' => 'utf8',
	);

	
/*
	var $test = array(
		'driver' => 'mssql',
		'persistent' => false,
		'host' => 'developers01.gorecoquimbo.cl',
		'login' => 'usr_cometida',
		'password' => 'pas_cometida',
		'database' => 'personal',
		'prefix' => '',
		//'encoding' => 'utf8',
	);
*/
	/*var $default = array(
		'driver' => 'mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'user',
		'password' => 'password',
		'database' => 'database_name',
		'prefix' => '',
		//'encoding' => 'utf8',
	);

	var $test = array(
		'driver' => 'mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'user',
		'password' => 'password',
		'database' => 'test_database_name',
		'prefix' => '',
		//'encoding' => 'utf8',
	);*/
}