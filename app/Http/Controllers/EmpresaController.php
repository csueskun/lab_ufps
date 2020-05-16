<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Empresa;

class EmpresaController extends Controller
{
    protected $fields = [
        'codemp', 'nombre', 'descripcion', 'nomcorto', 'telefono', 
        'celular1', 'celular2', 'direccion', 'email', 'contacto', 
        'descrip_comida', 'rutalogo', 'rutafoto', 'coordenadax', 
        'coordenaday', 'valor_contrato', 'sitioweb', 
        'instagram', 'facebook', 'fechaini_actividad', 'prioridad', 
        'observacion', 'etiquetas', 'estado'];

    protected $rules = [
        'codemp' => 'required|unique:empresa', 'nombre' => 'required|unique:empresa', 'descripcion' => 'required', 
        'nomcorto' => '', 'telefono' => 'required', 'celular1' => '', 'celular2' => '', 
        'direccion' => 'required', 'email' => '', 'contacto' => '', 'descrip_comida' => 'required', 
        'rutalogo' => '', 'rutafoto' => '', 'coordenadax' => '', 'coordenaday' => '', 
        'valor_contrato' => '', 'sitioweb' => '', 
        'instagram' => '', 'facebook' => '', 'fechaini_actividad' => '', 'prioridad' => '', 
        'observacion' => '', 'etiquetas' => '', 'estado' => ''
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
            $res = Empresa::where($params)->get();
        }
        else{
            $whereRaw = str_replace('s3lect', 'select', $whereRaw);
            $res = Empresa::whereRaw($whereRaw)->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Empresa::find($id);
        if($model){
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
}