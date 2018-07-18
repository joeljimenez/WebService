<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class UsuarioJuego extends REST_Controller {

   public function __construct(){
	header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE");
	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
	header("Access-Control-Allow-Origin: *");
	   parent:: __construct();

	   $this->load->database();
   }

   public function registrar_Usuario_POST(){//Insertar Usuario
	   $data = $this->post();
	   $useri = null;
	   if(!isset($data['Nombre']) OR !isset($data['Apellido']) OR
	      !isset($data['Cedula']) OR !isset($data['claveSeguridad'])OR 
	      !isset($data['UsuarioS']) OR !isset($data['Contra'] )OR 
	      !isset($data['Correo_Elec']) OR !isset($data['Carrera_Uni'])OR
	      !isset($data['Annio']) OR !isset($data['Edad'] )){
         $respuesta = array('error' =>TRUE , 
				   'Mensaje'=> 'Los Datos estan Vacios');
				   $this->response($respuesta);
	   }else{
		   /*si todo esta bien, busca si el usuario ingresado ya existe */
		$query = $this->db->query("SELECT Usuario FROM `usuario` WHERE Usuario ='".$data['UsuarioS']."'");
		foreach ($query->result_array() as $row)
		  {
			   $useri = $row['Usuario'];
		  }

		  if($useri){
			$respuesta= array( 'error'=>TRUE,
			'Mensaje'=>'El Usuario Ya Existe');
			$this->response($respuesta);
			return;
		  }else{
		   $insertar = array('Usuario' =>$data['UsuarioS'] , 
		                     'contra' => $data['Contra'],
							 'Clave' => $data['claveSeguridad']);

				 $this->db->insert('usuario',$insertar);
							 $respuesta = array('error' =>TRUE ,
							                    'Linea'=>$insertar);
					    
				 $this->response($respuesta);
		  }
			
		  $this->db->reset_query();
			
			   $query = $this->db->query("SELECT Id_User FROM `usuario` WHERE Usuario ='".$data['UsuarioS']."'");
			foreach ($query->result_array() as $row)
              {
				   $id = $row['Id_User'];
			  }
			
			if(!$id){
				$respuesta= array( 'error'=>TRUE,
				'Mensaje'=>'No Existe El Usuario');
				$this->response($respuesta);
			}else{
				 $insertar = array('cedula' => $data['Cedula'] , 
					'nombre' => $data['Nombre'] , 
					'apellido'=>$data['Apellido'] , 
					'carrera'=>$data['Carrera_Uni'] , 
					'edad' =>   $data['Edad'] , 
					'Fk_IdSer'=>$id , );

				  $this->db->insert('info_usuario',$insertar);
	                $respuesta= array( 'error'=>FALSE,
	                                  'Mensaje'=>$id);

	               $Insert = array('Activo' => TRUE, 
			              		 'Id_UsuarioJ'=>$id);
	              	 $this->db->insert('boton',$Insert);
	               $this->response($respuesta);
				  }
			
			}						   
	   
	   }
   

//servicio para hacer el login
public function  Login_Jugador_post(){
	$data=$this->post();
	if( !isset ( $data['Usuario'] ) OR !isset( $data['Contra'] )){//aqui se verifica si viene la data vacia
		$respuesta = array('error'=> true,
							'Mensaje'=> 'Los datos estan vacios' );
							$this->response($respuesta);
	}
	$condiciones = array('Usuario' => $data['Usuario'],//condicion para ser buscdo en el where
                             'contra'=> $data['Contra'] );
         $query = $this->db->query("SELECT * FROM `usuario` WHERE Usuario ='".$data['Usuario']."' and contra = '".$data['Contra']."'");
		 $usuario = $query->row();
             if(!$usuario ){
			    $respuesta = array('Error' => TRUE,
						'Mensaje'=>' Usuario o clave Incorrectos' );
							$this->response($respuesta);
			 }else{
				$respuesta = array('Error' =>FALSE ,
				'Mensaje'=>'Usuario Valido' );
				$this->response($respuesta);


				$this->db->reset_query();

				$tokenh= hash('ripemd160',$data['Contra']);
				
				$id=$usuario->Id_User;

				$Actualizar = array('Token' => $tokenh );
				$this->db->where('Id_User',$id);
				$this->db->update('usuario',$Actualizar);
				
				
								
				$Actuali = array('Activo' => FALSE, 
								 'Id_UsuarioJ'=>$id);


			    $this->db->reset_query();
				$Verficar = $this->db->query("SELECT * FROM `boton` WHERE Id_UsuarioJ ='".$id."'");
				$usuario = $Verficar->row();
				if(!$usuario){
			return;
				}else{
					$this->db->where('Id_UsuarioJ',$id);
					$this->db->update('boton',$Actuali);
				}
			
               

				$Final = array( 'Error'=>FALSE,
				                 'Id_Jugador'=>$id,
								'token'=> $tokenh);
				$this->response($Final);

			 } 

  }

  public function habilitado_post($id="0",$token="0"){

if($id =="0" || $token =="0"){
$respuesta = array('Error' =>TRUE ,
				'Mensaje' =>'Token o Id Son Incorrecto');
				$this->response($respuesta);
}else{
	$query = $this->db->query("SELECT Activo FROM `boton` WHERE Id_UsuarioJ = '".$id."'");
			foreach ($query->result_array() as $row)
			  { $Activo = $row['Activo'];}
			  if($Activo=="0"){
				$respuesta = array('Error' =>TRUE ,
				'Mensaje' =>'Usuario Logueado');
				$this->response($respuesta);
			  }else{
				$respuesta = array('Error' =>FALSE ,
				'Mensaje' =>'Usuario No Logueado');
				$this->response($respuesta);
			  }
}
}

public function cerrarSession_PUT($id="0",$token="0"){
 

if($id =="0" || $token =="0"){
	$respuesta = array('Error' =>TRUE ,
					'Mensaje' =>'Token o Id Son Incorrecto');
					$this->response($respuesta);
					return;
	}else{
 
	$respuesta = array('Error' =>FALSE ,
					'Mensaje' =>'Cerrando Sessión');
 
	$Actualizar = array('Activo'=>1);
	$this->db->where('Id_UsuarioJ ',$id);
				$this->db->update('boton',$Actualizar);
				$this->response($respuesta);

 }

}

public function Buscar_Usuario_get($usuario="0"){

	if($usuario == "0"){
		$respuesta = array('Error' =>FALSE ,
							'Mensaje'=>'Datos Vacios' );
							$this->response($respuesta);
	}
	$query = $this->db->query("SELECT * FROM `usuario` WHERE Usuario ='".$usuario."'");
	$usuarioFila= $query->row();
	if($usuarioFila){
		$respuesta = array('Error' =>true ,
						   'Mensaje' =>'Este Usuario ya esta En Uso' );
		$this->response($respuesta);
	}else {
		$respuesta = array('Error' =>false ,
						   'Mensaje' =>'Usuario Disponible' );
		$this->response($respuesta);
	}
	}

	public function Perfil_Usuario_get($Id){


		if( $Id == "0"){
			$respuesta = array('Error' =>TRUE ,
					  'Mensaje' =>'No Hay Id De Usuario');
					  $this->response($respuesta);
					  return;
		}else{
			$query = $this->db->query("SELECT usuario.Usuario , info_usuario.cedula,info_usuario.nombre,info_usuario.apellido,info_usuario.carrera,info_usuario.edad, detalle_usuario.Puntacion,detalle_usuario.nivel, detalle_usuario.Fecha_Partida,detalle_usuario.Hora_Partida from usuario INNER JOIN info_usuario on usuario.Id_User=info_usuario.Fk_IdSer INNER JOIN detalle_usuario on usuario.Id_User = detalle_usuario.fk_idDe where usuario.Id_User = '".$Id."'");
			$respuesta = array('Error' => FALSE,
								'Linea'=> $query->result_array());
		$this->response($respuesta);
		}


	}


}