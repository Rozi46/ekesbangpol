<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{Controller,SistemController,ActionController,ApiControllerPengaturan,UsersController,SettingsController,LeaveController,RankController,PositionController,EmployeesController,BeritaController,AgendaController,InfoPengumumanController,SingelPageController};

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
	// Agenda
	Route::get('/dataagenda',[AgendaController::class, 'listagenda']);
	Route::get('/datalistagenda', [AgendaController::class, 'datalistagenda']);
	Route::post('/saveagenda',[AgendaController::class, 'saveagenda']);
	Route::get('/viewagenda',[AgendaController::class, 'viewagenda']);
	Route::put('/updateagenda', [AgendaController::class, 'updateagenda']);
	Route::post('/statusagenda', [AgendaController::class, 'statusagenda']);
	Route::delete('/deleteagenda', [AgendaController::class, 'deleteagenda']);
	Route::post('/exportagenda', [AgendaController::class, 'exportagenda']);
	// Info dan Pengumuman
	Route::get('/datainfopengumuman',[InfoPengumumanController::class, 'listinfopengumuman']);
	Route::get('/datalistinfopengumuman', [InfoPengumumanController::class, 'datalistinfopengumuman']);
	Route::post('/saveinfopengumuman',[InfoPengumumanController::class, 'saveinfopengumuman']);
	Route::get('/viewinfopengumuman',[InfoPengumumanController::class, 'viewinfopengumuman']);
	Route::put('/updateinfopengumuman', [InfoPengumumanController::class, 'updateinfopengumuman']);
	Route::post('/statusinfopengumuman', [InfoPengumumanController::class, 'statusinfopengumuman']);
	Route::delete('/deleteinfopengumuman', [InfoPengumumanController::class, 'deleteinfopengumuman']);
	Route::post('/exportinfopengumuman', [InfoPengumumanController::class, 'exportinfopengumuman']);
	// Singel Page
	Route::get('/datavisimisi',[SingelPageController::class, 'datavisimisi']);	
	Route::get('/viewvisimisi',[SingelPageController::class, 'viewvisimisi']);
	Route::put('/updatevisimisi',[SingelPageController::class, 'updatevisimisi']);
	Route::get('/datatentang', [SingelPageController::class, 'datatentang']);
	Route::get('/viewtentang',[SingelPageController::class, 'viewtentang']);
	Route::put('/updatetentang',[SingelPageController::class, 'updatetentang']);
	Route::get('/datatugasfungsi',[SingelPageController::class, 'datatugasfungsi']);
	Route::get('/viewtugasfungsi',[SingelPageController::class, 'viewtugasfungsi']);
	Route::put('/updatetugasfungsi',[SingelPageController::class, 'updatetugasfungsi']);
	Route::get('/datastrukturorganisasi',[SingelPageController::class, 'datastrukturorganisasi']);
	Route::get('/viewstrukturorganisasi',[SingelPageController::class, 'viewstrukturorganisasi']);
	Route::put('/updatestrukturorganisasi',[SingelPageController::class, 'updatestrukturorganisasi']);
	Route::get('/dataalamatkontak',[SingelPageController::class, 'dataalamatkontak']);
	Route::get('/viewalamatkontak',[SingelPageController::class, 'viewalamatkontak']);
	Route::put('/updatealamatkontak',[SingelPageController::class, 'updatealamatkontak']);

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