<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{

    public function index()
    {
        $title="Login";
        return view('login.index',compact('title'));
    }

    public function loginact(Request $request)  {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            alert('Gagal', $validator->messages());
            return redirect()->back()->withInput();
        }

        $credentials =[
            "email" => $request->email,
            "password" => $request->password
        ];
        try {
            
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
    Alert::success('Success', 'Login Berhasil di lakukan')->flash();
                return redirect()->intended('dashboard');
    
            }else {
                Alert::error('Gagal',"email atau password salah");
            }
        } catch (\Throwable $th) {
            //throw $th;
            alert()->error('Gagal',$th->getMessage());
            return back();
        //     alert()->error('Gagal',"nis/nip atau password salah");
        // return back();
        }   
    }

    public function register()  {
        $title= "registrasi";
        return view('register.index',compact('title'));
    }

    public function registeract(Request $request) {

        $validator= Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'alamat'=>'required|string|max:255',
            'no_hp'=>'required|numeric',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            Alert::error($messages)->flash();
            return back()->withErrors($validator)->withInput();
        }
        DB::beginTransaction();
        try {
            //code...
         
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role="pelanggan";
            $user->save();
            $pelanggan=new Pelanggan();
            $pelanggan->id_user=$user->id;
            $pelanggan->name=$request->name;
            $pelanggan->alamat=$request->alamat;
            $pelanggan->no_hp=$request->no_hp;
            $pelanggan->save();

            Alert::success('Success', 'Berhasil Registrasi')->flash();
            DB::commit();
            return redirect()->route('login');



            // DB::commit();
        } catch (\Exception $th) {
            DB::rollBack();
            Alert::error('Error', $th->getMessage())->flash();
            return back()->withErrors($validator)->withInput();


        }
      
        
    }
}
