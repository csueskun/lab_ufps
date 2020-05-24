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

        $data = Producto::skip($skip)->take($per_page)->with('empresa')->get();
        
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
    
    public function tree(){
        $sql = Producto::select(
                'clase.descripcion as clase', 'clase.id as clase_id', 'grupo.descripcion as grupo', 
                'grupo.id as grupo_id', 'empresa.nombre as empresa', 'empresa.id as empresa_id')
            ->join('empresa', 'empresa.id', '=', 'producto.empresa_id')
            ->join('grupoempresa', 'grupoempresa.empresa_id', '=', 'empresa.id')
            ->join('grupo', 'grupo.id', '=', 'grupoempresa.grupo_id')
            ->join('clase', 'clase.id', '=', 'grupo.clase_id')
            ->groupBy('clase', 'grupo', 'empresa')
            ->get();

        $tree = array();
        $clases = [];
        foreach($sql as $row){

            $clase = $row['clase'];
            $clase_id = $row['clase_id'];
            $grupo = $row['grupo'];
            $grupo_id = $row['grupo_id'];
            $empresa = $row['empresa'];
            $empresa_id = $row['empresa_id'];

            if(in_array($clase, $clases)){
                for ($i=0; $i < count($tree) ; $i++) { 
                    if($tree[$i]['nombre'] == $clase){
                        $added = false;
                        for($j=0;$j<count($tree[$i]['grupos']);$j++) {
                            if($tree[$i]['grupos'][$j]['nombre']==$grupo){
                                $tree[$i]['grupos'][$j]['empresas'][] = ['nombre'=> $empresa, 'id'=>$empresa_id];
                                $added = true;
                            }
                        }
                        if(!$added){
                            $grupos = array();
                            $empresas = array();
                            $empresas[] = ['nombre'=> $empresa, 'id'=>$empresa_id];
                            $grupos[] = ['nombre'=> $grupo, 'id'=> $grupo_id, 'empresas'=>$empresas];
                            $tree[$i]['grupos'] = $grupos;
                        }
                    }
                }
            }
            else{
                $grupos = array();
                $empresas = array();
                $empresas[] = ['nombre'=> $empresa, 'id'=>$empresa_id];
                $grupos[] = ['nombre'=> $grupo, 'id'=> $grupo_id, 'empresas'=>$empresas];
                $tree[] = ['nombre'=>$clase, 'id'=>$clase_id, 'grupos'=>$grupos];
                $clases[] = $clase;
            }
        }

        return response()->json(['data' => $tree]);
    }
}