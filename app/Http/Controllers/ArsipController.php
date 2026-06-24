<?php

namespace App\Http\Controllers;

use App\Models\{Arsip, ArsipLog, ArsipTag, ArsipTagRelasi};

use Illuminate\Http\{Request, Response, UploadedFile};
use Illuminate\Support\Facades\{Http, Route, Session, Hash, Artisan, Cookie};
use Illuminate\Support\Carbon;
use App\Http\Controllers\{Controller, ApiController};
use Tymon\JWTAuth\Facades\JWTAuth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{KategoriArsip, DataArsipTags, DataKlasifikasiArsip, DataArsip};

class ArsipController extends Controller
{
    // isi select
        public function listopkategoriarsip(Request $request)
        {
            try { 
                if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                    empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                    return redirect('/admin/logout')->with('error', 'Session tidak valid');
                }

                date_default_timezone_set('Asia/Jakarta');

                $request['u'] = session('admin_login_kesbangpol');
                $request['token'] = session('key_token_kesbangpol');
                $request['app'] = 'earsip';
                $request['url_active'] = 'dataarsip';

                $menu = 'dataarsip';
                $action = 'newarsip';

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

                $response = app('App\Services\ApiArsip')->listopkategoriarsip($request);            
                $results = is_array($response) ? $response : $response->getData(true); 

                return $results;
                
            } catch (Throwable $e) {
                Log::error('datalistarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
                return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
            }        
        }
    // end isi select

    // Arsip
    public function listarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'dataarsip';

            $menu = 'earsip';
            $action = 'dataarsip';
            $viewpath = 'admin.AdminOne.earsip.listdata.dataarsip';

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
            Log::error('dataarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function datalistarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'dataarsip';

            $menu = 'earsip';
            $action = 'dataarsip';

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

            $response = app('App\Services\ApiArsip')->dataarsip($request);            
            $results = is_array($response) ? $response : $response->getData(true); 

            return $results;
            
        } catch (Throwable $e) {
            Log::error('dataarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function savearsip(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'dataarsip';
            $action = 'newarsip';

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
                'code_kategori'     => 'required|string|max:200',
                'judul'             => 'required|string|max:200',
                'tanggal_dokumen'   => 'required|string|max:200',
                'file_path'         => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:5120', // 5 MB
                'deskripsi'         => 'required|string|max:200',
                'akses'             => 'required|string|max:200',
            ]);

            $response = app('App\Services\ApiArsip')->savearsip($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 201 : 422);

        } catch (Throwable $e) {
            Log::error('savearsipp Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewarsip(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'dataarsip';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user     = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {
                return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan'], 500);
            }

            $resultsUser             = $get_user['results'][0];
            $res_level_user          = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $level_user              = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($level_user[$menu] ?? 'No') === 'No') {
                return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses'], 403);
            }

            $request->validate(['code_data' => 'required']);

            $response = app('App\Services\ApiArsip')->viewarsip($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['arsip'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updatearsip(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'dataarsip';
            $action = 'editarsip';

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
                'code_kategori'     => 'required|string|max:200',
                'judul'             => 'required|string|max:200',
                'tanggal_dokumen'   => 'required|string|max:200',
                'file_path'         => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:5120', // 5 MB
                'deskripsi'         => 'required|string|max:200',
                'akses'             => 'required|string|max:200',
            ]);

            $response = app('App\Services\ApiArsip')->editarsip($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updatearsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function deletearsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu = 'dataarsip';
            $action = 'deletearsip';

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

            $response = app('App\Services\ApiArsip')->deletearsip($request);
            $results = is_array($response) ? $response : $response->getData(true);

            return response()->json($results);
            
        } catch (Throwable $e) {
            Log::error('deletearsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function exportarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'dataarsip';

            $menu = 'dataarsip';
            $action = 'exportarsip';

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
                
            $fileName = "Data-Arsip-".date('Y-m-d-His').".xls" ;
            Excel::store(new DataArsip($request),'exports/' . $fileName,'public');
            return response()->json(['success' => true,'download_url' => url('/admin/download-exportdata/' . $fileName)]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage() ], 500);
        }
    } 

    // KategoriArsip
    public function listkategoriarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'kategoriarsip';

            $menu = 'earsip';
            $action = 'kategoriarsip';
            $viewpath = 'admin.AdminOne.earsip.listdata.kategoriarsip';

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
            Log::error('kategoriarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function datalistkategoriarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'kategoriarsip';

            $menu = 'earsip';
            $action = 'kategoriarsip';

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

            $response = app('App\Services\ApiArsip')->kategoriarsip($request);            
            $results = is_array($response) ? $response : $response->getData(true); 

            return $results;
            
        } catch (Throwable $e) {
            Log::error('datakategoriarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function savekategoriarsip(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'kategoriarsip';
            $action = 'newkategoriarsip';

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
                'nama_kategori' => 'required|string|max:200|unique:db_kategori_arsip,nama_kategori',
                'keterangan'    => 'required|string|max:200',
            ]);

            $response = app('App\Services\ApiArsip')->savekategoriarsip($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 201 : 422);

        } catch (Throwable $e) {
            Log::error('savekategoriarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewkategoriarsip(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'kategoriarsip';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user     = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {
                return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan'], 500);
            }

            $resultsUser             = $get_user['results'][0];
            $res_level_user          = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $level_user              = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($level_user[$menu] ?? 'No') === 'No') {
                return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses'], 403);
            }

            $request->validate(['code_data' => 'required']);

            $response = app('App\Services\ApiArsip')->viewkategoriarsip($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['kategoriarsip'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailkategoriarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updatekategoriarsip(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'kategoriarsip';
            $action = 'editkategoriarsip';

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
                'nama_kategori' => 'required|string|max:200',
                'keterangan'    => 'required|string|max:200',
            ]);

            $response = app('App\Services\ApiArsip')->editkategoriarsip($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updatekategoriarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function deletekategoriarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu = 'kategoriarsip';
            $action = 'deletekategoriarsip';

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

            $response = app('App\Services\ApiArsip')->deletekategoriarsip($request);
            $results = is_array($response) ? $response : $response->getData(true);

            return response()->json($results);
            
        } catch (Throwable $e) {
            Log::error('deletekategoriarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function exportkategoriarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'kategoriarsip';

            $menu = 'kategoriarsip';
            $action = 'exportkategoriarsip';

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
                
            $fileName = "Data-KategoriArsip-".date('Y-m-d-His').".xls" ;
            Excel::store(new KategoriArsip($request),'exports/' . $fileName,'public');
            return response()->json(['success' => true,'download_url' => url('/admin/download-exportdata/' . $fileName)]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage() ], 500);
        }
    }  

    // ArsipTags
    public function listarsiptags(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'arsiptags';

            $menu = 'earsip';
            $action = 'arsiptags';
            $viewpath = 'admin.AdminOne.earsip.listdata.arsiptags';

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
            Log::error('arsiptags Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function datalistarsiptags(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'arsiptags';

            $menu = 'earsip';
            $action = 'arsiptags';

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

            $response = app('App\Services\ApiArsip')->arsiptags($request);            
            $results = is_array($response) ? $response : $response->getData(true); 

            return $results;
            
        } catch (Throwable $e) {
            Log::error('dataarsiptags Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function savearsiptags(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'arsiptags';
            $action = 'newarsiptags';

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
                'nama_tag' => 'required|string|max:200|unique:db_arsip_tags,nama_tag',
            ]);

            $response = app('App\Services\ApiArsip')->savearsiptags($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 201 : 422);

        } catch (Throwable $e) {
            Log::error('savearsiptags Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewarsiptags(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'arsiptags';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user     = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {
                return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan'], 500);
            }

            $resultsUser             = $get_user['results'][0];
            $res_level_user          = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $level_user              = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($level_user[$menu] ?? 'No') === 'No') {
                return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses'], 403);
            }

            $request->validate(['code_data' => 'required']);

            $response = app('App\Services\ApiArsip')->viewarsiptags($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['arsiptags'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailarsiptags Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updatearsiptags(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'arsiptags';
            $action = 'editarsiptags';

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
                'nama_tag' => 'required|string|max:200',
            ]);

            $response = app('App\Services\ApiArsip')->editarsiptags($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updatearsiptags Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function deletearsiptags(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu = 'arsiptags';
            $action = 'deletearsiptags';

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

            $response = app('App\Services\ApiArsip')->deletearsiptags($request);
            $results = is_array($response) ? $response : $response->getData(true);

            return response()->json($results);
            
        } catch (Throwable $e) {
            Log::error('deletearsiptagsp Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function exportarsiptags(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'arsiptags';

            $menu = 'arsiptags';
            $action = 'exportarsiptags';

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
                
            $fileName = "Data-ArsipTags-".date('Y-m-d-His').".xls" ;
            Excel::store(new DataArsipTags($request),'exports/' . $fileName,'public');
            return response()->json(['success' => true,'download_url' => url('/admin/download-exportdata/' . $fileName)]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage() ], 500);
        }
    }  

    // KlasifikasiArsip
    public function listklasifikasiarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'klasifikasiarsip';

            $menu = 'earsip';
            $action = 'klasifikasiarsip';
            $viewpath = 'admin.AdminOne.earsip.listdata.klasifikasiarsip';

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
            Log::error('klasifikasiarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function datalistklasifikasiarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'klasifikasiarsip';

            $menu = 'earsip';
            $action = 'klasifikasiarsip';

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

            $response = app('App\Services\ApiArsip')->klasifikasiarsip($request);            
            $results = is_array($response) ? $response : $response->getData(true); 

            return $results;
            
        } catch (Throwable $e) {
            Log::error('dataklasifikasiarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }        
    }

    public function saveklasifikasiarsip(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'klasifikasiarsip';
            $action = 'newklasifikasiarsip';

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
                'code_klasifikasi' => 'required|string|max:200|unique:db_klasifikasi_arsip,code_klasifikasi',
                'nama_klasifikasi' => 'required|string|max:200|unique:db_klasifikasi_arsip,nama_klasifikasi',
            ]);

            $response = app('App\Services\ApiArsip')->saveklasifikasiarsip($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 201 : 422);

        } catch (Throwable $e) {
            Log::error('saveklasifikasiarsipp Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewklasifikasiarsip(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'klasifikasiarsip';

            $responseUser = app('App\Services\ApiUsers')->getadmin($request);
            $get_user     = is_array($responseUser) ? $responseUser : $responseUser->getData(true);
            if (!$get_user || $get_user['status_message'] === 'error') {
                return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan'], 500);
            }

            $resultsUser             = $get_user['results'][0];
            $res_level_user          = $resultsUser['leveladmin'][0];
            $request['data_company'] = $get_user['results'][0]['data_company'];
            $level_user              = collect($res_level_user)->pluck('access_rights', 'data_menu')->toArray();

            if (($level_user[$menu] ?? 'No') === 'No') {
                return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses'], 403);
            }

            $request->validate(['code_data' => 'required']);

            $response = app('App\Services\ApiArsip')->viewklasifikasiarsip($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['klasifikasiArsip'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailklasifikasiarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updateklasifikasiarsip(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'klasifikasiarsip';
            $action = 'editklasifikasiarsip';

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
                'code_klasifikasi' => 'required|string|max:200',
                'nama_klasifikasi' => 'required|string|max:200',
            ]);

            $response = app('App\Services\ApiArsip')->editklasifikasiarsip($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updateklasifikasiarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function deleteklasifikasiarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu = 'klasifikasiarsip';
            $action = 'deleteklasifikasiarsip';

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

            $response = app('App\Services\ApiArsip')->deleteklasifikasiarsip($request);
            $results = is_array($response) ? $response : $response->getData(true);

            return response()->json($results);
            
        } catch (Throwable $e) {
            Log::error('deleteklasifikasiarsip Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function exportklasifikasiarsip(Request $request)
    {
        try { 
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return redirect('/admin/logout')->with('error', 'Session tidak valid');
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'earsip';
            $request['url_active'] = 'klasifikasiarsip';

            $menu = 'klasifikasiarsip';
            $action = 'exportklasifikasiarsip';

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
                
            $fileName = "Data-KlasifikasiArsip-".date('Y-m-d-His').".xls" ;
            Excel::store(new DataKlasifikasiArsip($request),'exports/' . $fileName,'public');
            return response()->json(['success' => true,'download_url' => url('/admin/download-exportdata/' . $fileName)]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage() ], 500);
        }
    } 
}
