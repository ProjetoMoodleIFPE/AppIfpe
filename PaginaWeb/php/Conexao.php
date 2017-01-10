<?php


	try{
	$conexao = new PDO('mysql:host=localhost;dbname=aplicativomobile','root','');
	}catch(PDOException $e){
	echo 'ERROR: ' . $e->getMessage();
 

} 


?>