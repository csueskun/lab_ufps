<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Servicio;

class ServicioController extends Controller
{
    protected $fields = [
	'descripcion' ,   'prioridad' ,   'estado' ,'icono' 
     ];

    protected $rules = [
        'descripcion' => 'required|unique:servicio', 'prioridad' => 'required', 
        'estado' => 'required',  'icono' => ''
    ];
    
    public function all(){
        return response()->json(['data' => Servicio::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = Servicio::where($params)->get();
        }
        else{
            $res = Servicio::whereRaw($whereRaw)->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Servicio::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Servicio, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['descripcion'] .= ',descripcion,'.$id;
        $fields = $this->fields;
        return $this->save($request, Servicio::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        $rules['descripcion'] .= ',descripcion,'.$id;
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
        return $this->save($request, Servicio::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Servicio::destroy($id);
        if($res){
            return response()->json(['data' => $model]);
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