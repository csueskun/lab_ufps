<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\GrupoEmpresa;       


class GrupoEmpresaController extends Controller
{
    protected $fields = [
        'empresa_id','grupo_id','estado','prioridad'
		];

    protected $rules = [
        'empresa_id'=>'required|unique:grupoempresa','grupo_id'=>'required|unique:grupoempresa', 'estado'=>'required', 'prioridad'=>'required'
    ];
    
    public function all(){
        return response()->json(['data' => EmpGrupo::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = GrupoEmpresa::where($params)->with('empresa')->get();
        }
        else{
            $res = GrupoEmpresa::whereRaw($whereRaw)->with('empresa')->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = GrupoEmpresa::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new GrupoEmpresa, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
     
        $rules['empresa_id'] .= ',empresa_id,'.$id;
        $rules['grupo_id']   .= ',grupo_id,'.$id;
        
        $fields = $this->fields;
        return $this->save($request, GrupoEmpresa::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        
        $rules['empresa_id'] .= ',empresa_id,'.$id;
        $rules['grupo_id'] .= ',grupo_id,'.$id;
        
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
        return $this->save($request, GrupoEmpresa::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = GrupoEmpresa::destroy($id);
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