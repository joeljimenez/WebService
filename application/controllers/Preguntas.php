<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Preguntas extends REST_Controller {

   public function __construct(){
	header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE");
	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
	header("Access-Control-Allow-Origin: *");
	   parent:: __construct();

	   $this->load->database();
   }
public function registrar_post(){
    $data=$this->post();
    if(!isset($data['Pregunta'])OR !isset($data['OpcionA']) 
    OR !isset($data['OpcionB']) OR !isset($data['Fecha'])
    OR !isset($data['OpcionC'])
    OR !isset($data['Correcta']) OR !isset($data['Dificultad'])){
      $respuesta = array('Error' =>TRUE ,
                         'Mensaje'=> 'Los Datos Estan Vacios' );
                         $this->response($respuesta);
    }else{
        $Insertar = array('Pregunta' =>$data['Pregunta'],
                          'OpcionA' => $data['OpcionA'],
                          'OpcionB' => $data['OpcionB'],
                          'OpcionC' => $data['OpcionC'],
                          'Correcta' => $data['Correcta'],
                          'Dificultad' => $data['Dificultad'],
                          'Fecha'=> $data['Fecha']
                        );
                        $this->db->insert('pregunta',$Insertar);
                        $respuesta = array('Error' =>FALSE ,
                                            'Mensaje'=>'Fue Registrado Exitosamente',
                                            'Linea'=>$Insertar);
                        $this->response($respuesta);
    }
}

public function Traer_Preguntas_get(){
$query=$this->db->query("SELECT * FROM `pregunta` ORDER by Dificultad ASC");
$respuesta = array( 'Linea'=>$query->result_array() );
                    $this->response($respuesta);
}

public function Traer_Una_Pregunta_get($id){
    $this->db->reset_query();
    $query = $this->db->query("SELECT * FROM `pregunta` WHERE Id_Pregunta = '".$id."'");
    $pregunta=$query->row();
    if(!$pregunta){
        $respuesta= array( 'Error'=>TRUE,
        'Mensaje'=>'No Existe Un Jugar');
        $this->response($respuesta);
    }else{
        $respuesta= array( 'Error'=>FALSE,
        'Mensaje'=>$query->result_array());
        $this->response($respuesta);
    }
 }

 /*Actualizar*/

 public function Actualizar_Pregunta_put($id="0"){
    $usuario = null;
    $data=$this->put();
    if(!isset($data['Pregunta'])OR !isset($data['OpcionA'])
    OR !isset($data['OpcionB']) OR !isset($data['Fecha'])
    OR !isset($data['OpcionC']) 
    OR !isset($data['Correcta']) OR !isset($data['Dificultad'])
    OR !isset($data['FechaAc'])){
      $respuesta = array('Error' =>TRUE ,
                         'Mensaje'=> 'Los Datos Estan Vacios' );
                         $this->response($respuesta);
    }
    if($id =="0"){
        $respuesta = array('Error' =>TRUE , 
                            'Mensaje'=>'No Ingreso ID De Pregunta');
                            $this->response($respuesta);
                            return;
    } 
    $query = $this->db->query("SELECT * FROM pregunta WHERE Id_Pregunta ='".$id."'");

    
    $usuario = $query->row();
    if(!$usuario){
        $respuesta = array('Error' => TRUE,
        'Mensaje'=>'No puede ser Actualizado, No existe Id' );
            $this->response($respuesta);
    }else{
        $this->db->reset_query();
       $Actualizar = array('Pregunta' => $data['Pregunta'],
                            'OpcionA' => $data['OpcionA'],
                            'OpcionB' => $data['OpcionB'],
                            'OpcionC' => $data['OpcionC'],
                            'Correcta' =>$data['Correcta'],
                            'Dificultad' => $data['Dificultad'],
                            'FechaActu'=> $data['FechaAc'] );
                            $this->db->where('Id_Pregunta',$id);
                            $this->db->update('pregunta',$Actualizar);


                           $respuesta = array('Error' => FALSE,
											   'Mensaje'=>'Pregunta Actualizado Correctamente!'
                                               );
                         
    }
    $this->response($respuesta);	
 }

 /*Eliminar Preguntas*/
 public function Eliminar_Pregunta_delete($id="0"){

    if( $id == "0"){
        $respuesta = array('Error' =>TRUE ,
                  'Mensaje' =>'No ingreso Id De La Pregunta');
                  $this->response($respuesta);
                  return;
    }
    	//Verificar si el id ingresado es valido
        $query = $this->db->query("SELECT * FROM pregunta WHERE Id_Pregunta='". $id ."'");
 	

        $existe = $query->row();
      if(!$existe){
       $respuesta = array('Error' =>TRUE ,
       'Mensaje' =>'El ID NO EXISTE');
       $this->response($respuesta);
       return;
      }else{
        $this->db->reset_query();//eliminado las consultas anteriores
        $this->db->delete('pregunta' , array ('Id_Pregunta'=> $id));//metodo delete
        $this->db->reset_query();
         $query = $this->db->query('SELECT * FROM `pregunta`');//metodo get
    
        $respuesta = array('Error' => FALSE,//mandar el mensaje
        'mensaje' => 'Pregunta Eliminado Correcta Mente',
        'Linea'=> $query->result_array());
        $this->response($respuesta);
      }
  

 }

 public function buscar_Pregunta_get($difi="0"){
     

    if( $difi == "0"){
        $query=$this->db->query("SELECT * FROM `pregunta`");
        $respuesta = array( 'Error' =>FALSE ,
                            'Linea'=>$query->result_array() );
                            $this->response($respuesta);
    }else{
        $query = $this->db->query(" SELECT * FROM `pregunta` WHERE Dificultad  ='".$difi."'");
        $respuesta = array('Error' => FALSE,
                            'Linea'=> $query->result_array());
    
         $this->response($respuesta);
    }

    
 }

 public function tablero_verificar_get($Id="0"){
    if( $Id == "0"){
        $respuesta = array('Error' =>TRUE ,
                  'Mensaje' =>'No Hay Id De Usuario');
                  $this->response($respuesta);
                  return;
    }else{
        $query = $this->db->query("SELECT pregunta.Id_Pregunta,pregunta.Pregunta,pregunta.OpcionA,pregunta.OpcionB,pregunta.OpcionC,pregunta.Correcta,pregunta.Dificultad,detalle_pregu.respondida,detalle_pregu.terminada,detalle_pregu.habilitado FROM `pregunta`INNER JOIN detalle_pregu ON pregunta.Id_Pregunta = detalle_pregu.fk_IdPreguta WHERE detalle_pregu.fk_idUserDP='".$Id."' ORDER BY Pregunta.Dificultad DESC
        ");
        $respuesta = array('Error' => FALSE,
                            'Linea'=> $query->result_array());
    $this->response($respuesta);
    }
//SELECT pregunta.Id_Pregunta,pregunta.Dificultad,detalle_pregu.respondida,detalle_pregu.terminada,detalle_pregu.habilitado FROM `pregunta`INNER JOIN detalle_pregu ON pregunta.Id_Pregunta = detalle_pregu.fk_IdPreguta WHERE detalle_pregu.fk_idUserDP="36" ORDER BY Pregunta.Dificultad ASC

 }

}
