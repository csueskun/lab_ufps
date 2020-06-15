<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\News;

class NewsController extends Controller
{
    protected $fields = [
	
        'titulo', 'empresa_id', 'fechaini', 'fechafin',  'descripcion', 'resumen', 'likes', 
        'rutafoto', 'estado',   'prioridad'];

    protected $rules = [
        'titulo' => 'required|unique:news', 'empresa_id' => 'required', 'fechaini' => 'required', 'fechafin' => 'required', 
		 'descripcion' => 'required',  'resumen' => 'required', 'likes' => '', 'rutafoto' => '', 'estado' => 'required', 
        'prioridad' => 'required'
    ];
    
    public function all(){
        return response()->json(['data' => News::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = News::where($params)->with('empresa')->get();
        }
        else{
            $res = News::whereRaw($whereRaw)->with('empresa')->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = News::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new News, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['titulo'] .= ',titulo,'.$id;
        
        $fields = $this->fields;
        return $this->save($request, News::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        $rules['titulo'] .= ',titulo,'.$id;
        
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
        return $this->save($request, News::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = News::destroy($id);
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