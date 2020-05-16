<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Atencion;

class AtencionController extends Controller
{
    protected $fields = [
	'horaini_lunes' ,     'horafin_lunes',      'horaini2_lunes',      'horafin2_lunes',
    'horaini_martes' ,   'horafin_martes',    'horaini2_martes' ,   'horafin2_martes',
    'horaini_miercoles','horafin_miercoles', 'horaini2_miercoles','horafin2_miercoles',
    'horaini_jueves',     'horafin_jueves' ,    'horaini2_jueves',     'horafin2_jueves',
    'horaini_viernes',    'horafin_viernes' ,   'horaini2_viernes',    'horafin2_viernes',
    'horaini_sabados',  'horafin_sabados',  'horaini2_sabados',  'horafin2_sabados',
    'horaini_domingo',  'horafin_domingo', 'horaini2_domingo',  'horafin2_domingo',
    'horaini_festivos',    'horafin_festivos',   'horaini2_festivos',    'horafin2_festivos',
    'empresa_id',   'estado'
     ];

    protected $rules = [
        'empresa_id' => 'required|unique:atencion', 'estado' => 'required'
    ];
    
    public function all(){
        return response()->json(['data' => Atencion::all()]);
    }
    
	public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = Atencion::where($params)->with('empresa')->get();
        }
        else{
            $res = Atencion::whereRaw($whereRaw)->with('empresa')->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Atencion::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Atencion, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['empresa_id'] .= ',empresa_id,'.$id;
        $fields = $this->fields;
        return $this->save($request, Atencion::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        $rules['empresa_id'] .= ',empresa_id,'.$id;
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
        return $this->save($request, Atencion::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Atencion::destroy($id);
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