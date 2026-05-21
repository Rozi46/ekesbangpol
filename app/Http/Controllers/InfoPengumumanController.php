<?php

namespace App\Http\Controllers;

use App\Models\{Post};

use Illuminate\Http\{Request, Response, UploadedFile};
use Illuminate\Support\Facades\{Http, Route, Session, Hash, Artisan, Cookie};
use Illuminate\Support\Carbon;
use App\Http\Controllers\{Controller, ApiController};
use Tymon\JWTAuth\Facades\JWTAuth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{DataInfoPengumuman};

class InfoPengumumanController extends Controller
{ 
    public function listinfopengumuman(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'datawebsite';
            $request['url_active'] = 'datainfopengumuman';

            $menu = 'datawebsite';
            $action = 'datainfopengumuman';
            $viewpath = 'admin.AdminOne.datawebsite.listdata.infopengumuman';

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
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if ( ($level_user[$request['app']] ?? 'No') === 'No' || ($level_user[$request['url_active']] ?? 'No') === 'No' || ($level_user[$menu] ?? 'No') === 'No' || ($level_user[$action] ?? 'No') === 'No' ) { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }

            $vd = intval($request->vd ?? 20);            
            $request['vd'] = $vd;

            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results'],'count_vd' => $vd,'keysearch' => $request->keysearch]);
        } catch (Throwable $e) {
            Log::error('listinfopengumuman Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function datalistinfopengumuman(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'datawebsite';
            $request['url_active'] = 'datainfopengumuman';

            $menu = 'datawebsite';
            $action = 'datainfopengumuman';

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
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if ( ($level_user[$request['app']] ?? 'No') === 'No' || ($level_user[$request['url_active']] ?? 'No') === 'No' || ($level_user[$menu] ?? 'No') === 'No' || ($level_user[$action] ?? 'No') === 'No' ) { return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }

            $response = app('App\Services\ApiInfoPengumuman')->listinfopengumuman($request);            
            $results = is_array($response) ? $response : $response->getData(true); 

            return $results;
            
        } catch (Throwable $e) {
            Log::error('datalistinfopengumuman Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function saveinfopengumuman(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datainfopengumuman';
            $action = 'newinfopengumuman';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user     = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {
                return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan'], 500);
            }

            $resultsUser             = $get_user['results'][0];
            $res_level_user          = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $level_user              = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($level_user[$menu] ?? 'No') === 'No' || ($level_user[$action] ?? 'No') === 'No') {
                return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses'], 403);
            }

            $request->validate([
                'judul'  => 'required|string|max:200',
                'isi'    => 'required|string',
                'sumber' => 'required|string|max:200',
                'photo'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $response = app('App\Services\ApiInfoPengumuman')->saveinfopengumuman($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 201 : 422);

        } catch (Throwable $e) {
            Log::error('saveinfopengumuman Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewinfopengumuman(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datainfopengumuman';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user     = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {
                return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan'], 500);
            }

            $resultsUser    = $get_user['results'][0];
            $res_level_user = $resultsUser['leveladmin'][0];
            $level_user     = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($level_user[$menu] ?? 'No') === 'No') {
                return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses'], 403);
            }

            $request->validate(['code_data' => 'required']);

            $response = app('App\Services\ApiInfoPengumuman')->viewinfopengumuman($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['post'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailinfopengumuman Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updateinfopengumuman(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datainfopengumuman';
            $action = 'editinfopengumuman';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user     = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {
                return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan'], 500);
            }

            $resultsUser             = $get_user['results'][0];
            $res_level_user          = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $level_user              = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($level_user[$menu] ?? 'No') === 'No' || ($level_user[$action] ?? 'No') === 'No') {
                return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses'], 403);
            }

            $response = app('App\Services\ApiInfoPengumuman')->editinfopengumuman($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updateinfopengumuman Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function statusinfopengumuman(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu = 'datainfopengumuman';
            $action = 'editinfopengumuman';

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
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if ( ($level_user[$menu] ?? 'No') === 'No' || ($level_user[$action] ?? 'No') === 'No' ) { return redirect('/admin/dash')->with('error', 'Tidak ada akses');} 

            $request->validate([
                'code_data' => 'required',
                'status' => 'required|in:Aktif,Tidak Aktif'
            ]);

            $response = app('App\Services\ApiInfoPengumuman')->upstatusinfopengumuman($request);
            $results = is_array($response) ? $response : $response->getData(true);

            return response()->json($results);
            
        } catch (Throwable $e) {
            Log::error('statusinfopengumuman Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function deleteinfopengumuman(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu = 'datainfopengumuman';
            $action = 'deleteinfopengumuman';

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
            $list_akses = is_array($responseLevelakses) ? $responseLevelakses : $responseLevelakses->getData(true);

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if ( ($level_user[$menu] ?? 'No') === 'No' || ($level_user[$action] ?? 'No') === 'No' ) { return redirect('/admin/dash')->with('error', 'Tidak ada akses');} 

            $request->validate([
                'code_data' => 'required'
            ]);

            $response = app('App\Services\ApiInfoPengumuman')->deleteinfopengumuman($request);
            $results = is_array($response) ? $response : $response->getData(true);

            return response()->json($results);
            
        } catch (Throwable $e) {
            Log::error('deleteinfopengumuman Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function exportinfopengumuman(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'datawebsite';
            $request['url_active'] = 'datainfopengumuman';

            $menu = 'datainfopengumuman';
            $action = 'exportinfopengumuman';

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

            $level_user = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if ( ($level_user[$request['app']] ?? 'No') === 'No' || ($level_user[$request['url_active']] ?? 'No') === 'No' || ($level_user[$menu] ?? 'No') === 'No' || ($level_user[$action] ?? 'No') === 'No' ) {  return redirect('/admin/dash')->with('error', 'Tidak ada akses'); }
                
            $fileName = "Data-InfoPengumuman-".date('Y-m-d-His').".xls" ;
            Excel::store(new DataInfoPengumuman($request),'exports/' . $fileName,'public');
            return response()->json(['success' => true,'download_url' => url('/admin/download-exportdata/' . $fileName)]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage() ], 500);
        }
    }  
}