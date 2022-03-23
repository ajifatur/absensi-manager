<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Admin
Route::group(['middleware' => ['admin']], function() {
	// Summary Attendance
	Route::get('/admin/summary/attendance', 'SummaryAttendanceController@index')->name('admin.summary.attendance.index');
	Route::get('/admin/summary/attendance/detail/{id}', 'SummaryAttendanceController@detail')->name('admin.summary.attendance.detail');
	Route::get('/admin/summary/monitor-attendance', 'SummaryAttendanceController@monitor')->name('admin.summary.attendance.monitor');

	// Summary Salary
	Route::get('/admin/summary/salary', 'SummarySalaryController@index')->name('admin.summary.salary.index');
	Route::get('/admin/summary/salary/export', 'SummarySalaryController@export')->name('admin.summary.salary.export');
	Route::post('/admin/summary/salary/update/indicator', 'SummarySalaryController@updateIndicator')->name('admin.summary.salary.update.indicator');
	Route::post('/admin/summary/salary/update/late-fund', 'SummarySalaryController@updateLateFund')->name('admin.summary.salary.update.late-fund');
	Route::post('/admin/summary/salary/update/debt-fund', 'SummarySalaryController@updateDebtFund')->name('admin.summary.salary.update.debt-fund');

	// Summary Office
	Route::get('/admin/summary/office', 'SummaryOfficeController@index')->name('admin.summary.office.index');

	// Summary Certification
	Route::get('/admin/summary/certification', 'SummaryCertificationController@index')->name('admin.summary.certification.index');
	Route::post('/admin/summary/certification/update', 'SummaryCertificationController@update')->name('admin.summary.certification.update');

    // Attendance
	Route::get('/admin/attendance', 'AttendanceController@index')->name('admin.attendance.index');
	Route::get('/admin/attendance/create', 'AttendanceController@create')->name('admin.attendance.create');
	Route::post('/admin/attendance/store', 'AttendanceController@store')->name('admin.attendance.store');
	Route::get('/admin/attendance/edit/{id}', 'AttendanceController@edit')->name('admin.attendance.edit');
	Route::post('/admin/attendance/update', 'AttendanceController@update')->name('admin.attendance.update');
	Route::post('/admin/attendance/delete', 'AttendanceController@delete')->name('admin.attendance.delete');

    // Absent
	Route::get('/admin/absent', 'AbsentController@index')->name('admin.absent.index');
	Route::get('/admin/absent/create', 'AbsentController@create')->name('admin.absent.create');
	Route::post('/admin/absent/store', 'AbsentController@store')->name('admin.absent.store');
	Route::get('/admin/absent/edit/{id}', 'AbsentController@edit')->name('admin.absent.edit');
	Route::post('/admin/absent/update', 'AbsentController@update')->name('admin.absent.update');
	Route::post('/admin/absent/delete', 'AbsentController@delete')->name('admin.absent.delete');

    // Leave
	Route::get('/admin/leave', 'LeaveController@index')->name('admin.leave.index');
	Route::get('/admin/leave/create', 'LeaveController@create')->name('admin.leave.create');
	Route::post('/admin/leave/store', 'LeaveController@store')->name('admin.leave.store');
	Route::get('/admin/leave/edit/{id}', 'LeaveController@edit')->name('admin.leave.edit');
	Route::post('/admin/leave/update', 'LeaveController@update')->name('admin.leave.update');
	Route::post('/admin/leave/delete', 'LeaveController@delete')->name('admin.leave.delete');

	// User
	Route::get('/admin/user', 'UserController@index')->name('admin.user.index');
	Route::get('/admin/user/create', 'UserController@create')->name('admin.user.create');
	Route::post('/admin/user/store', 'UserController@store')->name('admin.user.store');
	Route::get('/admin/user/detail/{id}', 'UserController@detail')->name('admin.user.detail');
	Route::get('/admin/user/edit/{id}', 'UserController@edit')->name('admin.user.edit');
	Route::post('/admin/user/update', 'UserController@update')->name('admin.user.update');
	Route::post('/admin/user/delete', 'UserController@delete')->name('admin.user.delete');
	Route::get('/admin/user/edit-certification/{id}', 'UserController@editCertification')->name('admin.user.edit-certification');
	Route::post('/admin/user/update-certification', 'UserController@updateCertification')->name('admin.user.update-certification');

	// Group
	Route::get('/admin/group', 'GroupController@index')->name('admin.group.index');
	Route::get('/admin/group/create', 'GroupController@create')->name('admin.group.create');
	Route::post('/admin/group/store', 'GroupController@store')->name('admin.group.store');
	Route::get('/admin/group/detail/{id}', 'GroupController@detail')->name('admin.group.detail');
	Route::get('/admin/group/edit/{id}', 'GroupController@edit')->name('admin.group.edit');
	Route::post('/admin/group/update', 'GroupController@update')->name('admin.group.update');
	Route::post('/admin/group/delete', 'GroupController@delete')->name('admin.group.delete');

	// Office
	Route::get('/admin/office', 'OfficeController@index')->name('admin.office.index');
	Route::get('/admin/office/create', 'OfficeController@create')->name('admin.office.create');
	Route::post('/admin/office/store', 'OfficeController@store')->name('admin.office.store');
	Route::get('/admin/office/detail/{id}', 'OfficeController@detail')->name('admin.office.detail');
	Route::get('/admin/office/edit/{id}', 'OfficeController@edit')->name('admin.office.edit');
	Route::post('/admin/office/update', 'OfficeController@update')->name('admin.office.update');
	Route::post('/admin/office/delete', 'OfficeController@delete')->name('admin.office.delete');

	// Position
	Route::get('/admin/position', 'PositionController@index')->name('admin.position.index');
	Route::get('/admin/position/create', 'PositionController@create')->name('admin.position.create');
	Route::post('/admin/position/store', 'PositionController@store')->name('admin.position.store');
	Route::get('/admin/position/detail/{id}', 'PositionController@detail')->name('admin.position.detail');
	Route::get('/admin/position/edit/{id}', 'PositionController@edit')->name('admin.position.edit');
	Route::post('/admin/position/update', 'PositionController@update')->name('admin.position.update');
	Route::post('/admin/position/delete', 'PositionController@delete')->name('admin.position.delete');

	// Work Hour
	Route::get('/admin/work-hour', 'WorkHourController@index')->name('admin.work-hour.index');
	Route::get('/admin/work-hour/create', 'WorkHourController@create')->name('admin.work-hour.create');
	Route::post('/admin/work-hour/store', 'WorkHourController@store')->name('admin.work-hour.store');
	Route::get('/admin/work-hour/edit/{id}', 'WorkHourController@edit')->name('admin.work-hour.edit');
	Route::post('/admin/work-hour/update', 'WorkHourController@update')->name('admin.work-hour.update');
	Route::post('/admin/work-hour/delete', 'WorkHourController@delete')->name('admin.work-hour.delete');

	// Certification
	Route::get('/admin/certification', 'CertificationController@index')->name('admin.certification.index');
	Route::get('/admin/certification/create', 'CertificationController@create')->name('admin.certification.create');
	Route::post('/admin/certification/store', 'CertificationController@store')->name('admin.certification.store');
	Route::get('/admin/certification/edit/{id}', 'CertificationController@edit')->name('admin.certification.edit');
	Route::post('/admin/certification/update', 'CertificationController@update')->name('admin.certification.update');
	Route::post('/admin/certification/delete', 'CertificationController@delete')->name('admin.certification.delete');

	// Salary Category
	Route::get('/admin/salary-category', 'SalaryCategoryController@index')->name('admin.salary-category.index');
	Route::get('/admin/salary-category/create', 'SalaryCategoryController@create')->name('admin.salary-category.create');
	Route::post('/admin/salary-category/store', 'SalaryCategoryController@store')->name('admin.salary-category.store');
	Route::get('/admin/salary-category/edit/{id}', 'SalaryCategoryController@edit')->name('admin.salary-category.edit');
	Route::post('/admin/salary-category/update', 'SalaryCategoryController@update')->name('admin.salary-category.update');
	Route::get('/admin/salary-category/set/{id}', 'SalaryCategoryController@set')->name('admin.salary-category.set');
	Route::post('/admin/salary-category/update-indicator', 'SalaryCategoryController@updateIndicator')->name('admin.salary-category.update-indicator');
	Route::post('/admin/salary-category/delete', 'SalaryCategoryController@delete')->name('admin.salary-category.delete');
});

// Guest
Route::group(['middleware' => ['guest']], function() {
    // Home
    Route::get('/', function () {
        return redirect()->route('auth.login');
    });
});

// FaturHelper Routes
\Ajifatur\Helpers\RouteExt::auth();
\Ajifatur\Helpers\RouteExt::admin();