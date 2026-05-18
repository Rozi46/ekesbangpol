<?php

namespace App\Services;

use App\Models\{Setting, Company, User, LevelAdmin, ListAkses, Activity, Position};
use Illuminate\Http\{Request, UploadedFile, Response};
use Illuminate\Support\Facades\{Hash, Validator, File, Http, Route, Session, Auth, DB, Lang};
use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Query\Builder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiPosition
{
    public function listjabatan(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
        }

        $menus = ['masterdata', 'listjabatan'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }        
            
        $query = Position::query();

        if ($request->filled('search')) {
            $query->where('jabatan', 'Ilike', "%{$request->search}%")
                ->orWhere('code_data', 'Ilike', "%{$request->search}%");
        }

        $allowedSort = ['created_at', 'code_data', 'jabatan'];
        $sortBy = in_array($request->sort_by, $allowedSort) ? $request->sort_by : 'created_at';
        $sortOrder = $request->sort_order === 'desc' ? 'desc' : 'asc';

        $data = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate((int) $request->per_page);

        return response()->json(['status_message'=>'success','note'=>'Proses data berhasil','results'=> $data],200);
    }

    public function savejabatan(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['listjabatan', 'newjabatan'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $validator = Validator::make($request->all(), [
            'jabatan'   => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $lastData  = Position::orderByDesc('created_at')->first();
            $lastNumber = $lastData ? (int) substr($lastData->code_data, -4) : 0;
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $otp        = random_int(1000, 9999);
            $codeData  = "JB{$otp}{$newNumber}";

            $position = Position::create([
                'id'            => Str::uuid(),
                'code_data'     => $codeData,
                'jabatan'       => $request->jabatan,
                'status_data'   => $request->status_data ?? 'Aktif',
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Tambah data jabatan [{$request->jabatan} - {$codeData}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => $position], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewjabatan(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['listjabatan'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $jabatan = Position::where('code_data', $request->code_data)->first();
        if (!$jabatan) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['jabatan' => $jabatan, 'count_used' => 0]], 200);
    }

    public function editjabatan(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['listjabatan', 'editjabatan'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $jabatan = Position::where('code_data', $request->code_data)->first();
        if (!$jabatan) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        $validator = Validator::make($request->all(), [
            'jabatan' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $jabatan->update([
                'jabatan'       => ucfirst($request->jabatan),
                'status_data'   => $request->status_data,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data jabatan [{$jabatan->jabatan} - {$jabatan->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }
    
    public function upstatusjabatan(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['listjabatan', 'editjabatan'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $jabatan = Position::where('code_data', $request->code_data)->first();
        if (!$jabatan) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();            

            $jabatan->status_data = $request->status;
            $jabatan->save();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Update status jabatan [{$jabatan->jabatan} - {$jabatan->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }
    
    public function deletejabatan(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['listjabatan', 'deletejabatan'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $jabatan = Position::where('code_data', $request->code_data)->first();
        if (!$jabatan) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();
            $jabatan->delete();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Hapus data jabatan [{$jabatan->jabatan} - {$jabatan->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil dihapus','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }
}