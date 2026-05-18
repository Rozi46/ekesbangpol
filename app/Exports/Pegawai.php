<?php

namespace App\Exports;

use Illuminate\Http\{Request,Response,UploadedFile};
use Illuminate\Support\Facades\{Http,Route,Session,Hash};
use Illuminate\Support\Carbon;
use App\Http\Controllers\{Controller,ApiController};
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\{FromView, WithCustomValueBinder};
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use Artisan;
use Cookie;
use JWTAuth;

class Pegawai extends StringValueBinder implements FromView, WithCustomValueBinder
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol') || 
            empty(session('key_token_kesbangpol')) || empty(session('admin_login_kesbangpol'))) {
            return redirect('/admin/logout')->with('error', 'Session tidak valid');
        }

        date_default_timezone_set('Asia/Jakarta');
       
        $request = $this->request;
        $request['u'] = session('admin_login_kesbangpol');
        $request['token'] = session('key_token_kesbangpol');
        $request['app'] = 'masterdata';
        $request['url_active'] = 'listpegawai';

        $viewpath = 'admin.AdminOne.masterdata.exportdata.pegawai';

        $responseUser = app('App\Services\ApiUsers')->getadmin($request);
        $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true); 

        if (empty($get_user) || ($get_user['status_message'] ?? '') === 'error') {
            return redirect('/admin/logout')->with('error', 'Data user tidak valid');
        }

        $res_user = $get_user['results'][0]['detailadmin'][0] ?? [];

        $request['per_page'] = 999999999;
        $request['type'] = 'export';

        $response = app('App\Services\ApiEmployees')->listpegawai($request);
        $results = is_array($response) ? $response : ($response->getData(true) ?? []);

        return view($viewpath,['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => (object) $request,'res_user' => $res_user,'results' => $results['results'] ?? []]);
    }
}