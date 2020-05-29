<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Feedback;

class FeedbackController extends Controller
{
    protected $fields = [
	   'empresa_id','visitas','likes','favorito','comida',
       'servicio','precios','infraestructura','personal','estado','puntaje'
       ];

    protected $rules = [
        'empresa_id' => 'required|unique:feedback', 'visitas' => '', 'likes' => '', 
        'favorito' => '', 'comida' => '', 'servicio' => '', 'precios' => '', 
        'infraestructura' => '', 'personal' => '', 'estado' => 'required' , 'puntaje' => ''
    ];
    
    public function all(){
        return response()->json(['data' => Feedback::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = Feedback::where($params)->with('empresa')->get();
        }
        else{
            $res = Feedback::whereRaw($whereRaw)->with('empresa')->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Feedback::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Feedback, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        
		$rules['empresa_id'] .= ',empresa_id,'.$id;
        
		$fields = $this->fields;
        return $this->save($request, Feedback::find($id), $rules, $fields);
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
        return $this->save($request, Feedback::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Feedback::destroy($id);
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
	
	//  -----------------------------------------------------------------------------------
	
	public function last(){
		$last = Feedback::orderBy('created_at', 'desc')->first();
        return response()->json(['data' => $last], 200);
	}
	
	public function first(){
		$last = Feedback::orderBy('created_at', 'asc')->first();
        return response()->json(['data' => $last], 200);
	}
	
}