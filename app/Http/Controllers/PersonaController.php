<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class PersonaController extends Controller
{

    public function store(Request $request){

        $now = date('Y-m-d H:i:s');

         if (!$request->fecha_nacimiento) {
            
            $request->profesion = NULL;
        }

        if (!$request->profesion) {
            
            $request->profesion = NULL;
        }

        $result = app('db')->table('persona')->insertGetId([
            "nombre" => "$request->nombre",
            "apellido" => "$request->apellido",
            "apellido_casada" => "$request->apellido_casada",
            "estado_civil" => "$request->estado_civil",
            "genero" => "$request->genero",
            "fecha_nacimiento" => "$request->fecha_nacimiento",
            "no_dpi" => "$request->no_dpi",
            "direccion" => "$request->direccion",
            "no_casa" => "$request->no_casa",
            "zona" => "$request->zona",
            "colonia" => "$request->colonia",
            "habitantes" => "$request->habitantes",
            "referido_por" => "$request->referido_por",
            "profesion" => $request->profesion,
            "status" => "$request->status",
            "fecha_registro" => "$now",
            "clasificacion" => $request->clasificacion,
            "id_tipo" => $request->id_tipo,
            "rango" => $request->rango,
            "mes_cumple" => $request->mes_cumple,
            "observaciones" => $request->observaciones,
            "usuario_registro" => $request->usuario_registro,
            "id_dependencia" => $request->id_dependencia["id"],
            "id_municipio" => $request->id_municipio["id"],
            "otras_enfermedades" => "$request->otras_enfermedades"
        ]);

        // Insertar las enfermedades

        if($request->enfermedades){

            foreach($request->enfermedades as $enfermedad){

                $result_ = app('db')->table('persona_enfermedad')->insert([
                    "id_persona" => $result,
                    "id_enfermedad" => $enfermedad["id"] 
                ]);
    
            }

        }

        return response()->json($result);

    }

    public function index($id_usuario){

        $zonas =    app('db')
                    ->table('usuario_zona')
                    ->select('zona')
                    ->where('id_usuario', $id_usuario)
                    ->get();

        $array_zonas = [];

        foreach ($zonas as $zona) {
            
            $array_zonas [] = $zona->zona;

        }

        $result =   app('db')
                    ->table('persona')
                    ->leftJoin('clasificacion_contacto', 'persona.clasificacion', '=', 'clasificacion_contacto.id')
                    ->join('dependencia', 'persona.id_dependencia', '=', 'dependencia.id')
                    ->orderByRaw('id DESC, zona ASC')
                    ->select(
                        'persona.id', 
                        'persona.nombre', 
                        'persona.apellido', 
                        'persona.direccion', 
                        'persona.fecha_nacimiento',
                        'persona.zona',
                        'persona.id_dependencia',
                        'persona.no_dpi',
                        'persona.status',
                        app('db')->raw('dependencia.descripcion as dependencia'),
                        app('db')->raw('clasificacion_contacto.nombre as nombre_clasificacion'),
                        'clasificacion_contacto.color',
                        app('db')->raw('concat(persona.nombre, concat(" ", persona.apellido)) as nombre_completo')
                    )
                    ->whereIn('persona.zona', $array_zonas)
                    ->get();

        $fields = [
            [
                'key' => 'id',
                'label' => 'ID',
                'sortable' => true
            ],
            [
                'key' => 'nombre',
                'label' => 'Nombre',
            ],
            [
                'key' => 'apellido',
                'label' => 'Apellido',
            ],
            [
                'key' => 'no_dpi',
                'label' => 'DPI',
            ],
            [
                'key' => 'dependencia',
                'label' => 'Tipo'
            ],
            [
                'key' => 'estado',
                'label' => 'Estado',
            ],
            [
                'key' => 'zona',
                'label' => 'Zona',
            ],
            [
                'key' => 'action',
                'label' => 'Acción',
                'class' => 'text-right'
            ]
        ];

        return response()->json(
            ['items' => $result, 'fields' => $fields]
        );

    }

    public function show($id){

        $result = app('db')->table('persona')->select("*")->where('id', $id)->first();

        $clasificacion_persona =  app('db')->table('clasificacion_contacto')->select('*')->where('id', $result->clasificacion)->first();

        $clasificacion = app('db')->table('clasificacion_contacto')->select('*')->get();

        // Dependencia
        $dependencia = app('db')->table('dependencia')->select('*')->where('id', $result->id_dependencia)->first();

        $result->id_dependencia = $dependencia;

        // Municipio
        $municipio = app('db')->table('municipio')->select('*')->where('id', $result->id_municipio)->first();

        $result->id_municipio = $municipio;

        // Enfermedades
        $enfermedades =     app('db')
                            ->table('persona_enfermedad')
                            ->join('enfermedad', 'persona_enfermedad.id_enfermedad', '=', 'enfermedad.id')
                            ->select('enfermedad.*')
                            ->where('persona_enfermedad.id_persona', $id)
                            ->get();

        $result->enfermedades = $enfermedades;

        return response()->json([
            "detalle" => $result,
            "clasificacion" => $clasificacion
        ]);

    }

    public function delete($id){

        $result = app('db')->delete("delete from persona where id = $id");

        return $result;

    }

    public function update(Request $request){

        if (!$request->fecha_nacimiento) {
            
            $request->fecha_nacimiento = NULL;
        }

        if (!$request->profesion) {
            
            $request->profesion = NULL;
        }

        $result = app('db')->table('persona')->where('id', $request->id)->update([
            "nombre" => "$request->nombre",
            "apellido" => "$request->apellido",
            "apellido_casada" => "$request->apellido_casada",
            "estado_civil" => "$request->estado_civil",
            "genero" => "$request->genero",
            "fecha_nacimiento" => $request->fecha_nacimiento,
            "no_dpi" => "$request->no_dpi",
            "direccion" => "$request->direccion",
            "no_casa" => "$request->no_casa",
            "zona" => "$request->zona",
            "colonia" => "$request->colonia",
            "habitantes" => "$request->habitantes",
            "referido_por" => "$request->referido_por",
            "profesion" => "$request->profesion",
            "status" => "$request->status",
            "clasificacion" => $request->clasificacion,
            "id_tipo" => $request->id_tipo,
            "rango" => $request->rango,
            "mes_cumple" => $request->mes_cumple,
            "observaciones" => $request->observaciones,
            "id_dependencia" => $request->id_dependencia["id"],
            "id_municipio" => $request->id_municipio["id"],
            "tipo_actividad" => "$request->tipo_actividad"
        ]);

        // Actualizar las enfermedades
        $result = app('db')->table('persona_enfermedad')->where('id_persona', $request->id)->delete();

        if($request->enfermedades){

            foreach($request->enfermedades as $enfermedad){

                $result_ = app('db')->table('persona_enfermedad')->insert([
                    "id_persona" => $request->id,
                    "id_enfermedad" => $enfermedad["id"] 
                ]);
    
            }

        }

        return response()->json($result);

    }

    public function profesiones(){

        $result = app('db')->select('select id as value, nombre as text from profesion');

        return response()->json($result);

    }

    public function vias_contacto(){

        $result = app('db')->select('select id as value, nombre as text from via_contacto');

        return response()->json($result);

    }

    //
}
