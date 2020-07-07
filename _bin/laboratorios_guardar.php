<?php
//Guardador de formularios
include("../conf/config.php");

$id=$_POST['id'];
$razon=$_POST['razon'];
$rut=$_POST['rut'];
$direccion=$_POST['direccion'];
$ciudad=$_POST['ciudad'];
$fono=$_POST['fono'];
$email=$_POST['email'];

//Guardar


try { //control de error
$sql="UPDATE laboratorios SET razon = ?, rut = ?, direccion = ?, ciudad = ?, fono = ?, email = ? WHERE id=?";
$stmt = $DB->prepare($sql);
$stmt->execute(array($razon, $rut,$direccion,$ciudad,$fono,$email,$id));
echo 'ok';	
	
} catch(PDOException $ex) { //manejo del potencial error
echo 'error: ' . $ex->getMessage();
}


?>