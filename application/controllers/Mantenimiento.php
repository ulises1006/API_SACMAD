<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;


class Mantenimiento extends REST_Controller {


  public function __construct(){

    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    header("Access-Control-Allow-Origin: *");

    parent::__construct();
    $this->load->database();

  }

  // Insertar tecnico

  public function registrar_post(){
    $data = $this->post();

    if( !isset($data['fecha']) || !isset($data['equipo']) || !isset($data['descripcion']) ||!isset($data['trabajador']) || !isset($data['tiempo']) || !isset($data['planta']) || !isset($data['frecuencia']) || !isset($data['tipo']) ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> "Debes rellenar todos los datos."
                  );
      $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
      return;
    }

    $insertar = array('Fecha' => $data['fecha'], 'EqNum' =>$data['equipo'], 'Descripcion' =>$data['descripcion'], 'Laborcode_id' =>$data['trabajador'], 'Tiempo_Utilizado' => $data['tiempo'], 'Planta' => $data['planta'], 'Frecuencias' => $data['frecuencia'], 'Tipo_Mantenimiento' => $data['tipo'] );
    $this->db->insert( 'mantenimiento', $insertar );

    $respuesta = array(
      'error' => FALSE,
      'mensaje'=> "Mantenimiento registrado con éxito."
    );
    $this->response( $respuesta );
  }

  //Funcion borrar tecnico
  public function borrar_mantenimiento_delete( $folio = "" ){
    
    if(  $folio == ""  ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> ""
                  );
      $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
      return;
    }
    $condiciones = array('Folio' => $folio);
    $this->db->where( $condiciones );
    $query = $this->db->get('mantenimiento');

    $existe = $query->row();

    if( !$existe ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> "Folio no encontrado"
                  );
      $this->response( $respuesta );
      return;
    }
    
    $condiciones = array('Folio' => $folio );
    $this->db->delete( 'mantenimiento', $condiciones );


    $respuesta = array(
              'error' => FALSE,
              'mensaje'=>'El registro se ha eliminado correctamente'
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
    $query = $this->db->query('SELECT * FROM `mantenimiento` where Laborcode_id = ' . $id_usuario );

    $mantenimientos = array();

    foreach( $query->result() as $row ){

      $query_detalle = $this->db->query('SELECT a.*, b.EqDescription FROM `mantenimiento` a inner join tipo_equipamiento b on a.EqNum = b.EqType  where EqNum= '. $row->EqNum );

      $mantenimiento = $query_detalle->result();

      array_push( $mantenimientos, $mantenimiento );
      
    }

    $respuesta = array(
                  'error' => FALSE,
                  'mantenimiento'=> $mantenimientos
                );

      $this->response( $respuesta );

  }

  public function obtener_todos_get( ){

    $query = $this->db->query('SELECT a.*, b.EqDescription FROM `mantenimiento` a inner join tipo_equipamiento b on a.EqNum = b.EqType' );

    $respuesta = array(
            'error' => FALSE,
            'mantenimiento' => $query->result_array()
          );

    $this->response( $respuesta );

  }
  public function obtener_por_folio_get($folio = "0" ){
    if( $folio == "0" ){
      $respuesta = array(
                    'error' => TRUE,
                    'mensaje'=> "Usuario invalido."
                  );
      $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
      return;
    }

    $query = $this->db->query('SELECT * FROM `mantenimiento` where Folio = ' . $folio );

    $respuesta = array(
            'error' => FALSE,
            'mantenimiento' => $query->result_array()
          );

    $this->response( $respuesta );

  }
  
  public function obtener_equipo_get(){
    $query = $this->db->query('SELECT * FROM `tipo_equipamiento` WHERE EqType LIKE ("%0%")');
    $respuesta = array(
      'error' => FALSE,
      'equipo' => $query->result_array()
    );

    $this->response( $respuesta );
  }

  public function buscar_get( $termino = "no especifico" ){
    
    $query = $this->db->query('SELECT * FROM `mantenimiento` where `Descripcion` LIKE "%'. $termino .'%"');

    $respuesta = array(
        'error' => FALSE,
        'productos' => $query->result_array()
      );

    $this->response( $respuesta );
  }
  
}