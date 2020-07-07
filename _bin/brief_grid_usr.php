<?php
include("../conf/config.php");
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'brief';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	
	array(
        'db' => 'id',
        'dt' => 'DT_RowId',
        'formatter' => function( $d, $row ) {
            // Technically a DOM id cannot start with an integer, so we prefix
            // a string. This can also be useful if you have multiple tables
            // to ensure that the id is unique with a different prefix
            return 'r_'.$d;
        }
    ),
	array( 'db' => 'id', 'dt' => 0),
	array( 'db' => 'id_usuario', 'dt' => null),
	array( 'db' => 'titulo',  'dt' => 1 ),
	array( 'db' => 'fecha_revision', 'dt' => 2,
        'formatter' => function( $d, $row ) {
	
            return date("d-m-Y",strtotime($d));
        }),
	array( 'db' => 'fecha_entrega', 'dt' => 3,
        'formatter' => function( $d, $row ) {
	/* $hoy=date("U");
	$fecha=strtotime($d);
	$fecha_1=strtotime($d . " -1 day"); */
	
	$hoy=date("Y-m-d");
	$fecha=date("Y-m-d",strtotime($d));
	$fecha_1=date("Y-m-d",strtotime($d . " -1 day"));
	
	$estado="";
	
	if($hoy>$fecha)
		$estado=' <b class="badge bg-danger">Vencido</b>';
	elseif($hoy>$fecha_1)
		$estado=' <b class="badge bg-warning">Por vencer</b>';
	
	if($row['estado']==3) $estado="";
	
            return date("d-m-Y",strtotime($d)) . $estado;
        }),
	array( 'db' => 'estado', 'dt' => 4 ,
        'formatter' => function( $d, $row ) {
	global $estadosbrief,$estadosbriefcolor;
	
	$dueño="";
	
	if($row['id_usuario']==$_SESSION['idu'])
		$dueño=' <i class="fa fa-star text-success"></i>';
            return $estadosbriefcolor[$d] . utf8_decode($estadosbrief[$d]) . '</b>' . $dueño;
        }   )
	/*array( 'db' => 'first_name', 'dt' => 0 ),
	array( 'db' => 'last_name',  'dt' => 1 ),
	array( 'db' => 'position',   'dt' => 2 ),
	array( 'db' => 'office',     'dt' => 3 ),
	array(
		'db'        => 'start_date',
		'dt'        => 4,
		'formatter' => function( $d, $row ) {
			return date( 'jS M y', strtotime($d));
		}
	),
	array(
		'db'        => 'salary',
		'dt'        => 5,
		'formatter' => function( $d, $row ) {
			return '$'.number_format($d);
		}
	)*/
);

// SQL server connection information
$sql_details = array(
	'user' => $user,
	'pass' => $pwd,
	'db'   => $db,
	'host' => $server
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */



require( '../lib/datatables/ssp.class.php' );


//$whereAllClause="estado=".$_SESSION['filtrobrief'];
//Solución para los filtros de vencido y por vencer.
	
	$filtrobrief=$_SESSION['filtrobrief'];
	if($filtrobrief==4) //por vencer
	{$estado="estado<>3 AND  DATE(fecha_entrega) <= '" . date("Y-m-d",strtotime("+1 day")) . "' AND DATE(fecha_entrega) >= '" . date("Y-m-d") ."'";}
	elseif($filtrobrief==5) //vencido
	{$estado="estado<>3 AND  DATE(fecha_entrega) < '" . date("Y-m-d") ."'";}
	else //todos los demás
	{$estado=" estado=$filtrobrief";}

if($_SESSION['perfil']==0){ //Administrador ve todos los briefs
	
	//$whereAllClause="estado=".$_SESSION['filtrobrief'];
	$whereAllClause=$estado;
	//echo "SQL: " . $estado;

if($_SESSION['filtrobrief']=="")
$datos=SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns );
else
$datos=SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAllClause );
	
}else{ //Usuario ve todos los que tiene asignados
	
	$tipou=$_SESSION['tipou'];

if($_SESSION['filtrobrief']=="") {
	
	if ($tipou==1) //Cliente
	$whereAllClause="(id=(SELECT id_brief FROM brief_cliente WHERE brief.id=brief_cliente.id_brief AND brief_cliente.id_cliente=".$_SESSION['idu'].") OR id=(SELECT id_brief FROM brief_revisor WHERE brief.id=brief_revisor.id_brief AND brief_revisor.id_usuario=".$_SESSION['idu']." AND brief_revisor.tipo=1))";	
	
	if ($tipou==2) //Usuario
	$whereAllClause="(id=(SELECT id_brief FROM brief_usuario WHERE brief.id=brief_usuario.id_brief AND brief_usuario.id_usuario=".$_SESSION['idu'].") OR id=(SELECT id_brief FROM brief_revisor WHERE brief.id=brief_revisor.id_brief AND brief_revisor.id_usuario=".$_SESSION['idu']." AND brief_revisor.tipo=2))";	
	
$datos=SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAllClause );
}else{
	
	
	
	
	$whereAllClause="id=(SELECT id_brief FROM brief_usuario WHERE brief.id=brief_usuario.id_brief AND brief_usuario.id_usuario=1) AND $estado";
	
		if ($tipou==1) //Cliente
	$whereAllClause="(id=(SELECT id_brief FROM brief_cliente WHERE brief.id=brief_cliente.id_brief AND brief_cliente.id_cliente=".$_SESSION['idu'].") OR id=(SELECT id_brief FROM brief_revisor WHERE brief.id=brief_revisor.id_brief AND brief_revisor.id_usuario=".$_SESSION['idu']." AND brief_revisor.tipo=1)) AND $estado";	
	
	if ($tipou==2) //Usuario
	$whereAllClause="(id=(SELECT id_brief FROM brief_usuario WHERE brief.id=brief_usuario.id_brief AND brief_usuario.id_usuario=".$_SESSION['idu'].") OR id=(SELECT id_brief FROM brief_revisor WHERE brief.id=brief_revisor.id_brief AND brief_revisor.id_usuario=".$_SESSION['idu']." AND brief_revisor.tipo=2)) AND $estado";
	
$datos=SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAllClause );
}
}

//print_r($datos);
$datos=utf8ize($datos);
echo json_encode($datos);

//print_r(SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns ));

//print_r($_GET);


