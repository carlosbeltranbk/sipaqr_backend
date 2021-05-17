<?php

namespace App\Http\Controllers;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{    
    public function index()
    {
        return User::all();
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);        $user = new User([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);        
        $user->save();       
         return response()->json([
            'message' => 'Registrado! Has creado tu cuenta correctamente.'], 201);
    }    
    
    
    public function login(Request $request)
    {
        $email = $request->input('usuario');
        $password = $request->input('clave');

        $correo = DB::table('usuarios')->where('usuario',$email)->first();
        $contra = DB::table('usuarios')->where('clave',$password)->first();

        if(!($correo && $contra)){
            return response()->json(['errors'=>array(['code'=>404,'message'=>'Usuario/Contraseña incorrecto.'])],404);
            
        }
        if($correo){
            return DB::select('call login(?,?)',array($email,$password));
        }
        
        // $user = $request->user();
        // $tokenResult = $user->createToken('Personal Access Token');
        // $token = $tokenResult->token;        
        
        // if ($request->remember_me) {
        //     $token->expires_at = Carbon::now()->addWeeks(1);
        // }        $token->save();        
        
        // return response()->json([
        //     'access_token' => $tokenResult->accessToken,
        //     'token_type'   => 'Bearer',
        //     'expires_at'   => Carbon::parse(
        //         $tokenResult->token->expires_at)
        //             ->toDateTimeString(),
        // ]);
    }


     public function logout(Request $request)
     {
          Auth::logout();     
          return redirect('/login');
     }



    // public function user(User $user)
    // {
    //    // return $diaFeriado;
    //    $user=User::find($user);

	// 	// Si no existe ese fabricante devolvemos un error.
	// 	if (! $user)
	// 	{
	// 		// Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
	// 		// En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
	// 		return response()->json(['errors'=>array(['code'=>404,'message'=>'Registro no encontrado.'])],404);
	// 	}else{
    //         return response()->json(['status'=>'Registro modificado correctamente.','data'=>$user],200);

    //     }
    // }
}