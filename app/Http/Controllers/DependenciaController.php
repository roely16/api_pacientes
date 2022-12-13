<?php 

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class DependenciaController extends Controller{

        public function obtener(){

            $dependencias = app('db')->table('dependencia')->select('*')->get();

            return response()->json($dependencias);

        }

        public function obtener_dependencias_datos(){

            $dependencias =     app('db')
                                ->table('dependencia')
                                ->join('persona', 'dependencia.id', '=', 'persona.id_dependencia')
                                ->select(app('db')->raw('distinct(dependencia.id) as id, dependencia.descripcion'))
                                ->get();

            return response()->json($dependencias);

        }

    }

?>