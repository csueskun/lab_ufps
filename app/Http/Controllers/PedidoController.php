<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Pedido;
use App\PedidoDetalle;

class PedidoController extends Controller
{
    public function full(Request $request){
        $pedido = new Pedido;
        $pedido->phone_id = $request->input('phone_id');
        $pedido->empresa_id = $request->input('empresa_id');
        $pedido->estado = $request->input('estado');
        $pedido->total = 0;

        $items = $request->input('items');
        try {
            foreach($items as $item){
                $pedido->total += $item['cantidad'] * $item['precio'];
            }
        } catch (\Throwable $th) {
            
        }
        $pedido->save();

        if(!$pedido){
            return response()->json([], 422);
        }
        foreach($items as $item){
            $detalle = new PedidoDetalle;
            $detalle->pedido_id = $pedido->id;
            $detalle->producto_id = $item['id'];
            $detalle->cantidad = $item['cantidad'];
            $detalle->valorunitario = $item['precio'];
            $detalle->totalparcial = floatval($detalle->cantidad) * floatval($detalle->valorunitario);
            $detalle->save();
        }

        return response()->json([], 200);

    }

    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        $query = Pedido::with('empresa');
        if(!$whereRaw){
            $query = $query->where($params);
        }
        else{
            $whereRaw = str_replace('s3lect', 'select', $whereRaw);
            $query = $query->whereRaw($whereRaw);
        }
        $res = $query->get();
        return response()->json(['data' => $res]);
    }

    public function toConfirm($phone_id){
        $res = Pedido::select('id', 'empresa_id', 'created_at')
        ->with(['empresa'=>function($query){$query->select('empresa.id', 'empresa.nombre');}])
        ->where('phone_id', $phone_id)
        ->where('estado', 0)
        ->get();
        return response()->json(['data' => $res]);
    }

    public function confirm(Request $request){
        $id = $request->input('id');
        $estado = $request->input('estado');
        $res = Pedido::where('id', $id)->update(['estado' => $estado]);
        return response()->json([], $res ? 200: 422);
    }

}