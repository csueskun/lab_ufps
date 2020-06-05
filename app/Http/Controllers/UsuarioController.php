<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;

class UsuarioController extends Controller
{
    protected $fields = [
        'usuario', 'nombres', 'apellidos', 'email',  'password', 'celular', 
        'fecnaci', 'direccion'];

    protected $rules = [
        'usuario' => 'required|unique:users', 'nombres' => 'required',
        'apellidos' => 'required', 'email' => 'required|unique:users',
        'password' => 'required', 'celular' => 'required|unique:users',
        'fecnaci' => 'required', 'direccion' => 'required'
    ];
    
    public function all(){
        return response()->json(['data' => User::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = User::where($params)->get();
        }
        else{
            $res = User::whereRaw($whereRaw)->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = User::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new User, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['usuario'] .= ',usuario,'.$id;
        
        $fields = $this->fields;
        return $this->save($request, User::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        $rules['usuario'] .= ',usuario,'.$id;
        
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
        return $this->save($request, User::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = User::destroy($id);
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
            if($field == 'password'){
                $model->$field = Hash::make($request->input($field));
            }
            else{
                $model->$field = $request->input($field);
            }
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