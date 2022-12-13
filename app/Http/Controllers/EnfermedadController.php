<?php 

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class EnfermedadController extends Controller{

        public function obtener(){

            $enfermedades = app('db')->table('enfermedad')->select('*')->get();

            return response()->json($enfermedades);

        }

    }

?>