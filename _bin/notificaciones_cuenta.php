<?php
//Generador de formularios
include("../conf/config.php");

$idu=$_SESSION['idu'];

if($_SESSION['tipou']==2) //Usuario
{	
if($_SESSION['perfil']==0)
$sql="SELECT COUNT(n.id) total FROM notificaciones as n LEFT JOIN notificaciones_usuario as u ON u.id_notificacion = n.id AND u.id_usuario=$idu WHERE (n.id_usuario<>$idu OR tipou=1) AND u.id_notificacion IS NULL";
else	
$sql="SELECT COUNT(n.id) total FROM notificaciones as n JOIN brief_usuario as b ON b.id_brief=n.id_brief AND b.id_usuario=$idu LEFT JOIN notificaciones_usuario as u ON u.id_notificacion = n.id AND u.id_usuario=$idu WHERE (n.id_usuario<>$idu OR tipou=1) AND u.id_notificacion IS NULL";	

}
else //Cliente
$sql="SELECT COUNT(n.id) total FROM notificaciones as n JOIN brief_cliente as b ON b.id_brief=n.id_brief AND b.id_cliente=$idu  LEFT JOIN notificaciones_cliente as u ON u.id_notificacion = n.id AND u.id_cliente=$idu WHERE (n.id_usuario<>$idu OR tipou=2) AND u.id_notificacion IS NULL";	

echo $DB->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];