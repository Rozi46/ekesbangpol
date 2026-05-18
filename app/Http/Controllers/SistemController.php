<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, Response, UploadedFile};
use Illuminate\Support\Facades\{Http, Route, Session, Hash, Artisan, Cookie};
use Illuminate\Support\Carbon;
use App\Http\Controllers\{Controller, ApiController};
use Tymon\JWTAuth\Facades\JWTAuth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{DataPengguna, AktivitasPengguna, Atlet, Club, Kategori, KelompokUmur, Championship, Event, Register};

class SistemController extends Controller
{
    public function formlogin(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $url_api =  env('APP_API');

        if(!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol')){            
            return view('admin.AdminOne.login',['url' => '/admin/login']);
        }else{
            return redirect('/admin/dash');
        }
    }

    public function login(Request $request)
    {   
        date_default_timezone_set('Asia/Jakarta');
        $url_api =  env('APP_API');
        $url_app =  env('ART_APP');
        $email = $request->email;
        $password = $request->password;

        $this->validate($request, [
            'email' => 'required|min:1|max:200',
            'password' => 'required|min:1|max:200',
        ]);

        $response[] = app('App\Http\Controllers\ApiController')->login($request);  
        $response = collect($response)->toJson();
        $response = json_decode($response,true);
        $response = $response[0]['original'];
		
		$status = $response['status_message'];
		$note = $response['note'];
        $results = $response['results'];

        if($status == 'success'){
        	$detailadmin = $results[0]['detailadmin'][0];
            
            if($detailadmin['level']=='LV7622003'){
                Session::put('key_token_kesbangpol_cash',$response['key_token']);
                Session::put('admin_login_kesbangpol_cash',$detailadmin['id']);    
                $this->backup_database();    
                return redirect('/cash/dash');
            }else{
                Session::put('key_token_kesbangpol',$response['key_token']);
                Session::put('admin_login_kesbangpol',$detailadmin['id']);
                $this->backup_database();
                return redirect('/admin/dash');
            }
        }else{
        	return redirect('/admin/administration')->with('error',$note);
        }
    }

    public function logout(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $url_api =  env('APP_API');
    	$admin_login = session('admin_login_kesbangpol');
    	$key_token = session('key_token_kesbangpol');
        $request['u'] = $admin_login;
        $request['token'] = $key_token;

        $response[] = app('App\Http\Controllers\ApiController')->logout($request);  
        $response = collect($response)->toJson();
        $response = json_decode($response,true);
        $response = $response[0]['original'];

        Session::forget('key_token_kesbangpol');
        Session::forget('admin_login_kesbangpol');

        Cookie::queue(Cookie::forget('key_token_kesbangpol'));

        $this->backup_database();

        return redirect('/admin/login');
    }

    public function dash(Request $request)
    {
    	if(!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol')){
    		return redirect('/admin/logout')->with('error','Terjadi kesalahan!!! silahkan hubungi kami');
    	}else{
            date_default_timezone_set('Asia/Jakarta');
            $url_api =  env('APP_API');
            $admin_login = session('admin_login_kesbangpol');
            $key_token = session('key_token_kesbangpol');
            $load_app = $request->load;
            $request['u'] = $admin_login;
            $request['token'] = $key_token;
            $request['app'] = 'dash';
            $request['url_active'] = 'dash';
            $viewpath = 'admin.AdminOne.home';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Terjadi kesalahan!!! silahkan hubungi kami');}

            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $get_levelakses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            $vd = intval($request->vd ?? 20);
            
            $request['vd'] = $vd;  
            
            $response = app('App\Http\Controllers\ApiController')->getdash($request);
            $results = is_array($response) ? $response : $response->getData(true); 
            
            return view($viewpath,['url_api' => $url_api,'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $get_levelakses['results'],'count_vd' => $vd,'listdata' => $results['results'] ]);
        }
    }

}
