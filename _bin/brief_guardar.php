<?php
//Guardador de formularios
include("../conf/config.php");

$d = $_POST; //json_decode(file_get_contents("php://input"), true);

//var_dump($d);
//Guardar o crear

$id=$d['id'];
$clientes=json_decode($d['clientes']);
$usuarios=json_decode($d['usuarios']);
$revisores=json_decode($d['revisores']);
$productos=json_decode($d['productos']);

try { //control de error
	$DB->beginTransaction(); //inicio transacción
if ($id==0) {
$sql="INSERT INTO brief (titulo, estado, campana, fecha_creacion, fecha_revision, fecha_entrega, antecedentes, objetivo, target, objetivo_comunicacional, beneficio, tono, presupuesto, mandamientos, contrato) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
}else{
$sql="UPDATE brief SET titulo=?, estado=?, campana=?, fecha_creacion=?, fecha_revision=?, fecha_entrega=?, antecedentes=?, objetivo=?, target=?, objetivo_comunicacional=?, beneficio=?, tono=?, presupuesto=?, mandamientos=?, contrato=? WHERE id=" . $id;
}
	//echo $sql;
$stmt = $DB->prepare($sql);
$stmt->execute(array($d['titulo'],$d['estado'],$d['campana'],$d['fecha_creacion'],$d['fecha_revision'],$d['fecha_entrega'],$d['antecedentes'],$d['objetivo'],$d['target'],$d['objetivo_comunicacional'],$d['beneficio'],$d['tono'],$d['presupuesto'],$d['mandamientos'],$d['contrato']));
	
	if ($id==0) {
		$id = $DB->lastInsertId();
		
		comentar($_SESSION['idu'],$_SESSION['tipou'],$id,1,"He CREADO el Brief N° $id");
		$noti="<b>".$_SESSION['nusuario']. "</b> ha CREADO el Brief N° $id";
	notificar($noti,1,$id);
	} else {
	
	//limpieza de tablas relacionadas si es que es actualización
	
	$DB->exec("DELETE FROM brief_cliente WHERE id_brief=$id");
	$DB->exec("DELETE FROM brief_productos WHERE id_brief=$id");	
	$DB->exec("DELETE FROM brief_revisor WHERE id_brief=$id");
	$DB->exec("DELETE FROM brief_usuario WHERE id_brief=$id");
	}

	//Cliente y responsable por defecto
	$id_cliente=0;
	$id_usuario=0;
	
	//Guardado de clientes
	$sql="INSERT INTO brief_cliente (id_cliente, id_brief, orden) VALUES ";
	$i=0;
	$t=sizeof($clientes);
	foreach ($clientes as $d) {
		$sql.="($d,$id,$i)";
    $i++;
		if ($i<$t)
			$sql.=",";
		
		if($i==1)
			$id_cliente=$d;
		
}
	if($i>0)
		$DB->exec($sql);

	
	//Guardado de usuarios
		$sql="INSERT INTO brief_usuario (id_usuario, id_brief, orden) VALUES ";
	$i=0;
	$t=sizeof($usuarios);
	foreach ($usuarios as $d) {

		$sql.="($d,$id,$i)";
    $i++;
		if ($i<$t)
			$sql.=",";
		
		if($i==1)
			$id_usuario=$d;
}
	if($i>0)
		$DB->exec($sql);
	
	//Guardado de revisores
			$sql="INSERT INTO brief_revisor (id_usuario, tipo, id_brief, orden) VALUES ";
	$i=0;
	$t=sizeof($revisores);
	foreach ($revisores as $d) {

		$sql.="(". $d->{'id'} .",". $d->{'tipo'} .",".$id.",".$i.")";
    $i++;
		if ($i<$t)
			$sql.=",";
}
	if($i>0)
		$DB->exec($sql);
	
	//Guardado de productos
	$sql="INSERT INTO brief_productos (id_producto, cantidad, id_brief, orden) VALUES ";
	$i=0;
	$t=sizeof($productos);
	foreach ($productos as $d) {

		$sql.="(".$d->{'id'}.",".$d->{'cantidad'}.",".$id.",".$i.")";
    $i++;
		if ($i<$t)
			$sql.=",";
}
	if($i>0)
		$DB->exec($sql);
	
	
	//Guardado de responsables
	$sql="UPDATE brief SET id_cliente=$id_cliente, id_usuario=$id_usuario WHERE id=$id";
	$DB->exec($sql);
	
	comentar($_SESSION['idu'],$_SESSION['tipou'],$id,1,"He ACTUALIZADO el Brief N° $id");
	$noti="<b>".$_SESSION['nusuario']. "</b> ha ACTUALIZADO el Brief N° $id";
	notificar($noti,1,$id);
	
$DB->commit(); //Ejecuto transacción
echo 'ok||'.$id;	
	
} catch(PDOException $ex) { //manejo del potencial error
	$DB->rollBack(); //Revierto transacciones
echo 'error: ' . $ex->getMessage();
}


?>