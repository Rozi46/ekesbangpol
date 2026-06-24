<?php

namespace App\Services;

use App\Models\{Setting, Company, User, LevelAdmin, ListAkses, Activity, KategoriArsip, ArsipTag, KlasifikasiArsip, Arsip, ArsipLog};
use Illuminate\Http\{Request, UploadedFile, Response};
use Illuminate\Support\Facades\{Hash, Validator, File, Http, Route, Session, Auth, DB, Lang, Storage};
use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Query\Builder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiArsip
{
    // isi select
        public function listopkategoriarsip(Request $request)
        {
            $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
            if (!$admin) {
                return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
            }

            $menus = ['dataarsip', 'newarsip'];
            $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

            $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
                return ($access[$menu] ?? 'No') === 'No';
            });

            if ($hasNoAccess) {
                return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
            }  

            // $results = KategoriArsip::where('code_company',$admin->code_company)->orderBy('created_at', 'ASC')->get();
            $search = trim($request->search);

            $results = KategoriArsip::query()
                ->where('code_company', $admin->code_company)
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('nama_kategori', 'Ilike', "%{$search}%")
                        ->orWhere('code_data', 'Ilike', "%{$search}%");
                    });
                })

                ->orderBy('nama_kategori')
                ->get(['code_data','nama_kategori']);

            return response()->json(['status_message' => 'success','results' => $results], 201);
        }
    // end isi select

    // Arsip    
    public function dataarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
        }

        $menus = ['earsip', 'dataarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        } 

        $query = Arsip::with('kategori')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = "%{$request->search}%";

                $q->where(function ($query) use ($search) {
                    $query->where('judul', 'ILIKE', $search)
                        ->orWhere('code_data', 'ILIKE', $search)
                        ->orWhere('deskripsi', 'ILIKE', $search)
                        ->orWhereHas('kategori', function ($kategori) use ($search) {
                            $kategori->where('nama_kategori', 'ILIKE', $search);
                        });
                });
            });     

        $allowedSort = ['created_at', 'code_data', 'nama_kategori', 'judul', 'tanggal_dokumen', 'akses'];
        $sortBy = in_array($request->sort_by, $allowedSort) ? $request->sort_by : 'created_at';
        $sortOrder = $request->sort_order === 'desc' ? 'desc' : 'asc';

        $data = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate((int) $request->per_page);

        return response()->json(['status_message'=>'success','note'=>'Proses data berhasil','results'=> $data],200);
    }

    // public function savearsip(Request $request)
    // {
    //     date_default_timezone_set('Asia/Jakarta');

    //     $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
    //     if (!$admin) {
    //         return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
    //     }

    //     $menus  = ['dataarsip', 'newarsip'];
    //     $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

    //     $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
    //     if ($hasNoAccess) {
    //         return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'code_kategori'     => 'required|string|max:200',
    //         'judul'             => 'required|string|max:200',
    //         'tanggal_dokumen'   => 'required|string|max:200',
    //         'file_path'         => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:5120', // 5 MB
    //         'deskripsi'         => 'required|string|max:200',
    //         'akses'             => 'required|string|max:200',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
    //     }

    //     try {
    //         DB::beginTransaction();
    //         $codeData  = "ARS" . ltrim(now()->format('YmdHis') . Str::random(1), '0');

    //         $arsip = Arsip::create([
    //             'id'                => Str::uuid(),
    //             'code_data'         => $codeData,
    //             'code_kategori'     => $request->code_kategori,
    //             'judul'             => $request->judul,
    //             'tanggal_dokumen'   => $request->tanggal_dokumen,
    //             'deskripsi'         => $request->deskripsi,
    //             'akses'             => $request->akses,
    //             'file_path'         => $request->file_path,
    //             'code_user'         => $admin->code_data,
    //             'code_company'      => $admin->code_company,
    //         ]);

    //         ArsipLog::create([
    //             'id'           => Str::uuid(),
    //             'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
    //             'code_arsip'   => $codeData,
    //             'code_user'    => $admin->code_data,
    //             'aksi'         => "Tambah data arsip [{$request->judul} - {$codeData}]",
    //             'code_company' => $admin->code_company,
    //         ]);

    //         Activity::create([
    //             'id'           => Str::uuid(),
    //             'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
    //             'code_user'    => $admin->code_data,
    //             'activity'     => "Tambah data arsip [{$request->judul} - {$codeData}]",
    //             'code_company' => $admin->code_company,
    //         ]);

    //         DB::commit();
    //         return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => $arsip], 201);

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
    //     }
    // }

    public function savearsip(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();

        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['dataarsip', 'newarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $validator = Validator::make($request->all(), [
            'code_kategori'   => 'required|string|max:200',
            'judul'           => 'required|string|max:200',
            'tanggal_dokumen' => 'required|string|max:200',
            'deskripsi'       => 'required|string|max:200',
            'akses'           => 'required|string|max:200',
            'file_path'       => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => [] ], 422);
        }

        try {
            DB::beginTransaction();

            // =====================================================
            // UPLOAD FILE ke storage/app/public/arsip
            // path yang tersimpan di DB: "arsip/namafile.pdf"
            // akses publik via: /storage/arsip/namafile.pdf
            // =====================================================
            // $uploadedPath = $request->file('file_path')->store('arsip', 'public');

            $file = $request->file('file_path');
            $fileName = Str::slug($request->judul) . '_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $uploadedPath = $file->storeAs('arsip', $fileName, 'public');

            if (!$uploadedPath) {
                DB::rollBack();
                return response()->json(['status_message' => 'error','note' => 'Gagal mengupload file','results' => []], 500);
            }

            $codeData = 'ARS' . ltrim(now()->format('YmdHis') . Str::random(1), '0');

            $arsip = Arsip::create([
                'id'              => Str::uuid(),
                'code_data'       => $codeData,
                'code_kategori'   => $request->code_kategori,
                'judul'           => $request->judul,
                'tanggal_dokumen' => $request->tanggal_dokumen,
                'deskripsi'       => $request->deskripsi,
                'akses'           => $request->akses,
                'file_path'       => $uploadedPath, // ← "arsip/namafile.pdf"
                'code_user'       => $admin->code_data,
                'code_company'    => $admin->code_company,
            ]);

            ArsipLog::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_arsip'   => $codeData,
                'code_user'    => $admin->code_data,
                'aksi'         => "Tambah data arsip [{$request->judul} - {$codeData}]",
                'code_company' => $admin->code_company,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Tambah data arsip [{$request->judul} - {$codeData}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => $arsip], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file yang sudah terupload jika DB gagal
            if (!empty($uploadedPath) && Storage::disk('public')->exists($uploadedPath)) {
                Storage::disk('public')->delete($uploadedPath);
            }

            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    public function viewarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['kategoriarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $arsip = Arsip::with('kategori')->where('code_data', $request->code_data)->where('code_company', $admin->code_company)->first();
        if (!$arsip) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['arsip' => $arsip, 'count_used' => 0]], 200);
    }

    public function editarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['kategoriarsip', 'editkategoriarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $arsip = Arsip::where('code_data', $request->code_data)->where('code_company', $admin->code_company)->first();
        if (!$arsip) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        $validator = Validator::make($request->all(), [
            'code_kategori'   => 'required|string|max:200',
            'judul'           => 'required|string|max:200',
            'tanggal_dokumen' => 'required|string|max:200',
            'deskripsi'       => 'required|string|max:200',
            'akses'           => 'required|string|max:200',
            'file_path'       => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => [] ], 422);
        }

        // try {
        //     DB::beginTransaction();

        //     // Upload file baru jika ada
        //     if ($request->hasFile('file_path')) {
        //         $uploaded = $request->file('file_path')->store('arsip', 'public');
        //         if (!$uploaded) {
        //             throw new \Exception('Gagal upload file.');
        //         }
        //         $newFile = $uploaded;
        //     }
            
        //     $uploadedPath = $request->file('file_path')->store('arsip', 'public');

        //     if (!$uploadedPath) {
        //         DB::rollBack();
        //         return response()->json(['status_message' => 'error','note' => 'Gagal mengupload file','results' => []], 500);
        //     }

        //     $arsip->update([
        //         'code_kategori'   => $request->code_kategori,
        //         'judul'           => $request->judul,
        //         'tanggal_dokumen' => $request->tanggal_dokumen,
        //         'deskripsi'       => $request->deskripsi,
        //         'akses'           => $request->akses,
        //         'file_path'       => $uploadedPath, // ← "arsip/namafile.pdf"
        //         'code_user'       => $admin->code_data,
        //         'code_company'    => $admin->code_company,
        //     ]);

        //     ArsipLog::create([
        //         'id'           => Str::uuid(),
        //         'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
        //         'code_arsip'   => $codeData,
        //         'code_user'    => $admin->code_data,
        //         'aksi'         => "Ubah data arsip [{$arsip->judul} - {$arsip->judul}]",
        //         'code_company' => $admin->code_company,
        //     ]);

        //     Activity::create([
        //         'id'           => Str::uuid(),
        //         'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
        //         'code_user'    => $admin->code_data,
        //         'activity'     => "Ubah data arsip [{$arsip->judul} - {$arsip->judul}]",
        //         'code_company' => $admin->code_company,
        //     ]);

        //     DB::commit();
        //     return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        // } catch (\Throwable $e) {
        //     DB::rollBack();
        //     return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        // }

        try {
            DB::beginTransaction();

            $oldTitle = $arsip->judul;
            $oldFile  = $arsip->file_path;

            $newFile = $oldFile;

            // Upload file baru jika ada
            if ($request->hasFile('file_path')) {
                // $uploaded = $request->file('file_path')->store('arsip', 'public');
                $file = $request->file('file_path');
                $fileName = Str::slug($request->judul) . '_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                $uploaded = $file->storeAs('arsip', $fileName, 'public');
                if (!$uploaded) {
                    throw new \Exception('Gagal upload file.');
                }
                $newFile = $uploaded;
            }

            $arsip->update([
                'code_kategori'   => $request->code_kategori,
                'judul'           => $request->judul,
                'tanggal_dokumen' => $request->tanggal_dokumen,
                'deskripsi'       => $request->deskripsi,
                'akses'           => $request->akses,
                'file_path'       => $newFile,
                'code_user'       => $admin->code_data,
                'code_company'    => $admin->code_company,
            ]);

            ArsipLog::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis').Str::random(1),'0'),
                'code_arsip'   => $arsip->code_data,
                'code_user'    => $admin->code_data,
                'aksi'         => "Ubah data arsip [{$oldTitle} - {$request->judul}]",
                'code_company' => $admin->code_company,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis').Str::random(1),'0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data arsip [{$oldTitle} - {$request->judul}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();

            // Hapus file lama setelah database berhasil diupdate
            if (
                $request->hasFile('file_path') &&
                $oldFile &&
                Storage::disk('public')->exists($oldFile)
            ) {
                Storage::disk('public')->delete($oldFile);
            }

            return response()->json(['status_message' => 'success','note' => 'Data berhasil diperbarui.','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();

            // Jika upload file baru berhasil tetapi DB gagal,
            // hapus file baru agar tidak menjadi sampah.
            if (
                isset($newFile) &&
                $newFile !== $oldFile &&
                Storage::disk('public')->exists($newFile)
            ) {
                Storage::disk('public')->delete($newFile);
            }

            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }
    
    public function deletearsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['dataarsip', 'deletearsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $arsip = Arsip::where('code_data', $request->code_data)->where('code_company', $admin->code_company)->first();
        if (!$arsip) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();
            $filePath = $arsip->file_path;
            $arsip->delete();

            ArsipLog::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis').Str::random(1),'0'),
                'code_arsip'   => $arsip->code_data,
                'code_user'    => $admin->code_data,
                'aksi'         => "Hapus data arsip [{$arsip->judul} - {$arsip->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis').Str::random(1),'0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Hapus data arsip [{$arsip->judul} - {$arsip->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();

            // Hapus file setelah database berhasil dihapus
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return response()->json(['status_message' => 'success','note' => 'Data berhasil dihapus','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    // KategoriArsip    
    public function kategoriarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
        }

        $menus = ['earsip', 'kategoriarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }        
            
        $query = KategoriArsip::query();

        if ($request->filled('search')) {
            $query->where('nama_kategori', 'Ilike', "%{$request->search}%")
                ->orWhere('code_data', 'Ilike', "%{$request->search}%");
        }

        $allowedSort = ['created_at', 'code_data', 'nama_kategori'];
        $sortBy = in_array($request->sort_by, $allowedSort) ? $request->sort_by : 'created_at';
        $sortOrder = $request->sort_order === 'desc' ? 'desc' : 'asc';

        $data = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate((int) $request->per_page);

        return response()->json(['status_message'=>'success','note'=>'Proses data berhasil','results'=> $data],200);
    }

    public function savekategoriarsip(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['kategoriarsip', 'newkategoriarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:200',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\KategoriArsip::whereRaw(
                        'LOWER(nama_kategori) = ?',
                        [strtolower(trim($value))]
                    )->exists();

                    if ($exists) {
                        $fail('Nama kategori sudah terdaftar.');
                    }
                }
            ]
        ]);

        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:200',
            'keterangan'    => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $lastData  = KategoriArsip::orderByDesc('created_at')->first();
            $lastNumber = $lastData ? (int) substr($lastData->code_data, -4) : 0;
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $otp        = random_int(1000, 9999);
            $codeData  = "KA{$otp}{$newNumber}";

            $kategoriArsip = KategoriArsip::create([
                'id'            => Str::uuid(),
                'code_data'     => $codeData,
                'nama_kategori' => $request->nama_kategori,
                'keterangan'    => $request->keterangan,
                'code_company'  => $admin->code_company,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Tambah data kategori arsip [{$request->nama_kategori} - {$codeData}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => $kategoriArsip], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewkategoriarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['kategoriarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $kategoriarsip = KategoriArsip::where('code_data', $request->code_data)->first();
        if (!$kategoriarsip) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['kategoriarsip' => $kategoriarsip, 'count_used' => 0]], 200);
    }

    public function editkategoriarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['kategoriarsip', 'editkategoriarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $kategoriarsip = KategoriArsip::where('code_data', $request->code_data)->first();
        if (!$kategoriarsip) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:200',
                function ($attribute, $value, $fail) use ($request) {

                    $exists = \App\Models\KategoriArsip::whereRaw(
                            'LOWER(nama_kategori) = ?',
                            [strtolower(trim($value))]
                        )
                        ->where('code_data', '!=', $request->code_data)
                        ->exists();

                    if ($exists) {
                        $fail('Nama kategori sudah terdaftar.');
                    }
                }
            ]
        ]);

        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:200',
            'keterangan'    => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $kategoriarsip->update([
                'nama_kategori' => ucfirst($request->nama_kategori),
                'keterangan'    => $request->keterangan,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data kategori arsip [{$kategoriarsip->nama_kategori} - {$kategoriarsip->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }
    
    public function deletekategoriarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['kategoriarsip', 'deletekategoriarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $kategoriarsip = KategoriArsip::where('code_data', $request->code_data)->first();
        if (!$kategoriarsip) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();
            $kategoriarsip->delete();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Hapus data kategori arsip [{$kategoriarsip->nama_kategori} - {$kategoriarsip->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil dihapus','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    // ArsipTag
    public function arsiptags(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
        }

        $menus = ['earsip', 'arsiptags'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }        
            
        $query = ArsipTag::query();

        if ($request->filled('search')) {
            $query->where('nama_tag', 'Ilike', "%{$request->search}%")
                ->orWhere('code_data', 'Ilike', "%{$request->search}%");
        }

        $allowedSort = ['created_at', 'code_data', 'nama_tag'];
        $sortBy = in_array($request->sort_by, $allowedSort) ? $request->sort_by : 'created_at';
        $sortOrder = $request->sort_order === 'desc' ? 'desc' : 'asc';

        $data = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate((int) $request->per_page);

        return response()->json(['status_message'=>'success','note'=>'Proses data berhasil','results'=> $data],200);
    }

    public function savearsiptags(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['arsiptags', 'newarsiptags'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $request->validate([
            'nama_tag' => [
                'required',
                'string',
                'max:200',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\ArsipTag::whereRaw(
                        'LOWER(nama_tag) = ?',
                        [strtolower(trim($value))]
                    )->exists();

                    if ($exists) {
                        $fail('Nama tag sudah terdaftar.');
                    }
                }
            ]
        ]);

        $validator = Validator::make($request->all(), [
            'nama_tag'   => 'required|string|max:200|unique:db_arsip_tags,nama_tag',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $lastData  = ArsipTag::orderByDesc('created_at')->first();
            $lastNumber = $lastData ? (int) substr($lastData->code_data, -4) : 0;
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $otp        = random_int(1000, 9999);
            $codeData  = "AT{$otp}{$newNumber}";

            $arsipTag = ArsipTag::create([
                'id'            => Str::uuid(),
                'code_data'     => $codeData,
                'nama_tag'      => ucfirst($request->nama_tag),
                'code_company'  => $admin->code_company,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Tambah data arsip tag [{$request->nama_tag} - {$codeData}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => $arsipTag], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewarsiptags(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['arsiptags'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $arsipTag = ArsipTag::where('code_data', $request->code_data)->first();
        if (!$arsipTag) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['arsiptags' => $arsipTag, 'count_used' => 0]], 200);
    }

    public function editarsiptags(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['arsiptags', 'editarsiptags'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $arsipTag = ArsipTag::where('code_data', $request->code_data)->first();
        if (!$arsipTag) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }  

        $request->validate([
            'nama_tag' => [
                'required',
                'string',
                'max:200',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = \App\Models\ArsipTag::whereRaw(
                        'LOWER(nama_tag) = ?',
                        [strtolower(trim($value))]
                    )
                    ->where('code_data', '!=', $request->code_data)
                    ->exists();

                    if ($exists) {
                        $fail('Nama tag sudah terdaftar.');
                    }
                }
            ]
        ]);

        $validator = Validator::make($request->all(), [
            'nama_tag' => 'required|string|max:200',
        ]);  

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        } 

        try {
            DB::beginTransaction();

            $arsipTag->update([
                'nama_tag' => ucfirst($request->nama_tag),
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data arsip tag [{$arsipTag->nama_tag} - {$arsipTag->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }
    
    public function deletearsiptags(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['arsiptags', 'deletearsiptags'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $arsipTag = ArsipTag::where('code_data', $request->code_data)->first();
        if (!$arsipTag) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();
            $arsipTag->delete();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Hapus data arsip tag [{$arsipTag->nama_tag} - {$arsipTag->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil dihapus','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    // KlasifikasiArsip
    public function klasifikasiarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'User tidak valid'], 401);
        }

        $menus = ['earsip', 'klasifikasiarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }        
            
        $query = KlasifikasiArsip::query();

        if ($request->filled('search')) {
            $query->where('nama_klasifikasi', 'Ilike', "%{$request->search}%")
                ->orWhere('code_klasifikasi', 'Ilike', "%{$request->search}%")
                ->orWhere('code_data', 'Ilike', "%{$request->search}%");
        }

        $allowedSort = ['created_at', 'code_data', 'nama_klasifikasi', 'retensi_aktif', 'retensi_inaktif'];
        $sortBy = in_array($request->sort_by, $allowedSort) ? $request->sort_by : 'created_at';
        $sortOrder = $request->sort_order === 'desc' ? 'desc' : 'asc';

        $data = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate((int) $request->per_page);

        return response()->json(['status_message'=>'success','note'=>'Proses data berhasil','results'=> $data],200);
    }

    public function saveklasifikasiarsip(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['klasifikasiarsip', 'newklasifikasiarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $request->validate([
            'nama_klasifikasi' => [
                'required',
                'string',
                'max:200',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\KlasifikasiArsip::whereRaw(
                        'LOWER(nama_klasifikasi) = ?',
                        [strtolower(trim($value))]
                    )->exists();

                    if ($exists) {
                        $fail('Nama klasifikasi sudah terdaftar.');
                    }
                }
            ]
        ]);

        $validator = Validator::make($request->all(), [
            'code_klasifikasi'  => 'required|string|max:200|unique:db_klasifikasi_arsip,nama_klasifikasi',
            'nama_klasifikasi'  => 'required|string|max:200|unique:db_klasifikasi_arsip,nama_klasifikasi',
            'deskripsi'         => 'required|string|max:200',
            'retensi_aktif'     => 'required|string|max:200',
            'retensi_inaktif'   => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $lastData  = KlasifikasiArsip::orderByDesc('created_at')->first();
            $lastNumber = $lastData ? (int) substr($lastData->code_data, -4) : 0;
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $otp        = random_int(1000, 9999);
            $codeData  = "KA{$otp}{$newNumber}";

            $klasifikasiArsip = KlasifikasiArsip::create([
                'id'                => Str::uuid(),
                'code_data'         => $codeData,
                'code_klasifikasi'  => $request->code_klasifikasi,
                'nama_klasifikasi'  => ucfirst($request->nama_klasifikasi),
                'deskripsi'         => $request->deskripsi,
                'retensi_aktif'     => $request->retensi_aktif,
                'retensi_inaktif'   => $request->retensi_inaktif,
                'code_company'      => $admin->code_company,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Tambah data klasifikasi arsip [{$request->nama_tag} - {$codeData}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => $klasifikasiArsip], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewklasifikasiarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['klasifikasiarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $klasifikasiArsip = KlasifikasiArsip::where('code_data', $request->code_data)->first();
        if (!$klasifikasiArsip) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['klasifikasiArsip' => $klasifikasiArsip, 'count_used' => 0]], 200);
    }

    public function editklasifikasiarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['klasifikasiarsip', 'editklasifikasiarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $klasifikasiArsip = KlasifikasiArsip::where('code_data', $request->code_data)->first();
        if (!$klasifikasiArsip) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }  
        
        $request->validate([
            'nama_klasifikasi' => [
                'required',
                'string',
                'max:200',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = \App\Models\KlasifikasiArsip::whereRaw(
                        'LOWER(nama_klasifikasi) = ?',
                        [strtolower(trim($value))]
                    )
                    ->where('code_data', '!=', $request->code_data)
                    ->exists();

                    if ($exists) {
                        $fail('Nama klasifikasi sudah terdaftar.');
                    }
                }
            ]
        ]);

        $validator = Validator::make($request->all(), [
            'code_klasifikasi' => 'required|string|max:200',
            'nama_klasifikasi' => 'required|string|max:200',
            'deskripsi'         => 'required|string|max:200',
            'retensi_aktif'     => 'required|string|max:200',
            'retensi_inaktif'   => 'required|string|max:200',
        ]);  

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error', 'note' => $validator->errors()->first(), 'results' => []], 422);
        } 

        try {
            DB::beginTransaction();

            $klasifikasiArsip->update([
                'code_klasifikasi' => $request->code_klasifikasi,
                'nama_klasifikasi' => ucfirst($request->nama_klasifikasi),
                'deskripsi'        => $request->deskripsi,
                'retensi_aktif'    => $request->retensi_aktif,
                'retensi_inaktif'  => $request->retensi_inaktif,
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data klasifikasi arsip [{$klasifikasiArsip->nama_klasiifkasi} - {$klasifikasiArsip->code_data}]",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }
    
    public function deleteklasifikasiarsip(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['klasifikasiarsip', 'deleteklasifikasiarsip'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(function ($menu) use ($access) {
            return ($access[$menu] ?? 'No') === 'No';
        });

        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $klasifikasiArsip = KlasifikasiArsip::where('code_data', $request->code_data)->first();
        if (!$klasifikasiArsip) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();
            $klasifikasiArsip->delete();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Hapus data klasifikasi arsip [{$klasifikasiArsip->nama_klasifikasi} - {$klasifikasiArsip->code_data}]",
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