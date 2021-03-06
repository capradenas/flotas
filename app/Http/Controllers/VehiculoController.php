<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App;

class VehiculoController extends Controller
{
    public function index()
    {
        $data =  json_encode(App\Vehiculo::all());
        return view('vehiculos.listado')->with('data', $data);
    }

    public function create()
    {
      $vehiculoViewModel = [];
      $vehiculoViewModel['pestanasVM'] = $this->listarPestanasPlantilla();
      return view('vehiculos.detalle',['data'=>null, 'vm'=>$vehiculoViewModel]);
    }


    public function show(App\Vehiculo $vehiculo)
    {
        $vehiculoViewModel = [];
        $vehiculoViewModel['pestanasVM'] = $this->listarPestanasVehiculo($vehiculo);
        return view('vehiculos.detalle',['data'=>$vehiculo, 'vm'=>$vehiculoViewModel]);
    }

    public function edit(App\Vehiculo $vehiculo)
    {
        $vehiculoViewModel = [];
        $vehiculoViewModel['pestanasVM'] = $this->listarPestanasVehiculo($vehiculo);
        return view('vehiculos.detalle',['data'=>$vehiculo, 'vm'=>$vehiculoViewModel]);
    }

    /*****METODOS REST******/
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vehiculo = new App\Vehiculo;
        $vehiculo->patente = $request->input('patente');
        $vehiculo->marca = $request->input('marca');
        $vehiculo->modelo = $request->input('modelo');
        $vehiculo->anio = $request->input('anio');
        $vehiculo->kilometrajeInicial = $request->input('kilometrajeInicial');
        $vehiculo->capacidadEstanque = $request->input('capacidadEstanque');
        $vehiculo->dueno = $request->input('dueno');
        $vehiculo->tipoVehiculo = $request->input('tipoVehiculo');
        $vehiculo->color = $request->input('color');
        $vehiculo->numeroMotor = $request->input('numeroMotor');
        $vehiculo->numeroChasis = $request->input('numeroChasis');
        $vehiculo->observaciones = $request->input('observaciones');

        $vehiculo->save();


        $atributos = App\VehiculoDatos::where('idVehiculo','-1')->get();
        foreach ($atributos as $atributo) {
            $valor = $request->input('attr_'.$atributo->id);
            $valor = ($valor == null) ? '' : trim($valor);
            $vdat = App\VehiculoDatos::clone($atributo);
            $vdat->idVehiculo = $vehiculo->id;
            $vdat->valor = $valor;
            $vdat->save();
        }

        return ['respuesta'=>true, 'estado'=>'OK','mensaje'=>'Vehiculo '.$vehiculo->patente.' Insertado exitosamente!'];
    }

    public function update()
    {
        //todos funcionalidad de store de vehiculo aqui
    }

    public function destroy()
    {
        //todos funcionalidad de store de vehiculo aqui
    }

    /**********OTROS METODOS***********/

    private function listarPestanasPlantilla()
    {
      $pestanas = App\VehiculoDatos::where('idVehiculo', '-1')
              ->select('pestana')
              ->distinct()
              ->get();
      $ret = Array();
      foreach ($pestanas as $pestana) {
           $res = new PestanaResultType();
           $res->nombre = $pestana->pestana;
           $res->slug = $this::slugify($pestana->pestana);
           $res->titulos = Array();
           $titulos = App\VehiculoDatos::where([['idVehiculo', '=', '-1'],['pestana', '=', $pestana->pestana]])
                                               ->select('titulo')
                                               ->distinct()
                                               ->get();

           foreach ($titulos as $titulo) {
             $tit = new TituloResultType();
             $tit->nombre = $titulo->titulo;
             $tit->slug = $this::slugify($titulo->titulo);
             $tit->atributos = App\VehiculoDatos::where([['idVehiculo', '=', '-1'],['pestana', '=', $pestana->pestana], ['titulo','=',$titulo->titulo]])
                                                ->orderBy('orden')
                                                ->get();
             $res->titulos[] = $tit;
           }
           $ret[] = $res;
      }

      return $ret;
    }


    private function listarPestanasVehiculo($vehiculo)
    {
      $pestanas = App\VehiculoDatos::where('idVehiculo', $vehiculo->id)
              ->select('pestana')
              ->distinct()
              ->get();
      $ret = Array();
      foreach ($pestanas as $pestana) {
           $res = new PestanaResultType();
           $res->nombre = $pestana->pestana;
           $res->slug = $this::slugify($pestana->pestana);
           $res->titulos = Array();
           $titulos = App\VehiculoDatos::where([['idVehiculo', '=', $vehiculo->id],['pestana', '=', $pestana->pestana]])
                                               ->select('titulo')
                                               ->distinct()
                                               ->get();

           foreach ($titulos as $titulo) {
             $tit = new TituloResultType();
             $tit->nombre = $titulo->titulo;
             $tit->slug = $this::slugify($titulo->titulo);
             $tit->atributos = App\VehiculoDatos::where([['idVehiculo', '=', $vehiculo->id],['pestana', '=', $pestana->pestana], ['titulo','=',$titulo->titulo]])
                                                ->orderBy('orden')
                                                ->get();
             $res->titulos[] = $tit;
           }
           $ret[] = $res;
      }

      return $ret;
    }

    private function debugf($object)
    {
      echo "<pre>";
        var_dump($object);
      echo "</pre>";
    }

    private function slugify($text)
    {
      // replace non letter or digits by -
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      // trim
      $text = trim($text, '-');

      // remove duplicate -
      $text = preg_replace('~-+~', '-', $text);

      // lowercase
      $text = strtolower($text);

      if (empty($text)) {
        return 'n-a';
      }

      return $text;
    }
}

class PestanaResultType {
  public $nombre;
  public $slug;
  public $titulos;
}

class TituloResultType {
  public $nombre;
  public $slug;
  public $atributos;
}
