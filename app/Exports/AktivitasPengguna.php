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

class AktivitasPengguna implements FromView
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        // VALIDASI SESSION
        if (!session()->has('key_token_kesbangpol') || !session()->has('admin_login_kesbangpol')) {
            return redirect('/admin/logout')->with('error', 'Terjadi kesalahan!!! silahkan hubungi kami');
        }

        date_default_timezone_set('Asia/Jakarta');

        // INIT DATA        
        $request = $this->request;
        $request['u'] = session('admin_login_kesbangpol');
        $request['token'] = session('key_token_kesbangpol');
        $request['app'] = 'users';
        $request['url_active'] = 'activityusers';

        $viewpath = 'admin.AdminOne.masterpengguna.exportdata.aktivitaspengguna';

        // GET USER
        $responseUser = app('App\Services\ApiUsers')->getadmin($request);
        $get_user = is_array($responseUser) ? $responseUser : $responseUser->getData(true); 

        if (empty($get_user) || ($get_user['status_message'] ?? '') === 'error') {
            return redirect('/admin/logout')>with('error', 'Terjadi kesalahan!!! silahkan hubungi kami');
        }

        $res_user = $get_user['results'][0]['detailadmin'][0] ?? [];

        // FILTER TANGGAL
        Carbon::setLocale('en');

        $datefilterstart = now()->subDays(30)->startOfDay();
        $datefilterend = now()->endOfDay();

        if (!empty($request['searchdate'])) {
            [$start, $end] = explode("sd", $request['searchdate']);
            $datefilterstart = Carbon::parse($start)->startOfDay();
            $datefilterend = Carbon::parse($end)->endOfDay();
        }

        // PARAM EXPORT
        $request['vd'] = 999999999; // limit besar
        $request['type'] = 'export';
        $request['searchdate'] = $datefilterstart->format('Y-m-d H:i:s') . 'sd' .$datefilterend->format('Y-m-d H:i:s');

        // CALL SERVICE
        $response = app('App\Services\ApiUsers')->activityusers($request);
        $results = is_array($response) ? $response : ($response->getData(true) ?? []);

        // VIEW
        return view($viewpath, ['url_api' => env('APP_API'),'app' => $request['app'],'url_active' => $request['url_active'],'request' => (object) $request,'res_user' => $res_user,'results' => $results['results'] ?? []]);
    }
}