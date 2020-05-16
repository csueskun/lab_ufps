<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Ingrediente;

class IngredienteController extends Controller
{
    protected $fields = ['codigo', 'descripcion', 'unidad', 'imagen', 'visible', 'grupo'];

    protected $rules = [
        'descripcion' => 'required|unique:ingrediente',
        'unidad' => 'required'
    ];
    
    public function all(){
        return response()->json(['data' => Ingrediente::all()]);
    }
    
    public function find($id){
        $ingrediente = Ingrediente::find($id);
        if($ingrediente){
            return response()->json(['data' => $ingrediente]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Ingrediente, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['descripcion'] .= ',descripcion,'.$id;
        $fields = $this->fields;
        return $this->save($request, Ingrediente::find($id), $rules, $fields);
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
        return $this->save($request, Ingrediente::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Ingrediente::destroy($id);
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