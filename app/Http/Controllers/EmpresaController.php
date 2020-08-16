<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Empresa;
use App\Feedback;

class EmpresaController extends Controller
{
    protected $fields = [
        'codemp', 'nombre', 'descripcion',  'direccion', 'telefono', 'nomcorto', 
        'celular1', 'celular2',  'email', 'contacto', 
        'descrip_comida', 'rutalogo', 'rutafoto', 'coordenadax', 
        'coordenaday', 'valor_contrato', 'sitioweb', 'instagram', 'facebook', 'fechaini_actividad', 'prioridad', 
        'observacion', 'etiquetas', 'estado', 'ciudad_id', 'rutafoto2', 'rutafoto3'];

    protected $rules = [
        'codemp' => 'required|unique:empresa', 'nombre' => 'required|unique:empresa', 'descripcion' => 'required', 
        'direccion' => 'required', 'telefono' => 'required', 'nomcorto' => '', 'celular1' => '', 'celular2' => '', 
        'email' => '', 'contacto' => '', 'descrip_comida' => '', 
        'rutalogo' => '', 'rutafoto' => '', 'coordenadax' => '', 'coordenaday' => '', 
        'valor_contrato' => '', 'sitioweb' => '', 'instagram' => '', 'facebook' => '', 'fechaini_actividad' => '', 'prioridad' => '', 
        'observacion' => '', 'etiquetas' => '', 'estado' => '' ,'ciudad_id' => 'required' , 'rutafoto2' => '', 'rutafoto3' => ''
    ];
    
    public function all(){
        return response()->json(['data' => Empresa::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = Empresa::where($params)->with('ciudad')->with('feedback')->get();
        }
        else{
            $whereRaw = str_replace('s3lect', 'select', $whereRaw);
            $res = Empresa::whereRaw($whereRaw)->with('ciudad')->with('feedback')->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Empresa::where('id', $id)->with('productos.tipoproducto')->with('feedback')->first();
        if($model){

            $feedback = Feedback::where('empresa_id', $id)->first();
            if(!$feedback){
                $feedback = new Feedback;
                $feedback->empresa_id = $id;
                $feedback->visitas = 0;
            }
            $feedback->visitas = $feedback->visitas + 1;
            $feedback->save();

            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Empresa, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['nombre'] .= ',nombre,'.$id;
        $rules['codemp'] .= ',codemp,'.$id;
        $fields = $this->fields;
        return $this->save($request, Empresa::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        $rules['nombre'] .= ',nombre,'.$id;
        $rules['codemp'] .= ',codemp,'.$id;
        $fields = $this->fields;
        foreach ($rules as $key => $value) {
            if(!$request->has($key)){
                unset($rules[$key]);
            }
        }
        $fieldCount = count($fields);
        for ($i=0; $i < $fieldCount ;$i++) { 
            if(!$request->has($fields[$i])){
                unset($fields[$i]);
            }
        }
        return $this->save($request, Empresa::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Empresa::destroy($id);
        if($res){
            return response()->json(['data' => $res]);
        }
        else{
            return response()->json(['data' => $res], 422);
        }
    }

    public function save($request, $model, $rules, $fields){
        $this->validate($request, $rules);
        foreach($fields as $field){
            $model->$field = $request->input($field);
        }
        $res = $model->save();
        if($res){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json(['data' => $res], 422);
        }
    }

    public function paginate(Request $request){
        $per_page = 30;
        $current_page = 1;
        $params = $request->request->all();
        if(array_key_exists('per_page', $params)){
            $per_page = intval($params['per_page']);
        }
        if(array_key_exists('current_page', $params)){
            $current_page = intval($params['current_page']);
        }
        $where = [];
        $pagination = new \stdClass;
        $pagination->pagination = new \stdClass;

        if(array_key_exists('clase', $params)){
            $where['clase.id'] = $params['clase']; 
            $pagination->pagination->clase = intval($params['clase']);
        }
        if(array_key_exists('grupo', $params)){
            $where['grupo.id'] = $params['grupo']; 
            $pagination->pagination->grupo = intval($params['grupo']);
        }
        if(array_key_exists('grupo_in', $params)){
            $pagination->pagination->grupo = intval($params['grupo_in']);
        }
        // if(array_key_exists('search', $params)){
        //     if($params['search'] != ''){
        //         $where[] = ['producto.descripcion', 'like', '%'.$params['search'].'%']; 
        //         $pagination->pagination->search = $params['search'];
        //     }
        // }
        if(array_key_exists('nombre', $params)){
            $where[] = ['empresa.nombre', 'like', '%'.$params['nombre'].'%']; 
            $pagination->pagination->nombre = $params['nombre'];
        }
        $total = new Empresa;
        $total = $total->select('empresa.id')
        ->join('grupoempresa', 'grupoempresa.empresa_id', '=', 'empresa.id')
        ->join('grupo', 'grupo.id', '=', 'grupoempresa.grupo_id')
        ->join('clase', 'clase.id', '=', 'grupo.clase_id')
        ->join('producto', 'producto.empresa_id', '=', 'empresa.id')
        ->where($where);
        if(array_key_exists('grupo_in', $params)){
            $total = $total->whereIn('grupo.id', $params['grupo_in']);
        }
        if(array_key_exists('search', $params)){
            if($params['search'] != ''){
                $param = $params['search'];
                $total = $total->where(function($w) use($param){
                    $w->where('empresa.nombre', 'like', '%'.$param.'%')
                    ->orWhere('producto.etiquetas', 'like', '%'.$param.'%')
                    ->orWhere('producto.descripcion', 'like', '%'.$param.'%');
                });
            }
        }
        $total = $total
        ->distinct()
        ->get();
        
        $total = count($total);
        $pagination->pagination->last_page =  ceil($total/$per_page);

        if($current_page>$pagination->pagination->last_page){
            $current_page = $pagination->pagination->last_page;
        }
        elseif($current_page<1){
            $current_page = 1;
        }
        $skip = $per_page * ($current_page-1);

        $data = new Empresa;
        $data = $data
        ->select('empresa.*')
        ->join('grupoempresa', 'grupoempresa.empresa_id', '=', 'empresa.id')
        ->join('grupo', 'grupo.id', '=', 'grupoempresa.grupo_id')
        ->join('clase', 'clase.id', '=', 'grupo.clase_id')
        ->join('producto', 'producto.empresa_id', '=', 'empresa.id')
        ->where($where);
        if(array_key_exists('grupo_in', $params)){
            $data = $data->whereIn('grupo.id', $params['grupo_in']);
        }
        if(array_key_exists('search', $params)){
            if($params['search'] != ''){
                $param = $params['search'];
                $data = $data->where(function($w) use($param){
                    $w->where('empresa.nombre', 'like', '%'.$param.'%')
                    ->orWhere('producto.etiquetas', 'like', '%'.$param.'%')
                    ->orWhere('producto.descripcion', 'like', '%'.$param.'%');
                });
            }
        }
        $data = $data
        ->skip($skip)
        ->take($per_page)
        ->distinct()
        ->get();
        
        $pagination->pagination->current_page =  $current_page;
        $pagination->pagination->per_page =  $per_page;
        $pagination->pagination->total =  $total;
        $from = $skip + 1;
        $pagination->pagination->from =  $from;
        $to = ($total < $from + $per_page) ? $total : $per_page * $current_page;
        $pagination->pagination->to =  $to;
        $pagination->pagination->showing =  $to - $from + 1;
        $pagination->data =  $data;
        
        return response()->json(['data' => $pagination]);
    }
}