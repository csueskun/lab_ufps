<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Registro;
use App\Feedback;

class RegistroController extends Controller
{
    protected $fields = [
        'phone_id','empresa_id','tipoval','valor'
    ];

    protected $rules = [
        'phone_id'=>'required', 'empresa_id'=>'required', 'tipoval'=>'required', 'valor'=>'required', 
    ];
    
    public function all(){
        return response()->json(['data' => Registro::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        unset($params['api_token']);
        $res = Registro::where($params)->with('empresa')->get();
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Registro::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Registro, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $fields = $this->fields;
        return $this->save($request, Registro::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
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
        return $this->save($request, Registro::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Registro::destroy($id);
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
            return $this->updateFeedback($model);
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json(['data' => $res], 422);
        }
    }

    public function updateFeedback($reg){
        $feedback = Feedback::where('empresa_id', $reg->empresa_id)->first();
        if($reg->valor < 1){
            $reg->valor = -1;
        }
        if(!$feedback){
            $feedback = new Feedback;
            $feedback->empresa_id = $reg->empresa_id;
            $feedback->likes = 0;
            $feedback->favorito = 0;
        }
        if($reg->tipoval == 1){
            $feedback->likes = $feedback->likes + $reg->valor;
        }
        elseif($reg->tipoval == 2){
            $feedback->favorito = $feedback->favorito + $reg->valor;
        }
        else{
            $avg = Registro::where('empresa_id', $reg->empresa_id)->where('tipoval', $reg->tipoval)->groupBy('empresa_id')->avg('valor');
            if($reg->tipoval == 3){ $feedback->comida = $avg; }
            elseif($reg->tipoval == 4){ $feedback->servicio = $avg; }
            elseif($reg->tipoval == 5){ $feedback->precios = $avg; }
            elseif($reg->tipoval == 6){ $feedback->infraestructura = $avg; }
            elseif($reg->tipoval == 7){ $feedback->personal = $avg; }
        }
        $feedback->save();
    }
}