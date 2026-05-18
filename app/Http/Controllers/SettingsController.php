<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, Response, UploadedFile};
use Illuminate\Support\Facades\{Http, Route, Session, Hash, Artisan, Cookie};
use Illuminate\Support\{Carbon, Str};
use App\Http\Controllers\{Controller, ApiController};
use Tymon\JWTAuth\Facades\JWTAuth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{DataPengguna, AktivitasPengguna};

class SettingsController extends Controller
{ 
    // Pengaturan Menu & Akes
    public function settingmenu(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'setting';
            $request['url_active'] = 'settingmenu';

            $viewpath = 'admin.AdminOne.pengaturan.settingmenu';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }
                    
            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results']]); 
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function actionsettingmenu(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }

            $this->validate($request, [
                'no_urut' => 'required|string',
                'nama_menu' => 'required|string|max:200',
                'nama_akses' => 'required|min:1|max:200',
            ]);
            
            $response = app('App\Services\ApiSettings')->actionsettingmenu($request);            
            $results = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note = $results['note'] ?? 'Terjadi kesalahan'; 
            $message = data_get($results, 'note.nama_akses.0', $note);

            $redirectUrl = $status === 'success' ? '/admin/settingmenu' : '/admin/settingmenu';
            return redirect($redirectUrl)->with($status, $message);
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }  

    public function delmenu(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }

            $request['id'] = $request['d'];
            
            $response = app('App\Services\ApiSettings')->delmenu($request);            
            $results = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note = $results['note'] ?? 'Terjadi kesalahan'; 
            
            return redirect('/admin/settingmenu')->with($status, $note);
            
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    } 
    
    // Company
    public function listcompany(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'setting';
            $request['url_active'] = 'listcompany';

            $viewpath = 'admin.AdminOne.pengaturan.listcompany';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }

            $vd = intval($request->vd ?? 20);
            
            $request['vd'] = $vd;
                
            $response = app('App\Services\ApiSettings')->listcompany($request);            
            $results = is_array($response) ? $response : $response->getData(true); 

            if($results['note'] == 'Tidak ada akses'){return redirect('/admin/dash')->with('error','Tidak ada akses');}

            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results'],'count_vd' => $vd,'keysearch' => $request->keysearch,'results' => $results['results']['listdata'],'listdata' => $results['results']]);
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function newcompany(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'setting';
            $request['url_active'] = 'listcompany';

            $viewpath = 'admin.AdminOne.pengaturan.newcompany';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }
                    
            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results']]); 
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function savecompany(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }

            $this->validate($request, [
                'nama'   => 'required|string|max:200',
                'jenis'  => 'required|string|max:200',
                'alamat' => 'required|string|max:200',
                'email'  => 'required|email|max:200',
            ]); 

            $response = app('App\Services\ApiSettings')->savecompany($request);            
            $results = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note = $results['note'] ?? 'Terjadi kesalahan'; 
            $message = data_get($results, 'note.nama.0', $note);

            $redirectUrl = $status === 'success' ? '/admin/listcompany' : '/admin/newcompany';
            return redirect($redirectUrl)->with($status, $message);
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function viewcompany(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'setting';
            $request['url_active'] = 'listcompany';

            $viewpath = 'admin.AdminOne.pengaturan.editcompany';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();       

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }  

            $request['id'] = $request['d'];
                
            $response = app('App\Services\ApiSettings')->viewcompany($request);            
            $results = is_array($response) ? $response : $response->getData(true);

            if($results['note'] == 'Data tidak ditemukan'){return redirect('/admin/dash')->with('error','Data tidak ditemukan');}

            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results'],'results' => $results]);
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function editcompany(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); } 

            $this->validate($request, [
                'code_company'  => 'required|string|max:100',
                'nama_company'  => 'required|string|max:100',
                'jenis_company' => 'required|string|max:100',
                'alamat_company'=> 'required|string|max:100',
                'email_company' => 'required|string|email|max:200',
            ]);
                
            $response = app('App\Services\ApiSettings')->editcompany($request);            
            $results = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note = $results['note'] ?? 'Terjadi kesalahan';

            $redirectUrl = $status === 'success' ? '/admin/listcompany' : '/admin/editcompany?d='.$request->id_data;
            return redirect($redirectUrl)->with($status, $note);
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function deletecompany(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }
            
            $request['id'] = $request['d'];
                
            $response = app('App\Services\ApiSettings')->deletecompany($request);            
            $results = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note = $results['note'] ?? 'Terjadi kesalahan';

            return redirect('/admin/listcompany')->with($status,$note);
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    // Manual Book
    public function manualbook(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'setting';
            $request['url_active'] = 'manualbook';

            $viewpath = 'admin.AdminOne.pengaturan.manualbook';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }
                
            $response = app('App\Services\ApiSettings')->viewManualBook($request);            
            $results = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return redirect()->route('admin.dash')->with('error', 'Data tidak ditemukan');
            }
                    
            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results'], 'results' => $results]); 
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function uploadmanualbook(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'setting';
            $request['url_active'] = 'manualbook';

            $viewpath = 'admin.AdminOne.manualbook.tempmanualbook';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }

            $response = app('App\Services\ApiSettings')->uploadmanualbook($request);            
            $results = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note = $results['note'] ?? 'Terjadi kesalahan'; 

            $redirectUrl = $status === 'success' ? '/admin/manualbook?d='.$results['results'] : '/admin/manualbook';
            return redirect($redirectUrl)->with($status, $note);
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function viewmanualbook(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'setting';
            $request['url_active'] = 'manualbook';

            $viewpath = 'admin.AdminOne.manualbook.tempmanualbook';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($res_user['level'] ?? null) !== 'LV5677001') { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }

            $request['tipe_page'] = 'full';
            $request['file_manualbook'] = $request['d'];
            $request['title_manualbook'] = 'Manual Book';
            
            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results']]);
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function downloadmanualbook(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {return redirect('/admin/logout')->with('error', 'Data user tidak valid');}
            $resultsUser = $get_user['results'][0];
            $res_user = $resultsUser['detailadmin'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $request['nama_admin'] = \Str::limit($res_user['full_name'], 15, '...');

            $responseSetting = app('App\Services\ApiSettings')->getsetting($request);
            $get_setting = is_array($responseSetting) ? $responseSetting : $responseSetting->getData(true);
            $request['manual_book'] = $get_setting['results']['data_setting']['manual_book'] ?? null;

            $responseLevelakses = app('App\Services\ApiSettings')->getlevelakses($request);
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            return app('App\Services\ApiSettings')->downloadmanualbook($request);
        } catch (Throwable $e) {
            Log::error('SettingMenu Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}