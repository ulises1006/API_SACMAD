<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Fallas extends REST_Controller {


  public function __construct(){

    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");

    parent::__construct();
    $this->load->database();

  }



  public function registrar_post(){
    $data = $this->post();

    $insertar = array('Descripcion' => $data['descripcion'], 'Laborcode_id' =>$data['num_trabajador'], 'Causa' =>$data['causa'], 'Causa_Raiz' =>$data['causa_raiz'], 'Hora_Falla' =>$data['hora'], 'Tipo_Equipo' => $data['tipo_equipo'], 'Accion' => $data['accion'], 'Descripcion_Solucion' => $data['descripcion_solucion']);
    $this->db->insert( 'registro_fallas', $insertar );

    $respuesta = array(
      'error' => FALSE,
      'mensaje'=> "La falla se ha registrado correctamente."
    );
    $this->response( $respuesta );
  }

  //Funcion borrar tecnico
  public function borrar_falla_delete( $folio = "" ){
    
    if(  $folio == ""  ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> ""
                  );
      $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
      return;
    }
    $condiciones = array('id' => $folio);
    $this->db->where( $condiciones );
    $query = $this->db->get('registro_fallas');

    $existe = $query->row();

    if( !$existe ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> "No existe este registro"
                  );
      $this->response( $respuesta );
      return;
    }
    

    $condiciones = array('id' => $folio );
    $this->db->delete( 'registro_fallas', $condiciones );


    $respuesta = array(
              'error' => FALSE,
              'mensaje'=>'Se ha eliminado el registro correctamente'
          );

    $this->response( $respuesta );

  }

  public function obtener_por_usuario_get($token = "0", $id_usuario ="0" ){

    if( $token == "0" || $id_usuario == "0" ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> "Usuario invalido."
                  );
      $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
      return;
    }

    $condiciones = array('usuario' => $id_usuario, 'token'=> $token );
    $this->db->where( $condiciones );
    $query = $this->db->get('login');

    $existe = $query->row();

    if( !$existe ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> "Usuario y Token incorrectos"
                  );
      $this->response( $respuesta );
      return;
    }

    // Retornar todas las ordenes del usuario
    $query = $this->db->query('SELECT * FROM `registro_fallas` where Laborcode_id = ' . $id_usuario );

    $fallas = array();

    foreach( $query->result() as $row ){

      $query_detalle = $this->db->query('SELECT a.*, b.EqDescription FROM `registro_fallas` a inner join tipo_equipamiento b on a.Tipo_Equipo = b.EqType  where Tipo_Equipo= '. $row->Tipo_Equipo );

      $falla = $query_detalle->result();

      array_push( $fallas, $falla );
      
    }

    $respuesta = array(
                  'error' => FALSE,
                  'fallas'=> $fallas
                );

      $this->response( $respuesta );

  }


  public function obtener_todos_get( ){

    $query = $this->db->query('SELECT a.*, b.EqDescription FROM `registro_fallas` a inner join tipo_equipamiento b on a.Tipo_Equipo = b.EqType');

    $respuesta = array(
            'error' => FALSE,
            'falla' => $query->result_array()
          );

    $this->response( $respuesta );

  }
}