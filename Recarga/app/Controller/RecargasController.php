<?php
// Controller/RecargasController.php
class RecargasController extends AppController {

    public $components = array('RequestHandler');

    public function recargas() {

    }

    public function index() {
       
    }

    public function view($celular = null) {

        if(isset($celular)){
            $celular = $celular;
        }else{
            $celular =1;
        }
         $this->loadModel('User');
         $params = array('User.celular' => $celular);
        // $consulta_datos =$this->User->find('all',$params);


        $consulta_datos = $this->User->find('all', array(
            'fields'=>array(
                'User.id',
                'User.last_name',
                'logrecarga.valor_recarga',
                'logrecarga.saldo_actual',
                'logrecarga.segundos_consumidos',
                'logrecarga.segundos_disponibles',
                'logrecarga.create'

                ),
            'joins' => array( 
                    array('table' => 'logs',
                        'alias' => 'logrecarga',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'logrecarga.usuario = User.id'
                        )
                    )
                ),
            'conditions' =>$params,
            'order' => array('logrecarga.id DESC'),

            )
        );

        if(count($consulta_datos) < 1){
            $consulta_datos = array("User" => "No existen Datos");
        }


         $this->set(array(
            'listado_subcategorias' => $consulta_datos,
            '_serialize' => array('listado_subcategorias')
        ));
    }

    public function viewTimepo(){

        $this->loadModel('CostoSegundo');
       // $this->CostoSegundo->find('all');
        $id = $this->CostoSegundo->find('all', array( 'fields' => array('max(id) as id')));

        $valor = $id[0][0];    
        $params = array('CostoSegundo.id' => $valor['id']);
    
        $valor_segundos = $this->CostoSegundo->find('all', array(
            'fields' => array(
                'CostoSegundo.costo as costo',
                'CostoSegundo.segundo as segundo',
                'CostoSegundo.tipo as tipo'
                ),
            'conditions' =>$params
            )
        );

        $this->set(array(
            'valor_segundos' =>  $valor_segundos,
            '_serialize' => array('valor_segundos')
        ));
    }

    public function viewConsumo($celular = null){

        $this->loadModel('Consumo');
        $this->loadModel('User');
        if($celular){
              $params = array('User.celular' => $celular);
            $id = $this->User->find('all', array( 'fields' => array('id as id'), 'conditions' =>$params ));
            $iduser = $id[0]['User']; 
           
     
            $params = array('Consumo.usuario' => $iduser['id']);

            /*$id = $this->Consumo->find('all', array( 'fields' => array('max(id) as id'),'conditions' =>$params));

            $valor = $id[0][0];    
            $params = array('Consumo.usuario' => $valor['id']);*/

            $sqlconsumo = $this->Consumo->find('all', array('conditions' =>$params, 'order' => array('Consumo.id  DESC')));   


            $this->set(array(
                'valor_consumo' =>  $sqlconsumo,
                '_serialize' => array('valor_consumo')
            ));
        }
    }

    public function viewUtimaRecarga($celular = null){

        App::import('Model', 'Log');
         
        $log = new Log();

        $this->loadModel('User');
         $params = array('User.celular' => $celular);
        // $consulta_datos =$this->User->find('all',$params);


        $consulta_datos = $this->User->find('all', array(
            'fields'=>array(
                'User.id'
                ),
            'conditions' =>$params

            )
        );

        $idv = $consulta_datos[0]['User'];
       

        $params = array('Log.usuario' => $idv['id']);
        $id = $log->find('all', array( 
            'fields'=>array(
                'max(Log.id) as id'
            ),
            'conditions' =>$params));

        $valor = $id[0][0];
 
        $params = array('logrecarga.id' => $valor['id']);
        $consulta_datos = $this->User->find('all', array(
            'fields'=>array(
                'logrecarga.id',
                'User.id',
                'User.last_name',
                'User.celular',
                'logrecarga.valor_recarga',
                'logrecarga.saldo_actual',
                'logrecarga.segundos_consumidos',
                'logrecarga.segundos_disponibles',
                'logrecarga.create'

                ),
            'joins' => array( 
                    array('table' => 'logs',
                        'alias' => 'logrecarga',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'logrecarga.usuario = User.id'
                        )
                    )
                ),
            'conditions' =>$params

            )
        );

       
        if(empty($consulta_datos)){
            $consulta_datos = array('mensaje' => 'No se puedo realizar la recarga');
        }

        $this->set(array(
            'valor_recarga' =>  $consulta_datos,
            '_serialize' => array('valor_recarga')
        ));
    }

    public function add() {
        
        $this->Recipe->create();
        $this->loadModel('User');
        $this->loadModel('CostoSegundo');
        App::import('Model', 'Log');

        $id = $this->CostoSegundo->find('all', array( 'fields' => array('max(id) as id')));

        $valor = $id[0][0];    
        $params = array('CostoSegundo.id' => $valor['id']);
    
        $valor_segundos = $this->CostoSegundo->find('all', array(
            'fields' => array(
                'CostoSegundo.costo as costo'
                ),
            'conditions' =>$params
            )
        );

        $cotosegundo = $valor_segundos[0]['CostoSegundo'];
        $cotosegundo = $cotosegundo['costo'];

         
        $log = new Log();
        $params = array('Log.usuario' => $this->request->data['usuario']);
        $id = $log->find('all', array( 
            'fields'=>array(
                'max(Log.id) as id'
            ),
            'conditions' =>$params)); 
        
        $valor = $id[0][0];

        $params = array('logrecarga.id' => $valor['id']);
        $consulta_datos = $this->User->find('all', array(
            'fields'=>array(
                'logrecarga.id',
                'User.id',
                'User.last_name',
                'User.celular',
                'logrecarga.valor_recarga',
                'logrecarga.saldo_actual',
                'logrecarga.segundos_consumidos',
                'logrecarga.segundos_disponibles',
                'logrecarga.create'

                ),
            'joins' => array( 
                    array('table' => 'logs',
                        'alias' => 'logrecarga',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'logrecarga.usuario = User.id'
                        )
                    )
                ),
            'conditions' =>$params

            )
        );

        $user= $consulta_datos[0]['User'];
        $recarga = $consulta_datos[0]['logrecarga'];

       $saldo_actual = $this->request->data['valor_recarga'] + $recarga['saldo_actual'];

       $segundos_disponibles = round(($this->request->data['valor_recarga'] / $cotosegundo),0);
       
       

       $idkey = $log->find('all', array( 
            'fields'=>array(
                'max(Log.id) as id'
            ))); 
       $valorey = $idkey[0][0];
       $Id_sig = $valorey['id']+1;

        $data = array('id'=>$Id_sig, 
            'usuario' => $user['id'], 
            'valor_recarga' => $this->request->data['valor_recarga'],
            'saldo_actual' => $saldo_actual,
            'segundos_consumidos' => $recarga['segundos_consumidos'],
            'segundos_disponibles' => $recarga['segundos_disponibles'] + $segundos_disponibles,
            'create' =>  date("Y-m-d H:i:s"));

        $log->create();

        if ($log->save($data)) {
            $message = array('mensaje' => 'Saved', 'usuario' => $user['celular']);;
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }

    public function addCosto(){
        $this->loadModel('CostoSegundo');
        $this->CostoSegundo->create();

        $id = $this->CostoSegundo->find('all', array( 'fields' => array('max(id) as id')));
    
        $valor = $id[0][0]; 
        $ids = $valor['id'] + 1;

        $sql = $this->CostoSegundo->find('all', array( 'conditions' => array('id' => $valor['id'])));
       
        foreach ($sql as $key => $value) {
            $data = array('id' => $ids, 'costo' => $this->request->data['valor_recarga'] , 'tipo' => $value['CostoSegundo']['tipo'], 'segundo' => $value['CostoSegundo']['segundo'], 'create' =>  date("Y-m-d H:i:s"));
        }
      
         if ($this->CostoSegundo->save($data)) {
            $message = array('mensaje' => 'Saved', 'id' =>$ids );
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }

    public function addConsumo(){
        
        $this->loadModel('CostoSegundo');
        $this->loadModel('Consumo');
        $this->loadModel('Log');
        $this->loadModel('User');
        $this->Consumo->create();
        $this->Log->create();

        $idlog = $this->Log->find('all', array( 'fields' => array('max(Log.id) as id'), 'conditions' => array('Log.usuario' => $this->request->data['usuario']))); 
        $idlog = $idlog[0][0];
        $datoslog = $this->Log->find('all', array('conditions' => array('Log.id' => $idlog['id']))); 
        $log_data = $datoslog[0]['Log'];

        $idcelular = $this->User->find('all', array( 'fields' => array('User.celular as celular'), 'conditions' => array('id' => $this->request->data['usuario'])));
        
        $idcelular = $idcelular[0]['User']; 
        $celular = $idcelular['celular'];

        $idsegundo = $this->CostoSegundo->find('all', array( 'fields' => array('max(id) as id')));
        $valors = $idsegundo[0][0]; 
        
        $sqlsegundo = $this->CostoSegundo->find('all', array( 'fields' => array('costo as costo'), 'conditions' => array('id' => $valors['id'])));

        $valorcs = $sqlsegundo [0]['CostoSegundo'];
        $costo_segundo = $valorcs['costo'];

        $id = $this->Consumo->find('all', array( 'fields' => array('max(id) as id')));
    
        $valor = $id[0][0]; 
        $ids = $valor['id'] + 1;
       
        $sql = $this->Consumo->find('all', array( 'conditions' => array('id' => $valor['id'])));
        $tiempo_consumdo = $this->request->data['valor_recarga'] * $costo_segundo;

        

        foreach ($sql as $key => $value) {
            $data = array('id' => $ids, 
                'usuario' => $this->request->data['usuario'] , 
                'tiempo' => $this->request->data['valor_recarga'], 
                'costo' => $tiempo_consumdo, 
                'costo_segundo' => $costo_segundo,  
                'create' =>  date("Y-m-d H:i:s"));
        }

        
        $idlog = $log_data['id'] + 1;
        $valor_recarga = $log_data['valor_recarga'];
        $saldo_actual =   $log_data['saldo_actual'] - $tiempo_consumdo; 
        $segundos_disponibles = $log_data['segundos_disponibles'] - $this->request->data['valor_recarga'];

        if($log_data['segundos_disponibles']> $this->request->data['valor_recarga']){
            
            $data_log = array('id'=>$idlog, 
            'usuario' => $this->request->data['usuario'], 
            'valor_recarga' => $valor_recarga,
            'saldo_actual' => $saldo_actual,
            'segundos_consumidos' => $this->request->data['valor_recarga'],
            'segundos_disponibles' => $segundos_disponibles,
            'create' =>  date("Y-m-d H:i:s"));
     
            if ($this->Consumo->save($data) && $this->Log->save($data_log)) {
                $message = array('mensaje' => 'Saved', 'celular' => $celular);
            } else {
                $message = 'Error';
            }

        }else{

            $message = "Error";

        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }

    public function edit($id) {
        $this->Recipe->id = $id;
        if ($this->Recipe->save($this->request->data)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }

    public function delete($id) {
        if ($this->Recipe->delete($id)) {
            $message = 'Deleted';
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }
}