<?php

namespace App\Exports;

use Illuminate\Http\{Request,Response,UploadedFile};
use Illuminate\Support\Facades\{Http,Route,Session,Hash};
use Illuminate\Support\Carbon;
use App\Http\Controllers\{Controller,ApiController};
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Artisan;
use Cookie;
use JWTAuth;

class DataPengguna implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol')) {
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan!!! silahkan hubungi kami');
        }

        date_default_timezone_set('Asia/Jakarta');
       
        $request = $this->request;
        $request['u'] = session('admin_login_kesbangpol');
        $request['token'] = session('key_token_kesbangpol');
        $request['app'] = 'users';
        $request['url_active'] = 'listusers';

        $viewpath = 'admin.AdminOne.masterpengguna.exportdata.datapengguna';

        $responseUser = app('App\Services\ApiUsers')->getadmin($request);
        $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true); 

        if (empty($get_user) || ($get_user['status_message'] ?? '') === 'error') {
            return redirect('/admin/logout')>with('error', 'Terjadi kesalahan!!! silahkan hubungi kami');
        }

        $res_user = $get_user['results'][0]['detailadmin'][0] ?? [];

        Carbon::setLocale('en');

        $datefilterstart = now()->subDays(30)->startOfDay();
        $datefilterend = now()->endOfDay();

        if (!empty($request['searchdate'])) {
            [$start, $end] = explode("sd", $request['searchdate']);
            $datefilterstart = Carbon::parse($start)->startOfDay();
            $datefilterend = Carbon::parse($end)->endOfDay();
        }

        $request['vd'] = 999999999;
        $request['type'] = 'export';
        $request['searchdate'] = $datefilterstart->format('Y-m-d H:i:s') . 'sd' .$datefilterend->format('Y-m-d H:i:s');

        $response = app('App\Services\ApiUsers')->listusers($request);
        $results = is_array($response) ? $response : ($response->getData(true) ?? []);

        return view($viewpath,['url_api' => env('APP_API'), 'app' => $request['app'], 'url_active' => $request['url_active'],'request' => (object) $request,'res_user' => $res_user,'results' => $results['results']['list'] ?? []]);
    }
}