<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{Controller,SistemController,ActionController,ApiControllerPengaturan,UsersController,SettingsController,LeaveController,RankController,PositionController,EmployeesController,BeritaController};

Route::get('/admin', function () {
    // return view('welcome']);
    return redirect()->route('administration');
});

Route::get('/admin/administration', function () {
    if (Session::get('admin_login_kesbangpol')) {
        return redirect()->route('dash');
	}else{
		return view('admin.AdminOne.login', ['url' => 'login']);
		// return view('maintenance']);
    }
})->name('administration');

Route::get('/admin/login', function () { 
    return redirect()->route('administration');
})->name('login');

Route::post('/admin/login',[SistemController::class, 'login']);
Route::get('/admin/logout',[SistemController::class, 'logout'])->name('logout');
Route::get('/admin/dash',[SistemController::class, 'dash'])->name('dash');

// Route::group(['middleware' => 'auth.jwt'], function(){
	// Pengguna
	// Route::get('/admin/listusers',[UsersController::class, 'listusers']);
	// Route::get('/admin/exportlistusers',[UsersController::class, 'exportlistusers']);
	// Route::get('/admin/newusers',[UsersController::class, 'newusers']);
	// Route::post('/admin/saveusers',[UsersController::class, 'saveusers']);
	// Route::get('/admin/viewusers',[UsersController::class, 'viewusers']);
	// Route::post('/admin/editusers',[UsersController::class, 'editusers']);
	// Route::get('/admin/deleteusers',[UsersController::class, 'deleteusers']);
	
	// Level Pengguna
	// Route::get('/admin/levelusers',[UsersController::class, 'levelusers']);
	// Route::get('/admin/newlevelusers',[UsersController::class, 'newlevelusers']);
	// Route::post('/admin/actionlevel',[UsersController::class, 'actionlevel']);
	// Route::get('/admin/viewlevel',[UsersController::class, 'viewlevel']);
	// Route::get('/admin/deletelevel',[UsersController::class, 'deletelevel']);
	
	// Admin
	// Route::get('/admin/viewaccount',[UsersController::class, 'viewaccount']);
	// Route::post('/admin/editaccount',[UsersController::class, 'editaccount']);
	// Route::post('/admin/editpassaccount',[UsersController::class, 'editpassaccount']);

	// Aktivitas Pengguna
	// Route::get('/admin/activityusers',[UsersController::class, 'activityusers']);
	// Route::get('/admin/exportactivityusers',[UsersController::class, 'exportactivityusers']);

	// Setting
	// Route::get('/admin/settingmenu',[SettingsController::class, 'settingmenu']);
	// Route::get('/admin/delmenu',[SettingsController::class, 'delmenu']);
	// Route::post('/admin/actionsettingmenu',[SettingsController::class, 'actionsettingmenu']);
	// Route::get('/admin/listcompany',[SettingsController::class, 'listcompany']);
	// Route::get('/admin/newcompany',[SettingsController::class, 'newcompany']);
	// Route::post('/admin/savecompany',[SettingsController::class, 'savecompany']);
	// Route::get('/admin/viewcompany',[SettingsController::class, 'viewcompany']);
	// Route::post('/admin/editcompany',[SettingsController::class, 'editcompany']);
	// Route::get('/admin/deletecompany',[SettingsController::class, 'deletecompany']);
	// Route::get('/admin/manualbook',[SettingsController::class, 'manualbook']);
	// Route::post('/admin/uploadmanualbook',[SettingsController::class, 'uploadmanualbook']);
	// Route::get('/admin/viewmanualbook',[SettingsController::class, 'viewmanualbook']);
	// Route::get('/admin/downloadmanualbook',[SettingsController::class, 'downloadmanualbook']);
	// Route::get('/admin/sinkron',[ActionController::class, 'sinkron']);
// });

Route::prefix('admin')->name('admin.')->group(function () {
	// Berita
	Route::get('/databerita',[BeritaController::class, 'listberita']);
	Route::get('/datalistberita', [BeritaController::class, 'datalistberita']);
	Route::post('/saveberita',[BeritaController::class, 'saveberita']);
	Route::get('/viewberita',[BeritaController::class, 'viewberita']);
	Route::put('/updateberita', [BeritaController::class, 'updateberita']);
	Route::post('/statusberita', [BeritaController::class, 'statusberita']);
	Route::delete('/deleteberita', [BeritaController::class, 'deleteberita']);
	Route::post('/exportberita', [BeritaController::class, 'exportberita']);

	// Pegawai
	Route::get('/listpegawai',[EmployeesController::class, 'listpegawai']);
	Route::get('/datalistpegawai', [EmployeesController::class, 'datalistpegawai']);
	Route::post('/savepegawai',[EmployeesController::class, 'savepegawai']);
	Route::get('/viewpegawai',[EmployeesController::class, 'viewpegawai']);
	Route::put('/updatepegawai', [EmployeesController::class, 'updatepegawai']);
	Route::post('/statuspegawai', [EmployeesController::class, 'statuspegawai']);
	Route::delete('/deletepegawai', [EmployeesController::class, 'deletepegawai']);
	Route::get('/listopjabatan',[EmployeesController::class, 'listopjabatan']);
	Route::get('/listoppangkat',[EmployeesController::class, 'listoppangkat']);
	Route::post('/exportpegawai', [EmployeesController::class, 'exportpegawai']);

	// Jabatan
	Route::get('/listjabatan',[PositionController::class, 'listjabatan']);
	Route::get('/datalistjabatan', [PositionController::class, 'datalistjabatan']);
	Route::post('/savejabatan',[PositionController::class, 'savejabatan']);
	Route::get('/viewjabatan',[PositionController::class, 'viewjabatan']);
	Route::put('/updatejabatan', [PositionController::class, 'updatejabatan']);
	Route::post('/statusjabatan', [PositionController::class, 'statusjabatan']);
	Route::delete('/deletejabatan', [PositionController::class, 'deletejabatan']);
	Route::post('/exportjabatan',[PositionController::class, 'exportjabatan']);

	// Pangkat
	Route::get('/listpangkat',[RankController::class, 'listpangkat']);
	Route::get('/datalistpangkat', [RankController::class, 'datalistpangkat']);
	Route::post('/savepangkat',[RankController::class, 'savepangkat']);
	Route::get('/viewpangkat',[RankController::class, 'viewpangkat']);
	Route::put('/updatepangkat', [RankController::class, 'updatepangkat']);
	Route::post('/statuspangkat', [RankController::class, 'statuspangkat']);
	Route::delete('/deletepangkat', [RankController::class, 'deletepangkat']);
	Route::post('/exportpangkat',[RankController::class, 'exportpangkat']);

	// Cuti
	Route::get('/listcuti',[LeaveController::class, 'listcuti']);
	Route::get('/newcuti',[LeaveController::class, 'newcuti']);
	Route::post('/savecuti',[LeaveController::class, 'savecuti']);
	Route::get('/viewcuti',[LeaveController::class, 'viewcuti']);
	Route::post('/editcuti',[LeaveController::class, 'editcuti']);
	Route::get('/deletecuti',[LeaveController::class, 'deletecuti']);
	Route::post('/exportcuti',[LeaveController::class, 'exportcuti']);
	Route::get('/ajaxcuti', [LeaveController::class, 'ajaxcuti']);
	Route::post('/ajaxstatuscuti', [LeaveController::class, 'ajaxstatuscuti']);
	Route::delete('/ajaxdeletecuti', [LeaveController::class, 'ajaxdeletecuti']);

	// Route baru untuk single-page cuti
	Route::get('/ajaxdetailcuti', [LeaveController::class, 'ajaxdetailcuti']);
	Route::post('/ajaxstorecuti', [LeaveController::class, 'ajaxstorecuti']);
	Route::put('/ajaxupdatecuti', [LeaveController::class, 'ajaxupdatecuti']);

	// Pengguna
	Route::get('/listusers',[UsersController::class, 'listusers']);
	Route::post('/exportlistusers',[UsersController::class, 'exportlistusers']);
	Route::get('/newusers',[UsersController::class, 'newusers']);
	Route::post('/saveusers',[UsersController::class, 'saveusers']);
	Route::get('/viewusers',[UsersController::class, 'viewusers']);
	Route::post('/editusers',[UsersController::class, 'editusers']);
	Route::get('/deleteusers',[UsersController::class, 'deleteusers']);
	
	// Level Pengguna
	Route::get('/levelusers',[UsersController::class, 'levelusers']);
	Route::get('/newlevelusers',[UsersController::class, 'newlevelusers']);
	Route::post('/actionlevel',[UsersController::class, 'actionlevel']);
	Route::get('/viewlevel',[UsersController::class, 'viewlevel']);
	Route::get('/deletelevel',[UsersController::class, 'deletelevel']);
	
	// Admin
	Route::get('/viewaccount',[UsersController::class, 'viewaccount']);
	Route::post('/editaccount',[UsersController::class, 'editaccount']);
	Route::post('/editpassaccount',[UsersController::class, 'editpassaccount']);

	// Aktivitas Pengguna
	Route::get('/activityusers',[UsersController::class, 'activityusers']);
	Route::post('/exportactivityusers', [UsersController::class, 'exportactivityusers']);

	// download export data global
	Route::get('/download-exportdata/{file}', [Controller::class, 'downloadExport']);

	// Setting
	Route::get('/settingmenu',[SettingsController::class, 'settingmenu']);
	Route::get('/delmenu',[SettingsController::class, 'delmenu']);
	Route::post('/actionsettingmenu',[SettingsController::class, 'actionsettingmenu']);
	Route::get('/listcompany',[SettingsController::class, 'listcompany']);
	Route::get('/newcompany',[SettingsController::class, 'newcompany']);
	Route::post('/savecompany',[SettingsController::class, 'savecompany']);
	Route::get('/viewcompany',[SettingsController::class, 'viewcompany']);
	Route::post('/editcompany',[SettingsController::class, 'editcompany']);
	Route::get('deletecompany',[SettingsController::class, 'deletecompany']);
	Route::get('/manualbook',[SettingsController::class, 'manualbook']);
	Route::post('/uploadmanualbook',[SettingsController::class, 'uploadmanualbook']);
	Route::get('/viewmanualbook',[SettingsController::class, 'viewmanualbook']);
	Route::get('/downloadmanualbook',[SettingsController::class, 'downloadmanualbook']);
	Route::get('/sinkron',[ActionController::class, 'sinkron']);
});