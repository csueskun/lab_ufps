<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Ciudad;

class CiudadController extends Controller
{
    protected $fields = [
        'codigo','nombre','estado'
    ];

    protected $rules = [
        'codigo'=>'required|unique:ciudad','nombre'=>'required|unique:ciudad','estado'=>'required'
    ];
    
    public function all(){
        return response()->json(['data' => Ciudad::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = Ciudad::where($params)->get();
        }
        else{
            $res = Ciudad::whereRaw($whereRaw)->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Ciudad::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Ciudad, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['codigo'] .= ',codigo,'.$id;
        $rules['nombre'] .= ',nombre,'.$id;
        $fields = $this->fields;
        return $this->save($request, Ciudad::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        
        $rules['codigo'] .= ',codigo,'.$id;
        $rules['nombre'] .= ',nombre,'.$id;
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
        return $this->save($request, Ciudad::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Ciudad::destroy($id);
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