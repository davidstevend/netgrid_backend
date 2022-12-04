<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    protected $user;

    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');
        if($token != '')
            //En caso de que requiera autentifiación la ruta obtenemos el usuario y 
            //lo almacenamos en una variable, nosotros no lo utilizaremos.
            $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Listamos todos los productos
        return Favorite::get();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validamos los datos
        $data = $request->only('ref_api');
        $validator = Validator::make($data, [
            'ref_api' => 'required|max:250|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Creamos el producto en la BD
        $favorite = Favorite::create([
            'id_usuario' =>  $this->user->id,
            'ref_api' => $request->ref_api
            
        ]);
        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'message' => 'Favorito Agregado Con Exito!!!',
            'data' => $favorite
        ], Response::HTTP_OK);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($ref_api)
    {
        //Bucamos el producto
        $favorite = Favorite::where('ref_api',$ref_api);
        //Si el producto no existe devolvemos error no encontrado
        if (!$favorite) {
            return response()->json([
                'message' => 'Favorito no existe!!!'
            ], 404);
        }
        //Si hay producto lo devolvemos
        return $favorite;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
 
    public function destroy($id)
    {
        //Buscamos el producto
        $favorite = Favorite::findOrfail($id);
        //Eliminamos el producto
        $favorite->delete();
        //Devolvemos la respuesta
        return response()->json([
            'message' => 'Ya no es Favorito!'
        ], Response::HTTP_OK);
    }
}
