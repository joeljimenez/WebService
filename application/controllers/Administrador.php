<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Administrador extends REST_Controller {

   public function __construct(){
	header("Access-Control-Allow-Methods:GET,PUT,POST,DELETE");
	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
	header("Access-Control-Allow-Origin: *");
	   parent:: __construct();

	   $this->load->database();
   }
   /*traer todos los usuarios*/
	public function GetUsuarios_get(){
 

		/*$query = $this->db->query("SELECT * FROM `registro` where idUsuario= '".$id."'");*/
		$query = $this->db->query('SELECT * FROM `administrador`');
		$respuesta = array('Error' => FALSE,
		 'Linea'=> $query->result_array());
		 $this->response($respuesta);
	
	}
/* traer solo un usuario*/
	public function get_usuario_get($id){

      $query = $this->db->query("SELECT * FROM `administrador` WHERE idAdministrador ='".$id."'");
		$respuesta = array('Error' =>FALSE ,
							'Linea'=> $query->result_array());
							$this->response($respuesta);
	}
 
	
/*Actualizar un registro*/

public function actualizar_adminstrador_put($id){

	$data=$this->put();
	if(!isset($data['Administrador']) OR !isset($data['Nombre']) OR !isset($data['Materia'])){
		$respuesta = array('Error' =>TRUE ,
							'Mensaje'=>'Los Campos estan Vacio' );
							$this->response($respuesta);
	}

	$query = $this->db->query("SELECT * FROM `administrador` WHERE idAdministrador ='".$id."'");
	$usuario = $query->row();
	if(!$usuario){
		$respuesta = array('Error' =>TRUE ,
		'Mensaje'=>'El Id No existe, No se pudo Actualizar!' );
		$this->response($respuesta);
		return;
	}

	//ya paso por todos los casos y se puede actualizar
	$this->db->reset_query();
	$actualizar = array('Administrador'=> $data['Administrador'],
	                     'Nombre'=> $data['Nombre'],
						 'Materia'=> $data['Materia']);
						 
$this->db->where('idAdministrador',$id);
$this->db->update('administrador',$actualizar);

$respuesta = array('Error' => FALSE,
                   'Mensaje'=>'Registro Actualizado Correctamente!',
                   'idAdministrador',$id,
                   'Administrador' => $data['Administrador'],
                   'Nombre' => $data['Nombre'],
                   'Materia' => $data['Materia'] );
$this->response($respuesta);	
}

public function agregar_administrador_post(){
$data=$this->post();
if(!isset($data['Nombre']) OR !isset($data['Administrador']) 
OR !isset($data['Materia']) OR !isset($data['Contra'])){
$respuesta = array('Error' => TRUE ,
                    'Mensaje'=>'Datos Estas vacios..!'
							  );
							  $this->response($respuesta);
}else{
	$respuesta = array('Error' => FALSE ,
                        'Mensaje'=>'Datos llenos..!'
							  );
							  $this->response($respuesta);


$insertar = array('Administrador' =>$data['Administrador'],
				   'Contra'       =>$data['Contra'],
				   'Nombre'       =>$data['Nombre'],
				   'Materia'      =>$data['Materia'] 
				);
				 $this->db->insert('administrador',$insertar);
				 $respuesta = array('Error' =>FALSE ,
				                    'Linea' =>$insertar );
				 $this->response($respuesta);

			}

}

public function Buscar_Usuario_get($usuario="0"){

if($usuario == "0"){
	$respuesta = array('Error' =>FALSE ,
						'Mensaje'=>'Datos Vacios' );
						$this->response($respuesta);
}
$query = $this->db->query("SELECT * FROM `administrador` WHERE Administrador ='".$usuario."'");
$usuarioFila= $query->row();
if($usuarioFila){
	$respuesta = array('Error' =>true ,
	                   'Mensaje' =>'Este Administrador ya esta Escogido' );
	$this->response($respuesta);
}else {
	$respuesta = array('Error' =>false ,
	                   'Mensaje' =>'Adminstrador Disponible' );
	$this->response($respuesta);
}
}



 
}
