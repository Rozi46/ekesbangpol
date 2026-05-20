<?php

namespace App\Services;

use App\Models\{Setting, Company, User, LevelAdmin, ListAkses, Activity, Employees, Position, Ranks};
use Illuminate\Http\{Request, UploadedFile, Response};
use Illuminate\Support\Facades\{Hash, Validator, File, Http, Route, Session, Auth, DB, Lang};
use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Query\Builder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiEmployees
{    
    // isi select
        public function listopjabatan(Request $request)
        {
            $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
            if (!$admin) {
                return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
            }

            $menus = ['listpegawai', 'newpegawai'];
            $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

            $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
                return ($access[$menu] ?? 'No') === 'No';
            });

            if ($hasNoAccess) {
                return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
            }  

            $results = Position::where('status_data','Aktif')->orderBy('created_at', 'ASC')->get();

            return response()->json(['status_message' => 'success','results' => $results], 201);
        }

        public function listoppangkat(Request $request)
        {
            $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
            if (!$admin) {
                return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
            }

            $menus = ['listpegawai', 'newpegawai'];
            $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

            $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
                return ($access[$menu] ?? 'No') === 'No';
            });

            if ($hasNoAccess) {
                return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
            }  

            $results = Ranks::where('status_data','Aktif')->orderBy('created_at', 'ASC')->get();

            return response()->json(['status_message' => 'success','results' => $results], 201);
        }
    // end isi select

    public function listpegawai(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
        }

        $menus = ['masterdata', 'listpegawai'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        } 
        
        $query = Employees::with([
                'position:code_data,jabatan',
                'ranks:code_data,pangkat,golongan,ruang'
            ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_pegawai', 'ILIKE', "%{$search}%")
                    ->orWhere('gender', 'ILIKE', "%{$search}%")
                    ->orWhere('nomor_hp', 'ILIKE', "%{$search}%")
                    ->orWhere('code_data', 'ILIKE', "%{$search}%")
                    ->orWhereHas('position', function ($sub) use ($search) {
                        $sub->where('jabatan', 'ILIKE', "%{$search}%");
                    });
            });
        }

        $allowedSort = ['created_at', 'code_data', 'nama_pegawai', 'gender', 'nomor_hp', 'jabatan'];
        $sortBy = in_array($request->sort_by, $allowedSort) ? $request->sort_by : 'created_at';
        $sortOrder = $request->sort_order === 'desc' ? 'desc' : 'asc';
        if ($sortBy === 'jabatan') {
            $query->leftJoin('db_positions', 'db_positions.code_data', '=', 'db_employees.code_jabatan')
                ->select('db_employees.*')
                ->orderBy('db_positions.jabatan', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }
        $data = $query->paginate((int) $request->per_page);

        return response()->json(['status_message'=>'success','note'=>'Proses data berhasil','results'=> $data],200);
    }

    public function savepegawai(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['listpegawai', 'newpegawai'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_pegawai'  => 'required|string|max:200',
            'nomor_ktp'     => 'required|string|max:200',
            'agama'         => 'required|string|max:200',
            'nip'           => 'required|string|max:200',
            'gender'        => 'required|string|max:200',
            'tempat_lahir'  => 'required|string|max:200',
            'tanggal_lahir' => 'required|string|max:200',
            'code_jabatan'  => 'required|string|max:200',
            'pendidikan'    => 'required|string|max:200',
            'jurusan'       => 'required|string|max:200',
            'code_pangkat'  => 'required|string|max:200',
            'alamat'        => 'required|string|max:200',
            'email'         => 'required|string|max:200',
            'nomor_hp'      => 'required|string|max:200',
            'photo_profil'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'status_data'   => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $lastData   = Employees::orderByDesc('created_at')->first();
            $lastNumber = $lastData ? (int) substr($lastData->code_data, -4) : 0;
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $otp        = random_int(1000, 9999);
            $codeData   = "PG{$otp}{$newNumber}";

            $photoName = null;

            if ($request->hasFile('photo_profil')) {
                $file = $request->file('photo_profil');
                $photoName = $codeData . time() . $file->getClientOriginalName();
                $file->move(public_path('/image/pegawai/'),$photoName);
            }

            $Employees = Employees::create([
                'id'            => Str::uuid(),
                'code_data'     => $codeData,
                'nama_pegawai'  => $request->nama_pegawai,
                'nomor_ktp'     => $request->nomor_ktp,
                'agama'         => $request->agama,
                'nip'           => $request->nip,
                'gender'        => $request->gender,
                'tempat_lahir'  => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'code_jabatan'  => $request->code_jabatan,
                'pendidikan'    => $request->pendidikan,
                'jurusan'       => $request->jurusan,
                'code_pangkat'  => $request->code_pangkat,
                'alamat'        => $request->alamat,
                'email'         => $request->email,
                'nomor_hp'      => $request->nomor_hp,
                'photo_profil'  => $photoName,
                'status_data'   => $request->status_data ?? 'Aktif',
                'code_company'  => $admin->code_company,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Tambah data pegawai [{$request->nama_pegawai} - {$codeData}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => $Employees], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewpegawai(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['listpegawai'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $pegawai = Employees::with([
                'position:code_data,jabatan',
                'ranks:code_data,pangkat,golongan,ruang'
            ])
            ->where('code_data', $request->code_data)->first();

        if (!$pegawai) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['pegawai' => $pegawai, 'count_used' => 0]], 200);
    }

    public function editpegawai(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['listpegawai', 'editpegawai'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $pegawai = Employees::where('code_data', $request->code_data)->first();
        if (!$pegawai) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_pegawai'  => 'required|string|max:200',
            'nomor_ktp'     => 'required|string|max:200',
            'agama'         => 'required|string|max:200',
            'nip'           => 'required|string|max:200',
            'gender'        => 'required|string|max:200',
            'tempat_lahir'  => 'required|string|max:200',
            'tanggal_lahir' => 'required|string|max:200',
            'code_jabatan'  => 'required|string|max:200',
            'pendidikan'    => 'required|string|max:200',
            'jurusan'       => 'required|string|max:200',
            'code_pangkat'  => 'required|string|max:200',
            'alamat'        => 'required|string|max:200',
            'email'         => 'required|string|max:200',
            'nomor_hp'      => 'required|string|max:200',
            'status_data'   => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $photoName = $pegawai->photo_profil;
            // jika upload foto baru
            if ($request->hasFile('photo_profil')) {
                $file = $request->file('photo_profil');
                $photoName = $pegawai->code_data . time() . $file->getClientOriginalName();
                $file->move(public_path('/image/pegawai/'),$photoName);

                if (!empty($pegawai->photo_profil)) {
                    File::delete(public_path('/image/pegawai/' . $pegawai->photo_profil));
                }

                // // optional: hapus file lama
                // if ($pegawai->photo_profil && file_exists(public_path('/image/pegawai/' . $pegawai->photo_profil))) {
                //     @unlink(public_path('/image/pegawai/' . $pegawai->photo_profil));
                // }
            }

            $pegawai->update([
                'nama_pegawai'  => $request->nama_pegawai,
                'nomor_ktp'     => $request->nomor_ktp,
                'agama'         => $request->agama,
                'nip'           => $request->nip,
                'gender'        => $request->gender,
                'tempat_lahir'  => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'code_jabatan'  => $request->code_jabatan,
                'pendidikan'    => $request->pendidikan,
                'jurusan'       => $request->jurusan,
                'code_pangkat'  => $request->code_pangkat,
                'alamat'        => $request->alamat,
                'email'         => $request->email,
                'nomor_hp'      => $request->nomor_hp,
                'photo_profil'  => $photoName,
                'status_data'   => $request->status_data,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data pegawai [{$pegawai->nama_pegawai} - {$pegawai->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }
    
    public function upstatuspegawai(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['listpegawai', 'editpegawai'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $pegawai = Employees::where('code_data', $request->code_data)->first();
        if (!$pegawai) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();            

            $pegawai->status_data = $request->status;
            $pegawai->save();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Update status pegawai [{$pegawai->nama_pegawai} - {$pegawai->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }
    
    public function deletepegawai(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['listpegawai', 'deletepegawai'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $pegawai = Employees::where('code_data', $request->code_data)->first();
        if (!$pegawai) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();

            $oldFoto = $pegawai->photo_profil;
            $pegawai->delete();

            if (!empty($oldFoto)) {
                $path = public_path('/image/pegawai/' . $oldFoto);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Hapus data pegawai [{$pegawai->nama_pegawai} - {$pegawai->code_data}]",
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