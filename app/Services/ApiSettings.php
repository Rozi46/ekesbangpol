<?php

namespace App\Services;

use App\Models\{Setting, Company, User, LevelAdmin, ListAkses, Activity};
use Illuminate\Http\{Request, UploadedFile, Response};
use Illuminate\Support\Facades\{Hash, Validator, File, Http, Route, Session, Auth, DB, Lang};
use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Query\Builder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiSettings
{
    // Pengaturan Menu & Akses
    public function getsetting($request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if(!$viewadmin){ 
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);   
        }

        $results['data_setting'] = Setting::find(1);        
        return response()->json(['status_message' => 'success','results' => $results], 201);
    } 

    public function getlevelakses(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        // AMBIL SEMUA DATA
        $allAkses = ListAkses::get();

        // FILTER & SORT
        $menus = $allAkses
            ->where('menu', 'Yes')
            ->sortByDesc('no_urut') // MENU DESC
            ->values();

        $results = [
            'menu' => [],
            'submenu' => [],
            'action' => [],
            'subaction' => [],
            'count_used' => []
        ];

        foreach ($menus as $menu) {
            // MENU
            $results['menu'][] = $menu;

            // SUBMENU (ASC)
            $submenus = $allAkses
                ->where('menu_index', $menu->id)
                ->where('submenu', 'Yes')
                ->sortBy('no_urut') // ASC
                ->values();

            $results['submenu'][$menu->id] = $submenus;
            $results['count_used'][$menu->id] = $allAkses->where('menu_index', $menu->id)->count();

            foreach ($submenus as $submenu) {
                // ACTION (ASC)
                $actions = $allAkses
                    ->where('menu_index', $submenu->id)
                    ->where('action', 'Yes')
                    ->sortBy('no_urut') // ASC
                    ->values();

                $results['action'][$submenu->id] = $actions;

                foreach ($actions as $action) {
                    // SUBACTION (ASC)
                    $subactions = $allAkses
                        ->where('menu_index', $action->id)
                        ->where('subaction', 'Yes')
                        ->sortBy('no_urut') // ASC
                        ->values();

                    $results['subaction'][$action->id] = $subactions;
                }
            }
        }

        return response()->json(['status_message' => 'success','results' => $results], 201);
    }

    public function listoplevel(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        // VALIDASI AKSES (RBAC)
        $menus = ['users', 'listusers', 'newusers'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');
        $hasAccess =($access['users'] ?? 'No') === 'Yes' && ($access['listusers'] ?? 'No') === 'Yes' && ($access['newusers'] ?? 'No') === 'Yes';
        if (!$hasAccess) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        // GET DATA LEVEL
        $results = DB::table('db_level_admin')
            ->select('level_name', 'code_data')
            ->groupBy('level_name', 'code_data')
            ->orderBy('level_name', 'ASC')
            ->get();

        return response()->json(['status_message' => 'success','results' => $results], 201);
    }

    public function actionsettingmenu(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();

        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        if (($viewadmin->level ?? null) !== 'LV5677001') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $validator = Validator::make($request->all(), [
            'no_urut'     => 'required|numeric',
            'nama_menu'   => 'required|string|max:200',
            'nama_akses'  => 'required|string|max:200|unique:db_list_akses,nama_akses',
            'type_menu'   => 'required|in:Menu,SubMenu,Action,SubAction',
            'menu_index'  => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => []], 422);
        }

        $typeMap = [
            'Menu'      => ['menu' => 'Yes', 'submenu' => 'No',  'action' => 'No',  'subaction' => 'No'],
            'SubMenu'   => ['menu' => 'No',  'submenu' => 'Yes', 'action' => 'No',  'subaction' => 'No'],
            'Action'    => ['menu' => 'No',  'submenu' => 'No',  'action' => 'Yes', 'subaction' => 'No'],
            'SubAction' => ['menu' => 'No',  'submenu' => 'No',  'action' => 'No',  'subaction' => 'Yes'],
        ];

        $flags = $typeMap[$request->type_menu];

        if ($request->type_menu !== 'Menu' && empty($request->menu_index)) {
            return response()->json(['status_message' => 'error','note' => 'menu_index wajib diisi untuk selain Menu'], 422);
        }

        $iconMenu = $request->icon_menu ?: 'fa fa-align-right';
        $namaAkses = Str::of($request->nama_akses)->replace(' ', '');

        try {
            DB::beginTransaction();

            $menu = ListAkses::create([
                'id'          => Str::uuid(),
                'no_urut'     => $request->no_urut,
                'nama_menu'   => $request->nama_menu,
                'nama_akses'  => $namaAkses,
                'menu_index'  => $request->menu_index,
                'menu'        => $flags['menu'],
                'submenu'     => $flags['submenu'],
                'action'      => $flags['action'],
                'subaction'   => $flags['subaction'],
                'icon_menu'   => $iconMenu,
                'status_data' => 'Aktif',
            ]);

            $levels = LevelAdmin::select('level_name', 'code_data')->groupBy('level_name', 'code_data')->get();

            foreach ($levels as $level) {
                LevelAdmin::create([
                    'id'            => Str::uuid(),
                    'code_data'     => $level->code_data,
                    'level_name'    => $level->level_name,
                    'data_menu'     => $namaAkses,
                    'access_rights' => $level->code_data === 'LV5677001' ? 'Yes' : 'No'
                ]);
            }

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','id_data' => $menu->id,'results' => $menu], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),], 500);
        }
    }

    public function delmenu(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();

        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        if (($viewadmin->level ?? null) !== 'LV5677001') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $menu = ListAkses::find($request->id);

        if (!$menu) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();
            
            $this->deleteMenuRecursive($menu);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil dihapus','results' => []], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Gagal menghapus data: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    protected function deleteMenuRecursive($menu)
    {
        // ambil semua child
        $children = ListAkses::where('menu_index', $menu->id)->get();

        foreach ($children as $child) {
            $this->deleteMenuRecursive($child);
        }

        // hapus relasi level
        LevelAdmin::where('data_menu', $menu->nama_akses)->delete();

        // hapus menu itu sendiri
        $menu->delete();
    }

    // Company
    public function listcompany(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid','results' => []], 401);
        }

        $vd = (int) ($request->vd ?? 20);
        

        $keysearch = trim($request->keysearch ?? '');

        $query = Company::query();

        if ($keysearch !== '') {
            $query->where(function ($q) use ($keysearch) {
                $q->where('code_data', 'ILIKE', "%{$keysearch}%")
                ->orWhere('nama_company', 'ILIKE', "%{$keysearch}%")
                ->orWhere('jenis', 'ILIKE', "%{$keysearch}%")
                ->orWhere('alamat', 'ILIKE', "%{$keysearch}%")
                ->orWhere('email', 'ILIKE', "%{$keysearch}%");
            });
        }

        $listdata = $query->orderBy('created_at', 'ASC')->paginate($vd);
        $companyCodes = $listdata->pluck('code_data');
        $userCounts = User::whereIn('code_company', $companyCodes)
            ->selectRaw('code_company, COUNT(*) as total')
            ->groupBy('code_company')
            ->pluck('total', 'code_company');

        $count_used = [];
        foreach ($listdata as $data) {
            $count_used[$data->id] = $userCounts[$data->code_data] ?? 0;
        }

        return response()->json([
            'status_message'   => 'success',
            'note'             => 'Proses data berhasil',
            'count_all_data'   => $listdata->total(),
            'count_view_data'  => $vd,
            'keysearch'        => $keysearch,
            'results' => [
                'listdata'    => $listdata,
                'count_used'  => $count_used
            ]
        ], 201);
    }

    public function savecompany(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid','results' => []], 401);
        }

        $validator = Validator::make($request->all(), [
            'nama'   => 'required|string|max:200',
            'jenis'  => 'required|string|max:200',
            'alamat' => 'required|string|max:200',
            'email'  => 'required|email|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => []], 422);
        }

        try {
            DB::beginTransaction();
            
            $lastCompany = Company::orderByDesc('created_at')->first();
            $lastNumber = $lastCompany ? (int) substr($lastCompany->code_data, -4) : 0;
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $otp = random_int(1000, 9999);
            $codeCompany = "CP{$otp}{$newNumber}";

            $company = Company::create([
                'id'           => Str::uuid(),
                'code_data'    => $codeCompany,
                'nama_company' => $request->nama,
                'jenis'        => $request->jenis,
                'alamat'       => $request->alamat,
                'email'        => $request->email,
                'keterangan'   => 'SERVER',
                'foto'         => null,
            ]);

            if (!$company) {
                throw new \Exception('Data gagal disimpan');
            }

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => now()->format('YmdHis') . Str::random(1),
                'code_user'    => $viewadmin->code_data,
                'activity'     => "Tambah data perusahaan [{$company->nama_company}]",
                'code_company' => $viewadmin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => $company], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    public function viewcompany(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();

        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid','results' => []], 401);
        }

        if (($viewadmin->level ?? null) !== 'LV5677001') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $company = Company::where('id', $request->id)
            ->withCount(['user as count_used' => function ($q) {
                $q->select(DB::raw('count(*)'));
            }])
            ->first();

        if (!$company) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => [] ], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['nama_company' => $company],'count_used' => $company->count_used], 201);
    }

    public function editcompany(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if(!$viewadmin){ 
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid','results' => []], 401);
        }

        $get_data['company'] = Company::where('id', $request->id_data)->first();

        $validator = Validator::make($request->all(), [
            'code_company'  => 'required|string|max:100',
            'nama_company'  => 'required|string|max:100',
            'jenis_company' => 'required|string|max:100',
            'alamat_company'=> 'required|string|max:100',
            'email_company' => 'required|string|email|max:200',
            'logo_company'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => []], 422);
        }

        try {
            DB::beginTransaction();
            
            $update = Company::where('id', $request->id_data)
                ->update([
                    'code_data'     => $request->get('code_company'),
                    'nama_company'  => $request->get('nama_company'),
                    'jenis'         => $request->get('jenis_company'),
                    'alamat'        => $request->get('alamat_company'),
                    'email'         => $request->get('email_company'),
                ]);

            // if (!$update) {
            //     DB::rollBack();
            //     return response()->json(['status_message' => 'error','note' => 'Data gagal disimpan','results' => []], 500);
            // }

            if (!$update) {
                throw new \Exception('Data gagal disimpan');
            }


            if ($request->hasFile('logo_company')) {
                $imageName = 'PK-' . $request->code_company . '-' . time() . '.' . $request->logo_company->extension();
                $request->logo_company->move(public_path('/themes/admin/AdminOne/image/public/'), $imageName);

                Company::where('id', $request->id_data)->update(['foto' => $imageName]);

                if (!empty($get_data['company']->foto)) {
                    File::delete(public_path('/themes/admin/AdminOne/image/public/' . $get_data['company']->foto));
                }

                $file = $request->file('logo_company');
                $filenameOriginal = $file->getClientOriginalName();
            }

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(Carbon::now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $viewadmin->code_data ?? null,
                'activity'     => 'Update data perusahaan [' . $request->get('nama_company') . ']',
                'code_company' => $viewadmin->code_company ?? null,
            ]);

            DB::commit();
            return response()->json([ 'status_message' => 'success','note' => 'Data berhasil disimpan','results' => $filenameOriginal ?? null], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    public function deletecompany(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();

        if (!$viewadmin) { 
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid','results' => []], 401);
        }

        $company = Company::where('id', $request->id)->first();
        if (!$company) {
            return response()->json(['status_message' => 'error', 'note' => 'Data tidak ditemukan','results' => []], 404);
        }

        try {
            DB::beginTransaction();

            $oldFoto = $company->foto;
            $companyName = $company->nama_company;
            $companyCode = $company->code_data;
            
            Company::where('id', $request->id)->delete();

            if (!empty($oldFoto)) {
                $path = public_path('/themes/admin/AdminOne/image/public/' . $oldFoto);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            Activity::create([
                'id'            => Str::uuid(),
                'code_data'    => ltrim(Carbon::now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'     => $viewadmin->code_data ?? null,
                'activity'      => 'Hapus data perusahaan [' . $companyName . ' - ' . $companyCode . ']',
                'code_company'  => $viewadmin->code_company ?? null,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil dihapus','results' => []], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    // Manual Book
    public function viewManualBook(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();

        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid','results' => []], 401);
        }

        if (($viewadmin->level ?? null) !== 'LV5677001') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $manualbook = Setting::find(1);

        if (!$manualbook) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => [] ], 404);
        }

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => ['setting' => $manualbook]], 201);
    }

    public function uploadmanualbook(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();

        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid','results' => []], 401);
        }

        if (($viewadmin->level ?? null) !== 'LV5677001') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $validator = Validator::make($request->all(), [
            'manual_book' => 'required|file|mimes:pdf,doc,docx|max:20480', // 20MB            
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => []], 422);
        }

        $setting = Setting::find(1);
        if (!$setting) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }


        try {
            DB::beginTransaction();

            // FILE PATH
            $path = public_path('themes/admin/AdminOne/ManualBook/');

            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // DELETE OLD FILE
            if ($setting->manual_book) {
                $oldPath = $path . $setting->manual_book;
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // UPLOAD FILE
            $file = $request->file('manual_book');
            $filename = 'MB-' . time() . '.' . $file->extension();
            $file->move($path, $filename);

            // UPDATE DB
            $setting->update([
                'manual_book' => $filename,
            ]);

            Activity::create([
                'id'            => Str::uuid(),
                'code_data'     => ltrim(Carbon::now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'     => $viewadmin->code_data,
                'activity'      => 'Ubah manual book aplikasi',
                'code_company'  => $viewadmin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => $filename]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }

    }

    public function downloadmanualbook(Request $request)
    {
        $filename = basename($request->get('d')); // 🔒 prevent path traversal
        $filePath = public_path("themes/admin/AdminOne/ManualBook/{$filename}");

        if (!file_exists($filePath)) {
            return response()->json(['status_message' => 'error','note' => 'File tidak ditemukan','results' => []], 404);
        }

        return response()->download($filePath);
    }

}