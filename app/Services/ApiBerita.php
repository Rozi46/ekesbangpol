<?php

namespace App\Services;

use App\Models\{Setting, Company, User, LevelAdmin, ListAkses, Activity, Berita};
use Illuminate\Http\{Request, UploadedFile, Response};
use Illuminate\Support\Facades\{Hash, Validator, File, Http, Route, Session, Auth, DB, Lang};
use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Query\Builder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiBerita
{ 
    public function listberita(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
        }

        $menus = ['datawebsite', 'databerita'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        } 
        
        // $query = Berita::query();
        $query = Berita::where('tipe_berita', 'Berita');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('judul_berita', 'Ilike', "%{$search}%")
                ->orWhere('url_berita', 'Ilike', "%{$search}%")
                ->orWhere('isi_berita', 'Ilike', "%{$search}%")
                ->orWhere('code_data', 'Ilike', "%{$search}%");
        }

        $allowedSort = ['created_at', 'code_data', 'judul_berita', 'url_berita', 'isi_berita'];
        $sortBy = in_array($request->sort_by, $allowedSort) ? $request->sort_by : 'created_at';
        $sortOrder = $request->sort_order === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortOrder);
        $data = $query->paginate((int) $request->per_page);

        return response()->json(['status_message'=>'success','note'=>'Proses data berhasil','results'=> $data],200);
    }

    public function saveberita(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['databerita', 'newberita'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $validator = Validator::make($request->all(), [
            'judul_berita'  => 'required|string|max:200',
            'isi_berita'    => 'required|string',
            'sumber_berita' => 'required|string|max:200',
            'photo_berita'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $lastData   = Berita::orderByDesc('created_at')->first();
            $lastNumber = $lastData ? (int) substr($lastData->code_data, -4) : 0;
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $otp        = random_int(1000, 9999);
            // $codeData   = "BR{$otp}{$newNumber}";
            $codeData = 'BR' . now()->format('YmdHis') . Str::upper(Str::random(1));

            $photoName = null;

            if ($request->hasFile('photo_berita')) {
                $file = $request->file('photo_berita');
                $photoName = $codeData . time() . $file->getClientOriginalName();
                $file->move(public_path('/image/post/'),$photoName);
            }

            $url_berita = str_replace(' ', '-',$request->judul_berita);
            $url_berita = preg_replace('/[^a-zA-Z0-9-]/','', $url_berita);
            $url_berita = strtolower(str_replace('--', '-',$url_berita));

            $berita = Berita::create([
                'id'            => Str::uuid(),
                'code_data'     => $codeData,
                'code_user'     => $admin->code_data,
                'url_berita'    => $url_berita,
                'judul_berita'  => $request->judul_berita,
                'isi_berita'    => $request->isi_berita,
                'sumber_berita' => $request->sumber_berita,
                'tumb_berita'   => $photoName,
                'jumlah_view'   => 0,
                'tipe_berita'   => 'Berita',
                'status_data'   => 'Aktif',
                'code_company'  => $admin->code_company,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Tambah data berita [{$request->judul_berita} - {$codeData}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => $berita], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewberita(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['databerita'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $berita = Berita::where('code_data', $request->code_data)->where('tipe_berita', 'Berita')->first();

        if (!$berita) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['berita' => $berita, 'count_used' => 0]], 200);
    }

    public function editberita(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['databerita', 'editberita'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $berita = Berita::where('code_data', $request->code_data)->where('tipe_berita', 'Berita')->first();
        if (!$berita) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        // $validator = Validator::make($request->all(), [
        //     'judul_berita'  => 'required|string|max:200',
        //     'isi_berita'    => 'required|string|max:200',
        //     'sumber_berita' => 'required|string|max:200',
        //     'photo_berita'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        // }

        try {
            DB::beginTransaction();

            $photoName = $berita->tumb_berita;
            // jika upload foto baru
            if ($request->hasFile('photo_berita')) {
                $file = $request->file('photo_berita');
                $photoName = $berita->code_data . time() . $file->getClientOriginalName();
                $file->move(public_path('/image/post/'),$photoName);

                // optional: hapus file lama
                if ($berita->photo_berita && file_exists(public_path('/image/post/' . $berita->photo_berita))) {
                    @unlink(public_path('/image/post/' . $berita->photo_berita));
                }
            }

            $url_berita = str_replace(' ', '-',$request->judul_berita);
            $url_berita = preg_replace('/[^a-zA-Z0-9-]/','', $url_berita);
            $url_berita = strtolower(str_replace('--', '-',$url_berita));

            $berita->update([
                'url_berita'    => $url_berita,
                'judul_berita'  => $request->judul_berita,
                'isi_berita'    => $request->isi_berita,
                'sumber_berita' => $request->sumber_berita,
                'tumb_berita'   => $photoName
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data berita [{$berita->judul_berita} - {$berita->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }
    
    public function upstatusberita(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['databerita', 'editberita'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $berita = Berita::where('code_data', $request->code_data)->where('tipe_berita', 'Berita')->first();
        if (!$berita) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();            

            $berita->status_data = $request->status;
            $berita->save();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Update status berita [{$berita->judul_berita} - {$berita->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }
    
    public function deleteberita(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['databerita', 'deleteberita'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $berita = Berita::where('code_data', $request->code_data)->where('tipe_berita', 'Berita')->first();
        if (!$berita) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();

            $oldFoto = $berita->photo_berita;
            $berita->delete();

            if (!empty($oldFoto)) {
                $path = public_path('/image/post/' . $oldFoto);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Hapus data berita [{$berita->judul_berita} - {$berita->code_data}]",
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