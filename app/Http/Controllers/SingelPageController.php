<?php

namespace App\Http\Controllers;

use App\Models\{Post};

use Illuminate\Http\{Request, Response, UploadedFile};
use Illuminate\Support\Facades\{Http, Route, Session, Hash, Artisan, Cookie};
use Illuminate\Support\Carbon;
use App\Http\Controllers\{Controller, ApiController};
use Tymon\JWTAuth\Facades\JWTAuth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{DataAgenda};

class SingelPageController extends Controller
{ 
    public function datavisimisi(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'datawebsite';
            $request['url_active'] = 'datavisimisi';

            $menu = 'datawebsite';
            $action = 'datavisimisi';
            $viewpath = 'admin.AdminOne.datawebsite.editdata.visimisi';

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

            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results']]);

        } catch (Throwable $e) {
            Log::error('detailagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewvisimisi(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datavisimisi';

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

            $response = app('App\Services\ApiSingelPage')->viewvisimisi($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['singelpage'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updatevisimisi(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datavisimisi';
            $action = 'editvisimisi';

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

            $response = app('App\Services\ApiSingelPage')->updatevisimisi($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updateagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function datatentang(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'datawebsite';
            $request['url_active'] = 'datatentang';

            $menu = 'datawebsite';
            $action = 'datatentang';
            $viewpath = 'admin.AdminOne.datawebsite.editdata.tentang';

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

            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results']]);

        } catch (Throwable $e) {
            Log::error('detailagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewtentang(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datatentang';

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

            $response = app('App\Services\ApiSingelPage')->viewtentang($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['singelpage'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updatetentang(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datatentang';
            $action = 'edittentang';

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

            $response = app('App\Services\ApiSingelPage')->updatetentang($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updateagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function datatugasfungsi(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'datawebsite';
            $request['url_active'] = 'datatugasfungsi';

            $menu = 'datawebsite';
            $action = 'datatugasfungsi';
            $viewpath = 'admin.AdminOne.datawebsite.editdata.tugasfungsi';

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

            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results']]);

        } catch (Throwable $e) {
            Log::error('detailagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewtugasfungsi(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datatugasfungsi';

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

            $response = app('App\Services\ApiSingelPage')->viewtugasfungsi($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['singelpage'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updatetugasfungsi(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datatugasfungsi';
            $action = 'edittugasfungsi';

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

            $response = app('App\Services\ApiSingelPage')->updatetugasfungsi($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updateagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function datastrukturorganisasi(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'datawebsite';
            $request['url_active'] = 'datastrukturorganisasi';

            $menu = 'datawebsite';
            $action = 'datastrukturorganisasi';
            $viewpath = 'admin.AdminOne.datawebsite.editdata.strukturorganisasi';

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

            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results']]);

        } catch (Throwable $e) {
            Log::error('detailagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewstrukturorganisasi(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datastrukturorganisasi';

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

            $response = app('App\Services\ApiSingelPage')->viewstrukturorganisasi($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['strukturOrganisasi'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updatestrukturorganisasi(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'datastrukturorganisasi';
            $action = 'editstrukturorganisasi';

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

            $response = app('App\Services\ApiSingelPage')->updatestrukturorganisasi($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updateagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function dataalamatkontak(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u'] = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');
            $request['app'] = 'datawebsite';
            $request['url_active'] = 'dataalamatkontak';

            $menu = 'datawebsite';
            $action = 'dataalamatkontak';
            $viewpath = 'admin.AdminOne.datawebsite.editdata.alamatkontak';

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

            return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => $request,'res_user' => $res_user,'level_user' => $level_user,'list_akses' => $list_akses['results']]);

        } catch (Throwable $e) {
            Log::error('detailagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function viewalamatkontak(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'dataalamatkontak';

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

            $response = app('App\Services\ApiSingelPage')->viewalamatkontak($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            if (($results['note'] ?? '') === 'Data tidak ditemukan') {
                return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan'], 404);
            }

            $data = $results['results']['singelpage'] ?? $results['results'] ?? [];

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dimuat','data' => $data], 201);

        } catch (Throwable $e) {
            Log::error('detailagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function updatealamatkontak(Request $request)
    {
        try {
            if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') ||
                empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
                return response()->json(['status_message' => 'error', 'note' => 'Session tidak valid'], 401);
            }

            date_default_timezone_set('Asia/Jakarta');

            $request['u']     = session('admin_login_kesbangpol');
            $request['token'] = session('key_token_kesbangpol');

            $menu   = 'dataalamatkontak';
            $action = 'editalamatkontak';

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

            $response = app('App\Services\ApiSingelPage')->updatealamatkontak($request);
            $results  = is_array($response) ? $response : $response->getData(true);

            $status = $results['status_message'] ?? 'error';
            $note   = $results['note'] ?? 'Terjadi kesalahan';

            return response()->json(['status_message' => $status,'note' => $note,'results' => $results['results'] ?? []], $status === 'success' ? 200 : 422);

        } catch (Throwable $e) {
            Log::error('updateagenda Error: ' . $e->getMessage(), ['user' => $request->session()->get('admin_login_kesbangpol')]);
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
}