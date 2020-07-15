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
        'empresa_id' => 'required', 'visitas' => '', 'likes' => '', 
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
    
    public function paginate(Request $request){
        $per_page = 20;
        $current_page = 1;
        $params = $request->request->all();
        if(array_key_exists('per_page', $params)){
            $per_page = intval($params['per_page']);
        }
        if(array_key_exists('current_page', $params)){
            $current_page = intval($params['current_page']);
        }
        $where = [];
        $pagination = new \stdClass;
        $pagination->pagination = new \stdClass;

        if(array_key_exists('empresa', $params)){
            $where['empresa.id'] = $params['empresa']; 
            $pagination->pagination->empresa = intval($params['empresa']);
        }    
        if(array_key_exists('search', $params)){
            if($params['search'] != ''){
                $where[] = ['empresa.nombre', 'like', '%'.$params['search'].'%']; 
                $pagination->pagination->search = $params['search'];
            }
        }    

        $total = FeedBack::select('feedback.id')
        ->join('empresa', 'empresa.id', '=', 'feedback.empresa_id')
        ->where($where)
        ->distinct()
        ->get();
        
        $total = count($total);
        $pagination->pagination->last_page =  ceil($total/$per_page);

        if($current_page>$pagination->pagination->last_page){
            $current_page = $pagination->pagination->last_page;
        }
        elseif($current_page<1){
            $current_page = 1;
        }
        $skip = $per_page * ($current_page-1);

        $data = FeedBack::select('feedback.*')
            ->join('empresa', 'empresa.id', '=', 'feedback.empresa_id')
            ->where($where)
            ->with('empresa')
            ->with('grupo')
            ->skip($skip)
            ->take($per_page)
            ->distinct()
            ->get();
        
        $pagination->pagination->current_page =  $current_page;
        $pagination->pagination->per_page =  $per_page;
        $pagination->pagination->total =  $total;
        $from = $skip + 1;
        $pagination->pagination->from =  $from;
        $to = ($total < $from + $per_page) ? $total : $per_page * $current_page;
        $pagination->pagination->to =  $to;
        $pagination->pagination->showing =  $to - $from + 1;
        $pagination->data =  $data;
        
        return response()->json(['data' => $pagination]);
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
    
        
		$fields = $this->fields;
        return $this->save($request, Feedback::find($id), $rules, $fields);
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