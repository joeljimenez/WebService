<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller;

class Respuestas extends REST_Controller {

   public function __construct(){
	header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE");
	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
	header("Access-Control-Allow-Origin: *");
	   parent:: __construct();

	   $this->load->database();
   }
//la logica del juego esta aqui.
public function Preguntas_Respondidas_post($Id="0",$Token="0"){//para guardar para cada usuario su pregunta que respondio
$data=$this->post();
if($Id == "0" ||$Token =="0"){
    $Respuesta = array('Error' =>TRUE ,
                      'Mensaje'=>'Token o ID Del usuario Invalidos' );
                      $this->response($Respuesta);
                      return;
}

if(!isset($data['dificultad']) OR !isset($data['respondida']) OR
!isset($data['terminada']) OR !isset($data['habilitado'])OR 
!isset($data['Fecha_Partida']) OR !isset($data['Duracion'] )OR 
!isset($data['Puntacion']) OR !isset($data['nivel'])OR
                      !isset($data['Id_Pre'])){
$respuesta = array('error' =>TRUE , 
         'Mensaje'=> 'Datos vacios');
         $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
  
}else{//todo esta bien
  $condiciones = array ('Id_User'=>$Id,
                        'Token'=>$Token);
                        $this->db->where($condiciones);
                        $query = $this->db->get('usuario');
                $existe=$query->row();

                if(!$existe){
                    $respuesta = array('error' =>TRUE , 
                    'Mensaje'=> 'No Existe Id ni Token');
                    $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
                   
                }else{
                    //cuando todo esta bien.
                     
                        $Actualizar = array('dificultad' => $data['dificultad'] ,
                                            'respondida' => $data['respondida'],
                                            'terminada' =>  $data['terminada'],
                                            'habilitado' => $data['habilitado']);
                                            $this->db->where('fk_idUserDP',$Id);
                                            $this->db->update('detalle_pregu',$Actualizar);

                                            $Respuesta = array('Error' =>FALSE ,
                                                                 'Mensaje'=>'Pregunta Actualizada');
                    $this->response($Respuesta);
                }
}
}

public function intentos_post($Id="0",$Token="0",$IdPre="0"){//para ir contanto los intentos
    $Intento=null;
    if($Id == "0" ||$Token =="0"||$IdPre =="0"){
        $Respuesta = array('Error' =>TRUE ,
                          'Mensaje'=>'Token o ID Del usuario Invalidos' );
                          $this->response($Respuesta);
                          return;
         }else{
////////////////////////////////////
           $condiciones = array ('Id_User'=>$Id,
           'Token'=>$Token);
           $this->db->where($condiciones);
           $query = $this->db->get('usuario');
           $existe=$query->row();

           if(!$existe){
           $respuesta = array('error' =>TRUE , 
           'Mensaje'=> 'No Esta Autenticado');
           $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);

           }else{   
     ///////////////////////////////////////////
     $query = $this->db->query("SELECT intentos FROM `detalle_pregu` WHERE fk_idUserDP = '".$Id."' and fk_IdPreguta='".$IdPre."'");
     foreach ($query->result_array() as $row)
  { 
      $Intento = $row['intentos'];
      $this->response($Intento);
  }

  if(!$Intento){
    $this->db->reset_query();

    $query = $this->db->query("SELECT Dificultad FROM `pregunta` WHERE Id_Pregunta ='".$IdPre."'");
    foreach ($query->result_array() as $row)
 { 
     $Dificultad = $row['Dificultad'];
 }
    $Intento =1;
        $Insertar = array('intentos' => $Intento,
                          'fk_idUserDP'=> $Id ,
                          'fk_IdPreguta'=>$IdPre,
                          'dificultad '=>$Dificultad);

        $this->db->insert('detalle_pregu',$Insertar);
        $respuesta = array('Error' =>TRUE ,
        'Mensaje' =>'Se Inserto');
        $this->response($respuesta); 
     }else{
    if($Intento!=null){//cuando el intento es distinto a null
        $Intento = $Intento + 1;
        $Condiciones = array('fk_idUserDP'=>$Id,
                            'fk_IdPreguta'=>$IdPre);
        $Actualizar = array('intentos' => $Intento);
        $this->db->where($Condiciones);
        $this->db->update('detalle_pregu',$Actualizar);
        $respuesta = array('Error' =>TRUE ,
        'Mensaje' => $Intento,$IdPre,$Id );
        $this->response($respuesta); 
    }
    }
                  
    }

    }

    }

    public function detelle_Usuario_post($Id="0",$Token="0"){
      $data=$this->post();

      if($Id == "0" ||$Token =="0"){
        $Respuesta = array('Error' =>TRUE ,
                          'Mensaje'=>'Token o ID Del usuario Invalidos' );
                          $this->response($Respuesta);
                          return;
         }
         else{
         

            $condiciones = array ('Id_User'=>$Id,
            'Token'=>$Token);
            $this->db->where($condiciones);
            $query = $this->db->get('usuario');
            $existe=$query->row();

if(!$existe){
$respuesta = array('error' =>TRUE , 
'Mensaje'=> 'No Esta Autenticado');
$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);

}else{
//
$punto=0;
$nivel=1;

$query = $this->db->query("SELECT  Puntacion FROM `detalle_usuario` where fk_idDe = '".$Id."'");
foreach ($query->result_array() as $row)
{ 
    $punto = $row['Puntacion'];
}
$this->response($punto);
if($punto!==0){
   
    if($punto<=2000){
        $nivel = 1;
        }else if($punto >= 2000 && $punto<=4000){
           $nivel = 2;
        }else if($punto >= 4000 && $punto<=6000){
           $nivel = 3;
        }else if($punto >= 6000 && $punto<=8000){
        $nivel=4;
        }
     
        $Actualizar = array('Fecha_Partida' => $data['Fecha_partida'],
                            'Hora_Partida'=> $data['Duracion_Partida'] ,
                            'Puntacion'=>$punto,
                            'nivel' =>$nivel,
                             'fk_idDe'=>$Id);
    
        $condiciones = array('fk_idDe' => $Id);
        $this->db->where($condiciones);
        $this->db->update('detalle_usuario',$Actualizar);
    
    
        $respuesta = array('Error' =>TRUE ,
    
        'Mensaje' =>'Actualizado');
        $this->response($respuesta); 
    }else{
    
        $condiciones = array('fk_idDe' => $Id);
        $this->db->where($condiciones);
        $query = $this->db->get('detalle_usuario');
        $existe=$query->row();
        
            $Insertar = array('Fecha_Partida' => $data['Fecha_partida'],
                              'Hora_Partida'=> $data['Duracion_Partida'] ,
                              'Puntacion'=>0,
                              'nivel' =>1,
                            'fk_idDe'=>$Id);
    
        $this->db->insert('detalle_usuario',$Insertar);
    
        $respuesta = array('Error' =>TRUE ,
    
        'Mensaje' =>'Insertado');
        $this->response($respuesta); 
    }
}
}
    }


 
public function actualizar_respuestas_put($Id="0",$Token="0",$IdPre="0"){
    $data=$this->put();
    if($Id == "0" ||$Token =="0"||$IdPre =="0"){
        $Respuesta = array('Error' =>TRUE ,
                          'Mensaje'=>'Token o ID Del usuario Invalidos' );
                          $this->response($Respuesta);
                          return;
         }else{
            $condiciones = array ('Id_User'=>$Id,
            'Token'=>$Token);
            $this->db->where($condiciones);
            $query = $this->db->get('usuario');
            $existe=$query->row();
 
            if(!$existe){
            $respuesta = array('error' =>TRUE , 
            'Mensaje'=> 'No Esta Autenticado');
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
 
            }else{  
//ya se puede actualizar en la tabla porque es jugador esta correctamente auntenticado :)

if($data['Respondida']=="0"){
    $data['Respondida']=FALSE;
}else if ($data['Respondida']=="1"){
    $data['Respondida']=TRUE;
}

if($data['Habilitado']=="0"){
    $data['Habilitado']=FALSE;
}else if ($data['Habilitado']=="1"){
    $data['Habilitado']=TRUE;
}

if($data['Terminado']=="0"){
    $data['Terminado']=FALSE;
}else if ($data['Terminado']=="1"){
    $data['Terminado']=TRUE;
}



$Condiciones = array('fk_idUserDP'=>$Id,
'fk_IdPreguta'=>$IdPre);
$Actualizar = array('respondida' => $data['Respondida'],
                    'terminada' =>  $data['Terminado'],
                    'habilitado' => $data['Habilitado'],);
$this->db->where($Condiciones);
$this->db->update('detalle_pregu',$Actualizar);

$respuesta = array('Error' =>TRUE ,
'Mensaje' => 'Actualizado Sin Error' );
$this->response($respuesta); 

            }

}

}


public function actualizar_puntaje_put($Id="0",$Token="0",$IdPre="0"){
$sumaPuntaje=100;
    if($Id == "0" ||$Token =="0"){
      $Respuesta = array('Error' =>TRUE ,
                        'Mensaje'=>'Token o ID Del usuario Invalidos' );
                        $this->response($Respuesta);
                        return;
       }
       else{
       

          $condiciones = array ('Id_User'=>$Id,
          'Token'=>$Token);
          $this->db->where($condiciones);
          $query = $this->db->get('usuario');
          $existe=$query->row();

if(!$existe){
$respuesta = array('error' =>TRUE , 
'Mensaje'=> 'No Esta Autenticado');
$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);

}else{
    $query = $this->db->query("SELECT  Puntacion FROM `detalle_usuario` where fk_idDe = '".$Id."'");
    foreach ($query->result_array() as $row)
    { 
        $punto = $row['Puntacion'];
    }
 
   //suma de puntos deacuerdo a la dificultad de la pregunta

   $this->db->reset_query();

   $query = $this->db->query("SELECT Dificultad FROM `pregunta` WHERE Id_Pregunta ='".$IdPre."'");
   foreach ($query->result_array() as $row)
{ 
    $Dificultad = $row['Dificultad'];
}

if($Dificultad ==="1"){
    $sumaPuntaje=$punto+200;
}
else if($Dificultad ==="2"){
    $sumaPuntaje=$punto+400;
}
else if($Dificultad ==="3"){
    $sumaPuntaje=$punto+600;
}

if($sumaPuntaje<=2000){
    $nivel = 1;
    }else if($sumaPuntaje >= 2000 && $sumaPuntaje<=4000){
       $nivel = 2;
    }else if($sumaPuntaje >= 4000 && $sumaPuntaje<=6000){
       $nivel = 3;
    }else if($sumaPuntaje >= 6000 && $sumaPuntaje<=8000){
    $nivel=4;
    }


  $Actualizar = array('Puntacion'=>$sumaPuntaje,'nivel'=>$nivel);
    
        $condiciones = array('fk_idDe' => $Id);
        $this->db->where($condiciones);
        $this->db->update('detalle_usuario',$Actualizar);

        $respuesta = array('Error' =>FALSE ,
                           'Mensaje'=>'Puntaje Actualizado' );
                           $this->response($respuesta);
}

}

}

public function traer_puntaje_get(){
 
    $query=$this->db->query("SELECT  Puntacion , nivel , usuario FROM usuario INNER JOIN detalle_usuario ON usuario.Id_User = detalle_usuario.fk_idDe ORDER BY Puntacion DESC");
           $respuesta = array( 'Linea'=>$query->result_array() );
      $this->response($respuesta);
}




}