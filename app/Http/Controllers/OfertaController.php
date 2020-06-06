<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Oferta;

class OfertaController extends Controller
{
    protected $fields = [
        'empresa_id', 'fechaini', 'fechafin', 'horaini', 'horafin', 'rutafoto',  'tipo', 'likes', 
        'estado',   'prioridad'
		];

    protected $rules = [
        'empresa_id' => 'required:oferta', 'fechaini' => 'required:oferta',  'fechafin' => 'required',
        'horaini' => 'required',  'horafin' => 'required',
        'rutafoto' => '', 'tipo' => 'required', 'likes' => '', 'estado' => 'required', 
        'prioridad' => 'requiered'
    ];
    
    public function all(){
        return response()->json(['data' => Oferta::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = Oferta::where($params)->with('empresa')->get();
        }
        else{
            $res = Oferta::whereRaw($whereRaw)->with('empresa')->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Oferta::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Oferta, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['empresa_id'] .= ',empresa_id,'.$id;
        $rules['fechaini'] .= ',fechaini,'.$id;
        $fields = $this->fields;
        return $this->save($request, Oferta::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        $rules['empresa_id'] .= ',empresa_id,'.$id;
        $rules['fechaini'] .= ',fechaini,'.$id;
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
        return $this->save($request, Oferta::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Oferta::destroy($id);
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