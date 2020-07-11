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

    //paginacion
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
        //count de registros
        $total = News::select('news.id')
        ->join('empresa', 'empresa.id', '=', 'news.empresa_id')
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
        //lista
        $data = News::select('news.*')
            ->join('empresa', 'empresa.id', '=', 'news.empresa_id')
            ->where($where)
            ->with('empresa')
            
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