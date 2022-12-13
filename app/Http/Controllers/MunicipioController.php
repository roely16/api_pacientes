<?php 

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class MunicipioController extends Controller{

        public function obtener(){

            $municipios = app('db')->table('municipio')->select('*')->get();

            return response()->json($municipios);

        }

    }

?>