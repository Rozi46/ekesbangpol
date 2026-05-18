<?php

namespace App\Services;

use App\Models\{Setting, Company, User, LevelAdmin, ListAkses, Activity};
use Illuminate\Http\{Request, UploadedFile, Response};
use Illuminate\Support\Facades\{Hash, Validator, File, Http, Route, Session, Auth, DB, Lang};
use Illuminate\Support\{Carbon, Str};
use Illuminate\Database\Query\Builder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiUsers
{
    // Data Pengguna
    public function listusers(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }
        
        $menus = ['users', 'listusers', 'exportusers'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');
        if (($access['users'] ?? 'No') === 'No' || ($access['listusers'] ?? 'No') === 'No') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => [] ], 403);
        }

        if ($request->type === 'export' && ($access['exportusers'] ?? 'No') === 'No') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $vd = (int) ($request->vd ?? 20);
        

        $query = User::with([
                'company:id,code_data,nama_company',
                'levelAdmin:id,code_data,level_name'
            ])
            ->where('tipe_login', 'User')
            ->where('code_company', $viewadmin->code_company);

        if ($viewadmin->level !== 'LV5677001') {
            $query->where('level', '!=', 'LV5677001');
        }

        if (!empty($request->keysearch)) {
            $search = $request->keysearch;

            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ILIKE', "%{$search}%")
                ->orWhere('code_data', 'ILIKE', "%{$search}%")
                ->orWhere('phone_number', 'ILIKE', "%{$search}%")
                ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $results = $query->orderBy('full_name', 'ASC')->paginate($vd);
        
        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','count_all_data' => $results->total(),'count_view_data' => $vd,'keysearch' => $request->keysearch,'results' => ['list' => $results]]);
    }

    public function saveusers(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $level = LevelAdmin::where('code_data', $request->level)->first();
        if (!$level) {return response()->json(['status_message' => 'error','note' => 'Data level tidak terdaftar'], 404);
        }

        $menus = ['listusers', 'newusers'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        if ( ($access['listusers'] ?? 'No') === 'No' || ($access['newusers'] ?? 'No') === 'No') { return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);}

        $validator = Validator::make($request->all(), [
            'full_name'     => 'required|string|max:200',
            'phone_number'  => 'required|string|max:200',
            'email'         => 'required|email|max:200|unique:db_users,email',
            'password'      => 'required|string|min:6|max:200',
            'level'         => 'required|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            $lastUser = User::orderByDesc('created_at')->first();
            $lastNumber = $lastUser ? (int) substr($lastUser->code_data, -4) : 0;
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $otp = random_int(1000, 9999);
            $codeUser = "US{$otp}{$newNumber}";

            $user = User::create([
                'id'            => Str::uuid(),
                'code_data'     => $codeUser,
                'full_name'     => $request->full_name,
                'email'         => $request->email,
                'phone_number'  => $request->phone_number,
                'password'      => bcrypt($request->password),
                'level'         => $request->level,
                'image'         => 'no_img',
                'status_data'   => 'Aktif',
                'tipe_user'     => 'User',
                'tipe_login'    => 'User',
                'code_company'  => $viewadmin->code_company,
            ]);

            $activityCode = ltrim(now()->format('YmdHis') . Str::random(1), '0');
            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => $activityCode,
                'code_user'    => $viewadmin->code_data,
                'activity'     => "Tambah data pengguna [{$request->full_name} - {$codeUser}]",
                'code_company' => $viewadmin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','results' => $user], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }
    
    public function viewusers(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['users', 'listusers'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');
        if ( ($access['users'] ?? 'No') === 'No' || ($access['listusers'] ?? 'No') === 'No' ) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $dataadmin = User::where('code_company', $viewadmin->code_company)->where('id', $request->id)->first();
        if (!$dataadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        $leveladmin = LevelAdmin::where('code_data', $dataadmin->level)->get();

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','results' => [['detailadmin' => [$dataadmin],'leveladmin' => [$leveladmin]]]], 200);
    }
    
    public function editusers(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $ceklevel = LevelAdmin::where('code_data', $request->level)->first();
        if (!$ceklevel) {
            return response()->json(['status_message' => 'error','note' => 'Data level tidak terdaftar'], 404);
        }

        $menus = ['listusers', 'editusers'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');
        if ( ($access['listusers'] ?? 'No') === 'No' || ($access['editusers'] ?? 'No') === 'No' ) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $user = User::where('code_company', $viewadmin->code_company)->where('id', $request->id)->first();
        if (!$user) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        if ($user->id === 'bd050931-d837-11eb-8038-204747ab6caa') {
            return response()->json(['status_message' => 'error','note' => 'Data tidak bisa ubah','results' => []], 403);
        }

        $rules = [
            'full_name'     => 'required|string|max:200',
            'phone_number'  => 'required|string|max:200',
            'email'         => 'required|email|max:200',
            'level'         => 'required|string|max:30',
            'status_data'   => 'required|string|max:30',
        ];

        // jika email berubah → validasi unique
        if ($request->email !== $user->email) {
            $rules['email'] .= '|unique:db_users,email';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors() ], 422);
        }

        try {
            DB::beginTransaction();
            $user->update([
                'full_name'     => $request->full_name,
                'phone_number'  => $request->phone_number,
                'email'         => $request->email,
                'level'         => $request->level,
                'status_data'   => $request->status_data,
            ]);

            $otp = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1);
            $code = ltrim(now()->format('YmdHis') . $otp, '0');

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => $code,
                'code_user'    => $viewadmin->code_data,
                'activity'     => "Ubah data pengguna [{$user->full_name} - {$user->code_data}]",
                'code_company' => $viewadmin->code_company,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','id_data' => $user->id,'results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }
    
    public function deleteusers(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $menus = ['listusers', 'deleteusers'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        if ( ($access['listusers'] ?? 'No') === 'No' || ($access['deleteusers'] ?? 'No') === 'No' ) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $user = User::where('code_company', $viewadmin->code_company)->where('id', $request->id)->first();
        if (!$user) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        if ($user->code_data === 'US35790001') {
            return response()->json(['status_message' => 'error','note' => 'Data gagal dihapus','results' => []], 403);
        }

        try {
            DB::beginTransaction();
            $user->delete();

            $code = ltrim(now()->format('YmdHis') . Str::random(1), '0');
            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => $code,
                'code_user'    => $viewadmin->code_data,
                'activity'     => "Hapus data pengguna [{$user->full_name} - {$user->code_data}]",
                'code_company' => $viewadmin->code_company,
            ]);

            if (!empty($user->image) && $user->image !== 'no_img') {
                $path = public_path('image/upload/' . $user->image);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil dihapus','results' => []]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    // Level Pengguna
    public function listlevelusers(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }
        
        $menus = ['users', 'levelusers'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');
        if (($access['users'] ?? 'No') === 'No' || ($access['levelusers'] ?? 'No') === 'No') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => [] ], 403);
        }

        $vd = (int) ($request->vd ?? 20);
        

        $query = DB::table('db_level_admin')->select('level_name', 'code_data');

        if ($viewadmin->level !== 'LV5677001') {
            $query->where('code_data', '!=', 'LV5677001');
        }

        if (!empty($request->keysearch)) {
            $search = $request->keysearch;

            $query->where(function ($q) use ($search) {
                $q->where('code_data', 'ILIKE', "%{$search}%")
                ->orWhere('level_name', 'ILIKE', "%{$search}%");
            });
        }

        $results = $query->groupBy('level_name', 'code_data')->orderBy('level_name', 'ASC')->paginate($vd);
        
        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','count_all_data' => $results->total(),'count_view_data' => $vd,'keysearch' => $request->keysearch,'results' => $results]);
    }    
    
    public function actionlevel(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', ['users', 'levelusers'])->pluck('access_rights', 'data_menu');

        if (($access['users'] ?? 'No') === 'No' || ($access['levelusers'] ?? 'No') === 'No') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses'], 403);
        }

        $isEdit = LevelAdmin::where('code_data', $request->code_data)->exists();
        $actionMenu = $isEdit ? 'editlevelusers' : 'newlevelusers';
        $actionAccess = LevelAdmin::where('code_data', $viewadmin->level)->where('data_menu', $actionMenu)->value('access_rights');

        if (($actionAccess ?? 'No') === 'No') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses'], 403);
        }

        $rules = [
            'level_name' => 'required|string|max:200|unique:db_level_admin,level_name'
        ];

        if ($isEdit) {
            $rules['level_name'] = 'required|string|max:200|unique:db_level_admin,level_name,' . $request->code_data . ',code_data';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            if ($isEdit) {
                $codeData = $request->code_data;
            } else {
                $lastNumber = (int) LevelAdmin::selectRaw("RIGHT(code_data,3) as num")->orderByDesc('num')->value('num');
                $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
                $otp = random_int(1000, 9999);
                $codeData = "LV{$otp}{$nextNumber}";
            }

            $menus = collect($request->except(['u', 'token', 'code_data', 'level_name', '_token']))->map(fn($v) => $v === 'Yes' ? 'Yes' : 'No');

            foreach ($menus as $data_menu => $accessValue) {
                LevelAdmin::updateOrCreate(
                    [
                        'code_data' => $codeData,
                        'data_menu' => $data_menu
                    ],
                    [
                        'id'            => Str::uuid(),
                        'level_name'    => $request->level_name,
                        'access_rights' => $accessValue
                    ]
                );
            }

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $viewadmin->code_data ?? null,
                'activity'     => $isEdit ? "Ubah data level pengguna [{$request->level_name} - {$codeData}]" : "Tambah data level pengguna [{$request->level_name} - {$codeData}]",
                'code_company' => $viewadmin->code_company ?? null
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil disimpan','id_data' => $codeData ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    } 
    
    public function viewlevel(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid'], 401);
        }

        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', ['users', 'levelusers'])->pluck('access_rights', 'data_menu');

        if ( ($access['users'] ?? 'No') === 'No' || ($access['levelusers'] ?? 'No') === 'No' ) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $level = LevelAdmin::where('code_data', $request->code_data)->first();
        if (!$level) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        $countUsed = User::where('level', $level->code_data)->count();
        $menus = LevelAdmin::where('code_data', $level->code_data)->orderBy('data_menu', 'ASC')->get();

        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','level_name' => $level->level_name,'code_data' => $level->code_data,'count_used' => $countUsed,'results' => $menus]);
    }

    public function deletelevel(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();

        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan saat proses data' ], 401);
        }

        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', ['levelusers', 'deletelevelusers'])->pluck('access_rights', 'data_menu');

        if ( ($access['levelusers'] ?? 'No') === 'No' || ($access['deletelevelusers'] ?? 'No') === 'No' ) {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []], 403);
        }

        $level = LevelAdmin::where('code_data', $request->code_data)->first();
        if (!$level) {
            return response()->json(['status_message' => 'error','note' => 'Data tidak ditemukan','results' => []], 404);
        }

        if ($level->code_data === 'LV5677001') {
            return response()->json(['status_message' => 'error','note' => 'Level utama tidak bisa dihapus','results' => []], 403);
        }

        $isUsed = User::where('level', $level->code_data)->exists();
        if ($isUsed) {
            return response()->json(['status_message' => 'error','note' => 'Level masih digunakan oleh user', 'results' => []], 422);
        }

        try {
            DB::beginTransaction();
            LevelAdmin::where('code_data', $level->code_data)->delete();

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => now()->format('YmdHis') . strtoupper(Str::random(1)),
                'code_user'    => $viewadmin->code_data ?? null,
                'activity'     => "Hapus level pengguna [{$level->level_name} - {$level->code_data}]",
                'code_company' => $viewadmin->code_company ?? null,
            ]);

            DB::commit();
            return response()->json(['status_message' => 'success','note' => 'Data berhasil dihapus','results' => [] ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Data gagal dihapus: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    //Admin
    public function editadmin(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if(!$viewadmin){ 
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid','results' => []], 401);
        }

        $validator = Validator::make($request->all(), [
            'full_name'     => 'required|string|max:200',
            'phone_number'  => 'required|string|max:30',
            'email'         => [
                'required',
                'email',
                'max:200',
                \Illuminate\Validation\Rule::unique('db_users', 'email')->ignore($viewadmin->id)
            ],
            'image_admin'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2548'
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => []], 422);
        }

        try {
            DB::beginTransaction();

            // Update data utama
            $viewadmin->update([
                'full_name'     => $request->full_name,
                'phone_number'  => $request->phone_number,
                'email'         => $request->email,
            ]);

            // Handle upload gambar
            $imageName = null;

            if ($request->hasFile('image_admin')) {
                $file = $request->file('image_admin');

                $imageName = 'PP-' . $viewadmin->id . '-' . time() . '.' . $file->extension();
                $path = public_path('themes/admin/AdminOne/image/upload/');

                // simpan file baru
                $file->move($path, $imageName);

                // hapus file lama (jika ada & bukan default)
                if ($viewadmin->getOriginal('image') && file_exists($path . $viewadmin->getOriginal('image'))) {
                    @unlink($path . $viewadmin->getOriginal('image'));
                }

                // update field image
                $viewadmin->update([
                    'image' => $imageName
                ]);
            }

            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(Carbon::now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $viewadmin->code_data ?? null,
                'activity'     => 'Ubah data admin [' . $viewadmin->full_name . ' - ' . $viewadmin->code_data . ']',
                'code_company' => $viewadmin->code_company ?? null,
            ]);

            DB::commit();
            return response()->json([ 'status_message' => 'success','note' => 'Data berhasil disimpan','results' => $filenameOriginal ?? 'Tanpa gambar'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    public function editpassadmin(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if(!$viewadmin){ 
            return response()->json(['status_message' => 'error','note' => 'Data user tidak valid','results' => []], 401);
        }

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|max:30',
            'new_password' => 'required|string|max:30|different:old_password',
        ], [
            'new_password.different' => 'Kata sandi baru harus berbeda dengan kata sandi lama.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_message' => 'error','note' => $validator->errors()->first(),'results' => []], 422);
        }

        if (!Hash::check($request->old_password, $viewadmin->password)) {
            return response()->json(['status_message' => 'error','note' => 'Kata sandi lama salah','results' => [] ], 403);
        }

        try {
            DB::beginTransaction();

            // $new_password = bcrypt($request->new_password); 
            // $viewadmin->update([
            //     'password' => $new_password,
            // ]);

            $viewadmin->update([
                'password' => Hash::make($request->new_password),
            ]);


            Activity::create([
                'id'           => Str::uuid(),
                'code_data'    => ltrim(Carbon::now()->format('YmdHis') . Str::random(1), '0'),
                'code_user'    => $viewadmin->code_data ?? null,
                'activity'     => 'Ubah password admin [' . $viewadmin->full_name . ' - ' . $viewadmin->code_data . ']',
                'code_company' => $viewadmin->code_company ?? null,
            ]);

            DB::commit();
            return response()->json([ 'status_message' => 'success','note' => 'Data berhasil disimpan','results' => $filenameOriginal ?? 'Tanpa gambar'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan: ' . $e->getMessage(),'results' => []], 500);
        }
    }

    // Aktifitas Pengguna  
    public function activityusers($request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'error','note' => 'Terjadi kesalahan saat proses data']);
        }

        $menus = ['users', 'activityusers', 'exportactivityusers'];
        $access = LevelAdmin::where('code_data', $viewadmin->level)->whereIn('data_menu', $menus)->pluck('access_rights', 'data_menu');

        // Cek akses utama
        if (($access['users'] ?? 'No') === 'No' || ($access['activityusers'] ?? 'No') === 'No') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []]);
        }

        // Cek akses export
        if ($request->type === 'export' && ($access['exportactivityusers'] ?? 'No') === 'No') {
            return response()->json(['status_message' => 'error','note' => 'Tidak ada akses','results' => []]);
        }

        // LIMIT DATA
        $vd = (int) ($request->vd ?? 20);
        

        // FILTER TANGGAL
        $datefilterstart = now()->subDays(30)->startOfDay();
        $datefilterend = now()->endOfDay();

        if (!empty($request->searchdate)) {
            [$start, $end] = explode("sd", $request->searchdate);
            $datefilterstart = Carbon::parse($start)->startOfDay();
            $datefilterend = Carbon::parse($end)->endOfDay();
        }

        // QUERY BASE
        $query = Activity::with([
                'user:id,code_data,full_name,email,level',
                'company:id,code_data'
            ])
            ->whereBetween('created_at', [$datefilterstart, $datefilterend])
            ->where('code_company', $viewadmin->code_company);

        // FILTER LEVEL USER
        if ($viewadmin->level !== 'LV5677001') {
            $query->whereHas('user', function ($q) {
                $q->where('level', '!=', 'LV5677001');
            });
        }

        // SEARCH
        if (!empty($request->keysearch)) {
            $search = $request->keysearch;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('full_name', 'ILIKE', "%{$search}%")
                    ->orWhere('code_data', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
                })
                ->orWhere('activity', 'ILIKE', "%{$search}%");
            });
        }

        // EXECUTE QUERY
        $results = $query->orderByDesc('created_at')->paginate($vd);

        // RESPONSE
        return response()->json(['status_message' => 'success','note' => 'Proses data berhasil','count_all_data' => $results->total(),'count_view_data' => $vd,'keysearch' => $request->keysearch,'results' => $results]);
    }

    public function getadmin($request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $viewadmin = User::where('id', $request->u)->where('key_token', $request->token)->first();
        if (!$viewadmin) {
            return response()->json(['status_message' => 'failed','note' => 'Terjadi kesalahan saat proses data'], 401);
        }

        $data_company = Company::select('nama_company','jenis','alamat','keterangan','foto')->where('code_data', $viewadmin->code_company)->first();
        $leveladmin = LevelAdmin::where('code_data', $viewadmin->level)->get();

        return response()->json(['status_message' => 'success','results' => [['data_company' => $data_company,'detailadmin' => [$viewadmin],'leveladmin' => [$leveladmin]]]], 200);
    }
}