<?php

namespace App\Http\Controllers;

use App\Models\{Rank};

use Illuminate\Http\{Request, Response, UploadedFile};
use Illuminate\Support\Facades\{Http, Route, Session, Hash, Artisan, Cookie};
use Illuminate\Support\Carbon;
use App\Http\Controllers\{Controller, ApiController};
use Tymon\JWTAuth\Facades\JWTAuth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{Pangkat};

class RankController extends Controller
{ 
    public function listpangkat(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'masterdata';
            $request['url_active'] = 'listpangkat';

            $menu = 'masterdata';
            $action = 'listpangkat';
            $viewpath = 'admin.AdminOne.masterdata.listdata.pangkat';

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
            Log::error('listpangkat Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function datalistpangkat(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'masterdata';
            $request['url_active'] = 'listpangkat';

            $menu = 'masterdata';
            $action = 'listpangkat';

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

            $response = app('App\Services\ApiRank')->listpangkat($request);            
            $results = is_array($response) ? $response : $response->getData(true); 

            return $results;
            
        } catch (Throwable $e) {
            Log::error('datalistpangkat Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function savepangkat(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'listpangkat';
            $action = 'newpangkat';

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
                'pangkat'   => 'required|string|max:200',
                'golongan'  => 'required|string|max:200',
                'ruang'     => 'required|string|max:200',
            ]);

            $response = app('App\Services\ApiRank')->savepangkat($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 201 : 422);

        } catch (Throwable $e) {
            Log::error('savepangkat Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewpangkat(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'listpangkat';

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

            $response = app('App\Services\ApiRank')->viewpangkat($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['pangkat'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailpangkat Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updatepangkat(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'listpangkat';
            $action = 'editpangkat';

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
                'code_data' => 'required',
                'pangkat'   => 'required|string|max:200',
                'golongan'  => 'required|string|max:200',
                'ruang'     => 'required|string|max:200',
            ]);

            $response = app('App\Services\ApiRank')->editpangkat($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updatepangkat Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function statuspangkat(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu = 'listpangkat';
            $action = 'editpangkat';

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

            $response = app('App\Services\ApiRank')->upstatuspangkat($request);
            $results = is_array($response) ? $response : $response->getData(true);

            return response()->json($results);
            
        } catch (Throwable $e) {
            Log::error('statuspangkat Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function deletepangkat(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu = 'listpangkat';
            $action = 'deletepangkat';

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

            $response = app('App\Services\ApiRank')->deletepangkat($request);
            $results = is_array($response) ? $response : $response->getData(true);

            return response()->json($results);
            
        } catch (Throwable $e) {
            Log::error('deletepangkat Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function exportpangkat(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'masterdata';
            $request['url_active'] = 'listpangkat';

            $menu = 'listpangkat';
            $action = 'exportpangkat';

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

            $fileName = "Data-Pangkat-".date('Y-m-d-His').".xls" ;
            Excel::store(new Pangkat($request),'exports/' . $fileName,'public');
            return response()->json(['success' => true,'download_url' => url('/admin/download-exportdata/' . $fileName)]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage() ], 500);
        }
    }
}