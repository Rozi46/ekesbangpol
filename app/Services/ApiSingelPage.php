<?php

namespace App\Services;

use App\Models\{Setting, Company, User, LevelAdmin, ListAkses, Activity, SingelPage};
use Illuminate\Http\{Request, UploadedFile, Response};
use Illuminate\Support\Facades\{Hash, Validator, File, Http, Route, Session, Auth, DB, Lang};
use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Query\Builder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiSingelPage
{ 
    public function viewvisimisi(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['datavisimisi'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $singelPage = SingelPage::where('code_data', 'VM20260521103936Z')->where('code_company', $admin->code_company)->first();

        if (!$singelPage) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['singelpage' => $singelPage]], 200);
    }

    public function updatevisimisi(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['datavisimisi', 'editvisimisi'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $singelPage = SingelPage::where('code_data', $request->code_data)->where('code_company', $admin->code_company)->first();
        if (!$singelPage) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        try {
            DB::beginTransaction();

            $singelPage->update([
                'isi_page'    => $request->isi
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data visi dan misi",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewtentang(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['datatentang'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $singelPage = SingelPage::where('code_data', 'TT20260521104105I')->where('code_company', $admin->code_company)->first();

        if (!$singelPage) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['singelpage' => $singelPage]], 200);
    }

    public function updatetentang(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['datatentang', 'edittentang'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $singelPage = SingelPage::where('code_data', $request->code_data)->where('code_company', $admin->code_company)->first();
        if (!$singelPage) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        try {
            DB::beginTransaction();

            $singelPage->update([
                'isi_page'    => $request->isi
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data tentang",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewtugasfungsi(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['datatugasfungsi'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $singelPage = SingelPage::where('code_data', 'TF20260521104005I')->where('code_company', $admin->code_company)->first();

        if (!$singelPage) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['singelpage' => $singelPage]], 200);
    }

    public function updatetugasfungsi(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['datatugasfungsi', 'edittugasfungsi'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $singelPage = SingelPage::where('code_data', $request->code_data)->where('code_company', $admin->code_company)->first();
        if (!$singelPage) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        try {
            DB::beginTransaction();

            $singelPage->update([
                'isi_page'    => $request->isi
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data tugas dan fungsi",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewstrukturorganisasi(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['datatugasfungsi'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $strukturOrganisasi = Setting::where('code_data','ST20260521111536S')->where('code_company',$admin->code_company)->first();

        if (!$strukturOrganisasi) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['strukturOrganisasi' => $strukturOrganisasi]], 200);
    }

    public function updatestrukturorganisasi(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['datastrukturorganisasi', 'editstrukturorganisasi'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $strukturOrganisasi = Setting::where('code_data','ST20260521111536S')->where('code_company',$admin->code_company)->first();

        if (!$strukturOrganisasi) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        try {
            DB::beginTransaction();

            $photoName = $strukturOrganisasi->file_struktur_organisasi;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $photoName = $strukturOrganisasi->code_data . time() . $file->getClientOriginalName();
                $file->move(public_path('/image/post/'),$photoName);

                if (!empty($strukturOrganisasi->file_struktur_organisasi)) {
                    File::delete(public_path('/image/post/' . $strukturOrganisasi->file_struktur_organisasi));
                }
            }

            $strukturOrganisasi->update([
                'file_struktur_organisasi'   => $photoName
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data struktur organisasi",
                'code_company' => $admin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success', 'note' => 'Data berhasil disimpan', 'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error', 'note' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []], 500);
        }
    }

    public function viewalamatkontak(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'User tidak valid'], 401);
        }

        $menus  = ['dataalamatkontak'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $singelPage = SingelPage::where('code_data', 'AK20260521104205I')->where('code_company', $admin->code_company)->first();

        if (!$singelPage) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['singelpage' => $singelPage]], 200);
    }

    public function updatealamatkontak(Request $request)
    {
        $admin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$admin) {
            return response()->json(['status_message' => 'error', 'note' => 'Data user tidak valid'], 401);
        }

        $menus  = ['dataalamatkontak', 'editalamatkontak'];
        $access = LevelAdmin::where('code_data', $admin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        $hasNoAccess = collect($menus)->contains(fn($m) => ($access[$m] ?? 'No') === 'No');
        if ($hasNoAccess) {
            return response()->json(['status_message' => 'error', 'note' => 'Tidak ada akses', 'results' => []], 403);
        }

        $singelPage = SingelPage::where('code_data', $request->code_data)->where('code_company', $admin->code_company)->first();
        if (!$singelPage) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan', 'results' => []], 404);
        }

        try {
            DB::beginTransaction();

            $singelPage->update([
                'isi_page'    => $request->isi
            ]);

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $admin->code_data,
                'activity'     => "Ubah data alamat dan kontak",
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