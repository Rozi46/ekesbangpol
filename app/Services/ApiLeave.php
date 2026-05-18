<?php

namespace App\Services;

use App\Models\{Setting, Company, User, LevelAdmin, ListAkses, Activity, Leave};
use Illuminate\Http\{Request, UploadedFile, Response};
use Illuminate\Support\Facades\{Hash, Validator, File, Http, Route, Session, Auth, DB, Lang};
use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Query\Builder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiLeave
{
    // public function listcuti(Request $request)
    // {
    //     $admin = User::where('id', $request->u)
    //         ->where('key_token', $request->token)
    //         ->first();

    //     if (!$admin) {
    //         return response()->json([
    //             'status_message' => 'error',
    //             'note' => 'User tidak valid'
    //         ], 401);
    //     }

    //     $menus = ['masterdata', 'listcuti', 'exportcuti'];

    //     $access = LevelAdmin::where('code_data', $admin->level)
    //         ->whereIn('data_menu', $menus)
    //         ->pluck('access_rights', 'data_menu');

    //     $hasNoAccess = collect($menus)->contains(
    //         fn ($menu) => ($access[$menu] ?? 'No') === 'No'
    //     );

    //     if ($hasNoAccess) {
    //         return response()->json([
    //             'status_message' => 'error',
    //             'note' => 'Tidak ada akses',
    //             'results' => []
    //         ], 403);
    //     }

    //     $query = Leave::query();

    //     if ($request->filled('search')) {
    //         $query->where(function ($q) use ($request) {
    //             $q->where('jenis_cuti', 'ILIKE', "%{$request->search}%")
    //             ->orWhere('code_data', 'ILIKE', "%{$request->search}%");
    //         });
    //     }

    //     $allowedSort = ['created_at', 'code_data', 'jenis_cuti'];

    //     $sortBy = in_array($request->sort_by, $allowedSort)
    //         ? $request->sort_by
    //         : 'created_at';

    //     $sortOrder = $request->sort_order === 'desc'
    //         ? 'desc'
    //         : 'asc';

    //     $perPage = ($request->type ?? '') === 'export'
    //         ? 999999999
    //         : (int) ($request->per_page ?? 10);

    //     $data = $query
    //         ->orderBy($sortBy, $sortOrder)
    //         ->paginate($perPage);

    //     return response()->json([
    //         'status_message' => 'success',
    //         'note' => 'Proses data berhasil',
    //         'results' => $data
    //     ], 200);
    // }

    public function listcuti(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
        }

        // ✅ RBAC (1x query saja)
        $menus = ['masterdata', 'listcuti'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        // akses utama
        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }        
            
        $query = Leave::query();

        if ($request->filled('search')) {
            $query->where('jenis_cuti', 'Ilike', "%{$request->search}%")
                ->orWhere('code_data', 'Ilike', "%{$request->search}%");
        }

        $allowedSort = ['created_at', 'code_data', 'jenis_cuti'];
        $sortBy = in_array($request->sort_by, $allowedSort) ? $request->sort_by : 'created_at';
        $sortOrder = $request->sort_order === 'desc' ? 'desc' : 'asc';

        $data = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate((int) $request->per_page);

        // return response()->json($data);
        return response()->json(['status_message'=>'success','note'=>'Proses data berhasil','results'=> $data],200);

        // // ✅ PAGINATION CLEAN
        // $perPage = (int) $request->get('vd', 20);
        // $perPage = $perPage > 0 ? $perPage : 20;

        // // ✅ QUERY
        // $query = Leave::query();

        // if ($request->filled('keysearch')) {
        //     $search = $request->keysearch;

        //     $query->where(function ($q) use ($search) {
        //         $q->where('code_data', 'ILIKE', "%{$search}%")
        //         ->orWhere('jenis_cuti', 'ILIKE', "%{$search}%");
        //     });
        // }

        // // ✅ OPTIMASI COUNT (hindari N+1)
        // // $query->withCount([
        // //     'suratCuti as count_used' => function ($q) {
        // //         $q->select(DB::raw("count(*)"));
        // //     }
        // // ]);

        // // $cuti = $query->orderByDesc('created_at')->paginate($perPage);
        // $cuti = $query->orderBy('created_at', 'asc')->paginate($perPage);

        // // ✅ RESPONSE
        // return response()->json([
        //     'status_message'   => 'success',
        //     'note'             => 'Proses data berhasil',
        //     'count_all_data'   => $cuti->total(),
        //     'count_view_data'  => $perPage,
        //     'keysearch'        => $request->keysearch,
        //     'results'          => ['list' => $cuti]
        // ]);
    }

    public function savecuti2(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['listcuti', 'newcuti'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $validator = Validator::make($request->all(), [
            'jenis_cuti' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $lastLeave = Leave::orderByDesc('created_at')->first();
            $lastNumber = $lastLeave ? (int) substr($lastLeave->code_data, -4) : 0;
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $otp = random_int(1000, 9999);
            $codeLeave = "CT{$otp}{$newNumber}";

            $leave = Leave::create([
                'id'            => Str::uuid(),
                'code_data'     => $codeLeave,
                'jenis_cuti'    => $request->jenis_cuti,
                'status_data'   => 'Aktif',
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $viewadmin->code_data,
                'activity'     => "Tambah data cuti [{$request->jenis_cuti} - {$codeLeave}]",
                'code_company' => $viewadmin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => $leave], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    public function viewcuti2(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['listcuti', 'editcuti'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $cuti = Leave::where('code_data', $request->code_data)->first();
        if (!$cuti) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        // $count_used = SuratCuti::where('code_cuti', $cuti->code_data)->count(); 
        $count_used = 0; 

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['cuti' => $cuti, 'count_used' => $count_used]], 200);
    }
    
    public function editcuti2(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['listcuti', 'editcuti'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $cuti = Leave::where('code_data', $request->code_data)->first();
        if (!$cuti) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        $validator = Validator::make($request->all(), [
            'jenis_cuti' => 'required|string|max:200',
        ]);

        if($validator->fails()){
            return response()->json(['status_message' => 'error','note' => $validator->errors() ], 422);
        }

        try {
            DB::beginTransaction();
            $cuti->update([
                'jenis_cuti' => ucfirst($request->get('jenis_cuti')),
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $viewadmin->code_data,
                'activity'     => "Ubah data cuti [{$cuti->jenis_cuti} - {$cuti->code_data}]",
                'code_company' => $viewadmin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }
    
    public function upstatuscuti(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['listcuti', 'editcuti'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $cuti = Leave::where('code_data', $request->code_data)->first();
        if (!$cuti) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();            

            $cuti->status_data = $request->status;
            $cuti->save();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $viewadmin->code_data,
                'activity'     => "Update status cuti [{$cuti->jenis_cuti} - {$cuti->code_data}]",
                'code_company' => $viewadmin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }
    
    public function deletecuti(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['listcuti', 'deletecuti'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $cuti = Leave::where('code_data', $request->code_data)->first();
        if (!$cuti) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();
            $cuti->delete();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $viewadmin->code_data,
                'activity'     => "Hapus data cuti [{$cuti->jenis_cuti} - {$cuti->code_data}]",
                'code_company' => $viewadmin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil dihapus','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    // ajax
    public function viewcuti(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['listcuti'];
        $access = LevelAdmin::where('code_data', $admin->level)
                    ->whereIn('data_menu', $menus)
                    ->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $cuti = Leave::where('code_data', $request->code_data)->first();
        if (!$cuti) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json([
            'status_message' => 'success',
            'note'           => 'Proses data berhasil',
            'results'        => ['cuti' => $cuti, 'count_used' => 0]
        ], 200);
    }

    public function savecuti(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['listcuti', 'newcuti'];
        $access = LevelAdmin::where('code_data', $admin->level)
                    ->whereIn('data_menu', $menus)
                    ->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $validator = Validator::make($request->all(), [
            'jenis_cuti' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $lastLeave  = Leave::orderByDesc('created_at')->first();
            $lastNumber = $lastLeave ? (int) substr($lastLeave->code_data, -4) : 0;
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $otp        = random_int(1000, 9999);
            $codeLeave  = "CT{$otp}{$newNumber}";

            $leave = Leave::create([
                'id'          => Str::uuid(),
                'code_data'   => $codeLeave,
                'jenis_cuti'  => $request->jenis_cuti,
                'status_data' => $request->status_data ?? 'Aktif',
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Tambah data cuti [{$request->jenis_cuti} - {$codeLeave}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => $leave], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function editcuti(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['listcuti', 'editcuti'];
        $access = LevelAdmin::where('code_data', $admin->level)
                    ->whereIn('data_menu', $menus)
                    ->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $cuti = Leave::where('code_data', $request->code_data)->first();
        if (!$cuti) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        $validator = Validator::make($request->all(), [
            'jenis_cuti' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $cuti->update([
                'jenis_cuti'    => ucfirst($request->jenis_cuti),
                'status_data'   => $request->status_data,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data cuti [{$cuti->jenis_cuti} - {$cuti->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }
}