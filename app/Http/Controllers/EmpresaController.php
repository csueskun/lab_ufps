<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Empresa;
use App\GrupoEmpresa;
use App\Feedback;
use App\Comentario;
use App\Clase;
use stdClass;

class EmpresaController extends Controller
{
    protected $fields = [
        'codemp', 'nombre', 'descripcion',  'direccion', 'telefono', 'nomcorto', 
        'celular1', 'celular2',  'email', 'contacto', 
        'descrip_comida', 'rutalogo', 'rutafoto', 'coordenadax', 
        'coordenaday', 'valor_contrato', 'sitioweb', 'instagram', 'facebook', 'fechaini_actividad', 'prioridad', 
        'observacion', 'etiquetas', 'estado', 'ciudad_id', 'rutafoto2', 'rutafoto3'];

    protected $rules = [
        'codemp' => 'required|unique:empresa', 'nombre' => 'required|unique:empresa', 'descripcion' => 'required', 
        'direccion' => 'required', 'telefono' => 'required', 'nomcorto' => '', 'celular1' => '', 'celular2' => '', 
        'email' => '', 'contacto' => '', 'descrip_comida' => '', 
        'rutalogo' => '', 'rutafoto' => '', 'coordenadax' => '', 'coordenaday' => '', 
        'valor_contrato' => '', 'sitioweb' => '', 'instagram' => '', 'facebook' => '', 'fechaini_actividad' => '', 'prioridad' => '', 
        'observacion' => '', 'etiquetas' => '', 'estado' => '' ,'ciudad_id' => 'required' , 'rutafoto2' => '', 'rutafoto3' => ''
    ];
    
    public function all(){
        return response()->json(['data' => Empresa::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = Empresa::where($params)->with('ciudad')->with('feedback')->get();
        }
        else{
            $whereRaw = str_replace('s3lect', 'select', $whereRaw);
            $res = Empresa::whereRaw($whereRaw)->with('ciudad')->with('feedback')->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Empresa::where('id', $id)->with('productos.tipoproducto')->with('feedback')->first();
        if($model){

            $feedback = Feedback::where('empresa_id', $id)->first();
            if(!$feedback){
                $feedback = new Feedback;
                $feedback->empresa_id = $id;
                $feedback->visitas = 0;
            }
            $feedback->visitas = $feedback->visitas + 1;
            $feedback->save();

            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function findWithComments($id){
        $model = Empresa::where('id', $id)->with('productos.tipoproducto')->with('feedback')->with('horarios')->first();
        if($model){

            $feedback = Feedback::where('empresa_id', $id)->first();
            if(!$feedback){
                $feedback = new Feedback;
                $feedback->empresa_id = $id;
                $feedback->visitas = 0;
            }
            $feedback->visitas = $feedback->visitas + 1;
            $feedback->save();

            $comentarios = Comentario::select('comentario.*', 'users.nombres as from')
                ->join('users', 'users.phone_id', '=', 'comentario.phone_id', 'left outer')
                ->where('empresa_id', $id)
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();
            $model->comentarios = $comentarios;

            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Empresa, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $rules['nombre'] .= ',nombre,'.$id;
        $rules['codemp'] .= ',codemp,'.$id;
        $fields = $this->fields;
        return $this->save($request, Empresa::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        $rules['nombre'] .= ',nombre,'.$id;
        $rules['codemp'] .= ',codemp,'.$id;
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
        return $this->save($request, Empresa::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Empresa::destroy($id);
        if($res){
            return response()->json(['data' => $res]);
        }
        else{
            return response()->json(['data' => $res], 422);
        }
    }

    public function save($request, $model, $rules, $fields){
        $params = $request->request->all();
        $grupos = [];
        if(array_key_exists('grupos', $params)){
            $grupos = $params['grupos'];
        }

        $this->validate($request, $rules);
        foreach($fields as $field){
            $model->$field = $request->input($field);
        }
        $res = $model->save();
        if($res){
            foreach($grupos as $grupo){
                $ge = new GrupoEmpresa;
                $ge->grupo_id = $grupo;
                $ge->empresa_id = $model->id;
                $ge->prioridad = 0;
                $ge->estado = 1;
                $ge->save();
            }
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json(['data' => $res], 422);
        }
    }

    public function paginate(Request $request){
        $per_page = 30;
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

        if(array_key_exists('clase', $params)){
            $where['clase.id'] = $params['clase']; 
            $pagination->pagination->clase = intval($params['clase']);
        }
        if(array_key_exists('grupo', $params)){
            $where['grupo.id'] = $params['grupo']; 
            $pagination->pagination->grupo = intval($params['grupo']);
        }
        if(array_key_exists('grupo_in', $params)){
            $pagination->pagination->grupo = intval($params['grupo_in']);
        }
        if(array_key_exists('nombre', $params)){
            $where[] = ['empresa.nombre', 'like', '%'.$params['nombre'].'%']; 
            $pagination->pagination->nombre = $params['nombre'];
        }
        $total = new Empresa;
        $total = $total->select('empresa.id')
        ->leftJoin('grupoempresa', 'grupoempresa.empresa_id', '=', 'empresa.id')
        ->leftJoin('grupo', 'grupo.id', '=', 'grupoempresa.grupo_id')
        ->leftJoin('clase', 'clase.id', '=', 'grupo.clase_id')
        ->leftJoin('producto', 'producto.empresa_id', '=', 'empresa.id')
        ->where($where);
        if(array_key_exists('grupo_in', $params)){
            $total = $total->whereIn('grupo.id', $params['grupo_in']);
        }
        if(array_key_exists('search', $params)){
            if($params['search'] != ''){
                $param = $params['search'];
                $total = $total->where(function($w) use($param){
                    $w->where('empresa.nombre', 'like', '%'.$param.'%')
                    ->orWhere('producto.etiquetas', 'like', '%'.$param.'%')
                    ->orWhere('producto.descripcion', 'like', '%'.$param.'%');
                });
            }
        }
        $total = $total
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

        $data = new Empresa;
        $data = $data
        ->select('empresa.*')
        ->leftJoin('grupoempresa', 'grupoempresa.empresa_id', '=', 'empresa.id')
        ->leftJoin('grupo', 'grupo.id', '=', 'grupoempresa.grupo_id')
        ->leftJoin('clase', 'clase.id', '=', 'grupo.clase_id')
        ->leftJoin('producto', 'producto.empresa_id', '=', 'empresa.id')
        ->where($where);
        if(array_key_exists('grupo_in', $params)){
            $data = $data->whereIn('grupo.id', $params['grupo_in']);
        }
        if(array_key_exists('search', $params)){
            if($params['search'] != ''){
                $param = $params['search'];
                $data = $data->where(function($w) use($param){
                    $w->where('empresa.nombre', 'like', '%'.$param.'%')
                    ->orWhere('producto.etiquetas', 'like', '%'.$param.'%')
                    ->orWhere('producto.descripcion', 'like', '%'.$param.'%');
                });
            }
        }
        $data = $data
        ->with('grupos.grupo.clase')
        ->with('feedback')
        ->skip($skip)
        ->take($per_page)
        ->distinct()
        ->get();

        foreach ($data as $e) {
            try {
                $e->feedback->puntaje = ($e->feedback->infraestructura+$e->feedback->personal+$e->feedback->precios+$e->feedback->servicio)/4;
            } catch (\Throwable $th) {
                $e->feedback->puntaje = 0;
            }
        }
        
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

    public function paginateNear(Request $request){
        $per_page = 32;
        $current_page = 1;
        $params = $request->request->all();

        $latitude = 0;
        $longitude = 0;
        $near_me = false;
        if(array_key_exists('latitude', $params)){
            $latitude = floatval($params['latitude']);
        }
        if(array_key_exists('longitude', $params)){
            $longitude = floatval($params['longitude']);
        }
        if(array_key_exists('near_me', $params)){
            $near_me = boolval($params['near_me']);
        }

        $where = [];
        $pagination = new \stdClass;
        $pagination->pagination = new \stdClass;

        if(array_key_exists('clase', $params)){
            $where['clase.id'] = $params['clase']; 
            $pagination->pagination->clase = intval($params['clase']);
        }
        if(array_key_exists('grupo', $params)){
            $where['grupo.id'] = $params['grupo']; 
            $pagination->pagination->grupo = intval($params['grupo']);
        }
        if(array_key_exists('grupo_in', $params)){
            $pagination->pagination->grupo = intval($params['grupo_in']);
        }
        if(array_key_exists('nombre', $params)){
            $where[] = ['empresa.nombre', 'like', '%'.$params['nombre'].'%']; 
            $pagination->pagination->nombre = $params['nombre'];
        }
        $total = new Empresa;
        $total = $total->select('empresa.id')
        ->join('grupoempresa', 'grupoempresa.empresa_id', '=', 'empresa.id')
        ->join('grupo', 'grupo.id', '=', 'grupoempresa.grupo_id')
        ->join('clase', 'clase.id', '=', 'grupo.clase_id')
        ->join('producto', 'producto.empresa_id', '=', 'empresa.id')
        ->where($where);
        if(array_key_exists('grupo_in', $params)){
            $total = $total->whereIn('grupo.id', $params['grupo_in']);
        }
        if(array_key_exists('search', $params)){
            if($params['search'] != ''){
                $param = $params['search'];
                $total = $total->where(function($w) use($param){
                    $w->where('empresa.nombre', 'like', '%'.$param.'%')
                    ->orWhere('producto.etiquetas', 'like', '%'.$param.'%')
                    ->orWhere('producto.descripcion', 'like', '%'.$param.'%');
                });
            }
        }
        $total = $total
        ->distinct()
        ->get();
        
        $total = count($total);
        $pagination->pagination->last_page =  1;
        $skip = 0;

        // ----------------------
        $data = new Empresa;
        $data = $data
        ->select('empresa.id', 'empresa.coordenadax', 'empresa.coordenaday')
        ->join('grupoempresa', 'grupoempresa.empresa_id', '=', 'empresa.id')
        ->join('grupo', 'grupo.id', '=', 'grupoempresa.grupo_id')
        ->join('clase', 'clase.id', '=', 'grupo.clase_id')
        ->join('producto', 'producto.empresa_id', '=', 'empresa.id')
        ->where($where);
        if(array_key_exists('grupo_in', $params)){
            $data = $data->whereIn('grupo.id', $params['grupo_in']);
        }
        if(array_key_exists('search', $params)){
            if($params['search'] != ''){
                $param = $params['search'];
                $data = $data->where(function($w) use($param){
                    $w->where('empresa.nombre', 'like', '%'.$param.'%')
                    ->orWhere('producto.etiquetas', 'like', '%'.$param.'%')
                    ->orWhere('producto.descripcion', 'like', '%'.$param.'%');
                });
            }
        }
        $data = $data
        ->distinct()
        ->get();

        $empresas_ids = [];
        $coordinates_dict = array();

        foreach ($data as $empresa_coord) {
            if($empresa_coord->coordenadax != null && $empresa_coord->coordenadax != null){
                if(is_float($empresa_coord->coordenadax)&&is_float($empresa_coord->coordenaday)){
                    $empresas_ids[] = $empresa_coord->id;
                    $coordinates_dict['id'.$empresa_coord->id] = $this->distance(
                        $latitude, $longitude, $empresa_coord->coordenadax, $empresa_coord->coordenaday
                    );
                    if(count($empresas_ids)>31){
                        break;
                    }
                }
            }
        }

        $data = new Empresa;
        $data = $data
        ->select('empresa.*')
        ->whereIn('id', $empresas_ids)
        ->get();

        $aux_data = [];

        foreach ($data as $empresa_coord){
            
            $empresa_coord->distance = $coordinates_dict['id'.$empresa_coord->id];
            $aux_data[] = $empresa_coord;
        }

        $data = $aux_data;
        for ($i=0; $i < count($data); $i++) {
            $original = $data[$i];
            $min = $data[$i]->distance;
            $swap = $i;

            for ($j=$i; $j < count($data); $j++) { 
                if($min > $data[$j]->distance){
                    $min = $data[$j]->distance;
                    $swap = $j;
                }
            }
            $data[$i] = $data[$swap];
            $data[$swap] = $original;
        }
        
        
        $pagination->pagination->current_page =  1;
        $pagination->pagination->per_page =  32;
        $pagination->pagination->total =  count($empresas_ids);
        $from = 1;
        $pagination->pagination->from =  $from;
        $to = count($empresas_ids);
        $pagination->pagination->to =  $to;
        $pagination->pagination->showing =  $to - $from + 1;
        $pagination->data =  $data;
        
        return response()->json(['data' => $pagination]);
    }

    public function upload(Request $request){
        $id = $request->get('id');
        $empresa = Empresa::find($id);
        if(!$empresa){
            return response()->json(['msg' => 'No se encontró la empresa'], 540);
        }
        $property = $request->file('property');
        $file = $request->file('file');
        $property = $request->get('property');
        $location = base_path().'/../'.$request->get('location');
        $extension = $file->getClientOriginalExtension();
        $save_as = $property.'_'.$id.'.'.$extension;
        $file->move($location, $save_as);
        if($file){
            $empresa->$property = $save_as;
            $empresa->save();
            return response()->json(['saved' => $save_as, 'id'=>$id, 'property'=>$property], 200);
        }
        else{
            return response()->json(['msg' => 'No se pudo subir el archivo'], 540);
        }
    }

    public function distance($lat1, $lon1, $lat2, $lon2) { 
        $pi80 = M_PI / 180; 
        $lat1 *= $pi80; 
        $lon1 *= $pi80; 
        $lat2 *= $pi80; 
        $lon2 *= $pi80; 
        $r = 6372.797;
        $dlat = $lat2 - $lat1; 
        $dlon = $lon2 - $lon1; 
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
        $km = $r * $c; 
        return $km; 
    }

    public function prepareNew() { 
        $clases = Clase::with('grupos')->get();
        return response()->json(['clases' => $clases], 200);
    }

}