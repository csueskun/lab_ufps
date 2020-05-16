<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\TipoProducto;

class TipoProductoController extends Controller
{
    protected $fields = [
        'codigo','descripcion','prioridad','estado'];

    protected $rules = [
        'codigo'=>'required|unique:tipoproducto','descripcion'=>'required|unique:tipoproducto',
		'prioridad'=>'required','estado'=>'required'
    ];
    
    public function all(){
        return response()->json(['data' => TipoProducto::all()]);
    }
    
	
	public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = TipoProducto::where($params)->get();
        }
        else{
            $res = TipoProducto::whereRaw($whereRaw)->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = TipoProducto::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new TipoProducto, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['codigo'] .= ',codigo,'.$id;
        $rules['descripcion'] .= ',descripcion,'.$id;
        $fields = $this->fields;
        return $this->save($request, TipoProducto::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        $rules['codigo'] .= ',codigo,'.$id;
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
        return $this->save($request, TipoProducto::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = TipoProducto::destroy($id);
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