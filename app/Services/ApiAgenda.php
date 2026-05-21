<?php

namespace App\Services;

use App\Models\{Setting, Company, User, LevelAdmin, ListAkses, Activity, Post};
use Illuminate\Http\{Request, UploadedFile, Response};
use Illuminate\Support\Facades\{Hash, Validator, File, Http, Route, Session, Auth, DB, Lang};
use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Query\Builder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAgenda
{ 
    public function listagenda(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
        }

        $menus = ['datawebsite', 'dataagenda'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        } 
        
        $query = Post::where('tipe', 'Agenda');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('judul', 'Ilike', "%{$search}%")
                ->orWhere('code_data', 'Ilike', "%{$search}%");
        }

        $allowedSort = ['created_at', 'code_data', 'judul', 'url', 'isi'];
        $sortBy = in_array($request->sort_by, $allowedSort) ? $request->sort_by : 'created_at';
        $sortOrder = $request->sort_order === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortOrder);
        $data = $query->paginate((int) $request->per_page);

        return response()->json(['status_message'=>'success','note'=>'Proses data berhasil','results'=> $data],200);
    }

    public function saveagenda(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['dataagenda', 'newagenda'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $validator = Validator::make($request->all(), [
            'judul'  => 'required|string|max:200',
            'isi'    => 'required|string',
            'sumber' => 'required|string|max:200',
            'photo'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $codeData = 'AG' . now()->format('YmdHis') . Str::upper(Str::random(1));
            $photoName = null;

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $photoName = $codeData . time() . $file->getClientOriginalName();
                $file->move(public_path('/image/post/'),$photoName);
            }

            $url = str_replace(' ', '-',$request->judul);
            $url = preg_replace('/[^a-zA-Z0-9-]/','', $url);
            $url = strtolower(str_replace('--', '-',$url));

            $post = Post::create([
                'id'            => Str::uuid(),
                'code_data'     => $codeData,
                'code_user'     => $admin->code_data,
                'url'           => $url,
                'judul'         => $request->judul,
                'isi'           => $request->isi,
                'sumber'        => $request->sumber,
                'tumb'          => $photoName,
                'jumlah_view'   => 0,
                'tipe'          => 'Agenda',
                'status_data'   => 'Aktif',
                'code_company'  => $admin->code_company,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Tambah data agenda [{$request->judul} - {$codeData}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => $post], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewagenda(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['dataagenda'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $post = Post::where('code_data', $request->code_data)->where('tipe', 'Agenda')->first();

        if (!$post) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['post' => $post, 'count_used' => 0]], 200);
    }

    public function editagenda(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['dataagenda', 'editagenda'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $post = Post::where('code_data', $request->code_data)->where('tipe', 'Agenda')->first();
        if (!$post) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        try {
            DB::beginTransaction();

            $photoName = $post->tumb;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $photoName = $post->code_data . time() . $file->getClientOriginalName();
                $file->move(public_path('/image/post/'),$photoName);

                if (!empty($post->tumb)) {
                    File::delete(public_path('/image/post/' . $post->tumb));
                }
            }

            $url = str_replace(' ', '-',$request->judul);
            $url = preg_replace('/[^a-zA-Z0-9-]/','', $url);
            $url = strtolower(str_replace('--', '-',$url));

            $post->update([
                'url'    => $url,
                'judul'  => $request->judul,
                'isi'    => $request->isi,
                'sumber' => $request->sumber,
                'tumb'   => $photoName
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data agenda [{$post->judul} - {$post->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }
    
    public function upstatusagenda(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['dataagenda', 'editagenda'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $post = Post::where('code_data', $request->code_data)->where('tipe', 'Agenda')->first();
        if (!$post) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();            

            $post->status_data = $request->status;
            $post->save();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Update status agenda [{$post->judul} - {$post->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }
    
    public function deleteagenda(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['dataagenda', 'deleteagenda'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $post = Post::where('code_data', $request->code_data)->where('tipe', 'Agenda')->first();
        if (!$post) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();

            $oldFoto = $post->tumb;
            $post->delete();

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
                'activity'     => "Hapus data agenda [{$post->judul} - {$post->code_data}]",
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