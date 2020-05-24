<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Producto;

class ProductoController extends Controller
{
    protected $fields = [
	'codigo' ,   'descripcion' ,   'detalle' ,   'empresa_id' ,
    'tipoproducto_id' ,  'tipocategoria_id'  ,  'observacion'  ,  'tamano' ,
    'precio1'  ,   'precio2'  ,   'precio3'  ,   'precio4'  ,
    'imagen' ,   'prioridad' ,   'estado' ,   'etiquetas' ,   'bodega' ,
    'unidad' ,   'marca'  ,   'terminado' ,   'compuesto' ,   'combo' ,   'iva' ,   'impco' 
     ];

    protected $rules = [
        'codigo' => 'required|unique:producto', 'descripcion' => 'required|unique:producto',  'detalle' => 'required', 
        'empresa_id' => 'required', 'tipoproducto_id' => 'required', 'tipocategoria_id' => 'required', 'observacion' => '', 
        'tamano' => 'required', 'precio1' => 'required', 'precio2' => '', 'precio3' => '', 
        'precio4' => '', 'imagen' => '', 'impco' => '', 'iva' => '', 'combo' => '', 'compuesto' => '', 
        'unidad' => '', 'marca' => '', 'terminado' => '', 'prioridad' => 'required', 
        'bodega' => '', 'etiquetas' => '', 'estado' => 'required'
    ];
    
    public function all(){
        return response()->json(['data' => Producto::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = Producto::where($params)->with('empresa')->get();
        }
        else{
            $res = Producto::whereRaw($whereRaw)->with('empresa')->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function paginate(Request $request){
        $per_page = 10;
        $current_page = 1;
        $params = $request->request->all();
        if(array_key_exists('per_page', $params)){
            $per_page = intval($params['per_page']);
        }
        if(array_key_exists('current_page', $params)){
            $current_page = intval($params['current_page']);
        }

        $pagination = new \stdClass;
        $pagination->pagination = new \stdClass;
        $total = Producto::count();
        $pagination->pagination->last_page =  ceil($total/$per_page);

        if($current_page>$pagination->pagination->last_page){
            $current_page = $pagination->pagination->last_page;
        }
        $skip = $per_page * ($current_page-1);

        $data = Producto::skip($skip)->take($per_page)->get();
        
        $pagination->pagination->current_page =  $current_page;
        $pagination->pagination->per_page =  $per_page;
        $pagination->pagination->total =  $total;
        $pagination->data =  $data;
        
        return response()->json(['data' => $pagination]);
    }
    
    public function find($id){
        $model = Producto::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Producto, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['codigo'] .= ',codigo,'.$id;
        $rules['descripcion'] .= ',descripcion,'.$id;
        $fields = $this->fields;
        return $this->save($request, Producto::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        $rules['codigo'] .= ',codigo,'.$id;
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
        return $this->save($request, Producto::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Producto::destroy($id);
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