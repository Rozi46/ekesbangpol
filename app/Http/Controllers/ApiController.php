<?php

namespace App\Http\Controllers;

require '../vendor/autoload.php';

use App\Http\Controllers\Controller;
use App\Models\{Setting, Company, User, LevelAdmin, ListAkses, Activity, Atlet, Club, Event, Result, Registrasi, KelompokUmur, Heat};
use Illuminate\Http\{Request, UploadedFile, Response};
use Illuminate\Support\Facades\{Hash, Validator, File, Http, Route, Session, Auth, DB, Lang};
use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Query\Builder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Ramsey\Uuid\Uuid;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $url_api =  env('ART_API');
        $url_app =  env('APP_URL');
        $object = [];
        $email = $request->email;
        $password = $request->password;
        $validator = Validator::make($request->all(), [
            'email'     => 'required|min:1|max:200',
            'password'  => 'required|min:1|max:200',
        ]);

        if($validator->fails()){return response()->json(['status_message' => 'failed','note' => $validator->errors()]);}

        $credentials = $request->only('email', 'password');
        $getdata = User::where('email',$email)->first();
        $getstatusdata = User::where('email',$email)->where('status_data','Aktif')->first();

        if($getdata){
            if($getstatusdata){
                if(Hash::check($password,$getdata->password)){
                    $resultsdata['detailadmin'] = array();
                    array_push($resultsdata['detailadmin'], $getdata);
                    $leveladmin = LevelAdmin::where('code_data','=',$getdata->level)->get();
                    $resultsdata['leveladmin'] = array();
                    array_push($resultsdata['leveladmin'], $leveladmin);
                    array_push($object, $resultsdata);

                    $link_akses = app()->environment('production') ? 'Online' : 'Offline';
                    
                    if($getdata->level != 'LV5677001'){
                        $otp = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1);
                        $newCodeData_activity = ltrim(Carbon::now()->format('Ymdhis') . $otp, '0');
                        Activity::create([
                            'id'            => Str::uuid(),
                            'code_data'     => $newCodeData_activity,
                            'code_user'     => $getdata->code_data,
                            'activity'      => 'Masuk ke sistem '.$link_akses,
                            'code_company'  => $getdata->code_company,
                        ]);
                    }

                    if (!$token = JWTAuth::attempt($credentials)) {
                        return response()->json(['error' => 'Unauthorized'], 401);
                    }

                    User::where('code_data', $getdata->code_data)->update([
                        'key_token' => $token,
                    ]);
                    return response()->json(['status_message' => 'success','note' => 'Berhasil masuk ke sistem','key_token' => $token,'results' => $object],200)->withCookie(cookie('jwt_token', $token, 120));
                }else{
                    return response()->json(['status_message' => 'failed','note' => 'Kata sandi salah','results' => $object]);
                }
            }else{
                return response()->json(['status_message' => 'failed','note' => 'Data pengguna tidak aktif','results' => $object]);
            }
        }else{
            return response()->json(['status_message' => 'failed','note' => 'Data tidak terdaftar','results' => $object]);
        }
    }

    public function logout(Request $request)
    {
        $url_api =  env('ART_API');
        $url_app =  env('APP_URL');
        $object = [];
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if($viewadmin && $request->token != null){

            $link_akses = app()->environment('production') ? 'Online' : 'Offline';
                    
            if($viewadmin->level != 'LV5677001'){
                $otp = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1);
                $newCodeData_activity = ltrim(Carbon::now()->format('Ymdhis') . $otp, '0');
                Activity::create([
                    'id'            => Str::uuid(),
                    'code_data'     => $newCodeData_activity,
                    'code_user'     => $viewadmin->code_data,
                    'activity'      => 'Keluar dari sistem '.$link_akses,
                    'code_company'  => $viewadmin->code_company,
                ]);
            }

            try {
                JWTAuth::invalidate(JWTAuth::getToken());
            } catch (TokenExpiredException $e) {
                // Token kadaluarsa, tidak perlu panic
            } catch (TokenInvalidException $e) {
                // Token tidak valid, bisa abaikan
            } catch (JWTException $e) {
                // Token tidak ditemukan atau error lainnya
            }

            User::where('id', $viewadmin->id)->update([
                'key_token' => null,
            ]);
    
            Session::flush();    
            return response()->json( ['status_message' => 'success','note' => 'Berhasil keluar ke sistem','code_data' => $viewadmin->code_data,]);
        }else{
            return response()->json( ['status_message' => 'failed','note' => 'Terjadi kesalahan saat keluar ke sistem','code_data' => $object]);
        }                
    }

    public function getdash(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $object = [];
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if(!$viewadmin){ 
            return response()->json(['status_message' => 'failed','note' => 'Terjadi kesalahan saat proses data']);   
        }else{   
            $thn_now = Carbon::now()->format('Y');
            $bln_now = Carbon::now()->format('m');
            $hari_now = Carbon::now()->format('d');

            $results['thn_now'] = $thn_now;
            $results['bln_now'] = $bln_now;            

            $vd = intval($request->vd ?? 20);
            

            return response()->json(['status_message' => 'success','results' => $results]);
        }
    }
}
