<?php
use App\Core\Route;

// Halaman publik 
Route::get('/', 'HomeController@index');
Route::get('/pendaftaran', 'PendaftaranController@index');
Route::get('/profil', 'HomeController@profil');

// Auth
Route::get('/auth/login', 'AuthController@showLogin');
Route::post('/auth/login', 'AuthController@loginProcess');
Route::get('/auth/logout', 'AuthController@logout');

// Area admin
Route::get('/admin/dashboard', 'Admin\DashboardController@index', ['auth', 'admin']);

Route::get('/guru/dashboard', 'GuruController@index', ['auth']);
Route::get('/siswa', 'SiswaController@index', ['auth']);

// Artikel (area admin)
// Menampilkan daftar artikel
Route::get('/admin/artikel', 'Admin\ArtikelController@index', ['auth', 'admin']);
// Route::get('/admin/artikel', 'Admin\ArtikelController@index', ['auth', 'admin']);
Route::get('/admin/artikel/tambah', 'Admin\ArtikelController@create', ['auth', 'admin']);

// Route untuk upload image TinyMCE
Route::post('/admin/upload_image_temp', 'Admin\UploadController@imageTemp', ['auth', 'admin']);

Route::post('/admin/artikel/store', 'Admin\ArtikelController@store', ['auth', 'admin']);

// Route detail artikel
Route::get('/admin/artikel/detail/{id}', 'Admin\ArtikelController@detail', ['auth', 'admin']);

// Halaman edit artikel (form)
Route::get('/admin/artikel/edit/{id}', 'Admin\ArtikelController@edit', ['auth', 'admin']);
 
// Proses update artikel
Route::post('/admin/artikel/update/{id}', 'Admin\ArtikelController@update', ['auth', 'admin']);
Route::post('/admin/artikel/delete/{id}', 'Admin\ArtikelController@delete', ['auth', 'admin']);

Route::get('/admin/artikel/search_suggest', 'Admin\ArtikelController@searchSuggest');

// pegawai
Route::get('/admin/pegawai', 'Admin\PegawaiController@index');
Route::get('/admin/pegawai/generate_nip', 'Admin\PegawaiController@generateNIP');
Route::post('/admin/pegawai/store', 'Admin\PegawaiController@store');
// Ambil data pegawai untuk edit (GET)
Route::get('/admin/pegawai/get/{id}', 'Admin\PegawaiController@get');

// Update pegawai (POST atau PUT)
Route::post('/admin/pegawai/update', 'Admin\PegawaiController@update');
Route::delete('/admin/pegawai/delete/{id}', 'Admin\PegawaiController@delete'); 

Route::get('/admin/pegawai/search_table', 'Admin\PegawaiController@searchTable');

// =====================================
// ROUTE UNTUK ADMIN_UNIT (SEMUA SEKOLAH)
// =====================================
// Halaman utama pegawai unit
Route::get('/unit/{slug}/pegawai', 'Unit\UnitPegawaiController@index');
Route::get('/unit/{slug}/pegawai/fetchAll', 'Unit\UnitPegawaiController@fetchAll');

Route::get('/unit/{slug}/pegawai/generate_nip', 'Unit\UnitPegawaiController@generateNIP');

Route::post('/unit/{slug}/pegawai/store', 'Unit\UnitPegawaiController@store');
Route::get('/unit/{slug}/pegawai/get/{id}', 'Unit\UnitPegawaiController@getById');
Route::delete('/unit/{slug}/pegawai/delete/{id}', 'Unit\UnitPegawaiController@delete');

Route::get('/unit/{slug}/pegawai/search', 'Unit\UnitPegawaiController@search');

// Halaman form pendaftaran SMP
Route::get('/pendaftaran/smp', 'PendaftaranController@formSmp');
Route::get('/pendaftaran/sma', 'PendaftaranController@formSma');

// Proses form pendaftaran SMP
Route::post('/pendaftaran/smp/store', 'PendaftaranController@storeSmp'); 
Route::post('/pendaftaran/sma/store', 'PendaftaranController@storeSma'); 
Route::get('/pendaftaran/sukses', 'PendaftaranController@sukses');
Route::get('/pendaftaran/pdf', 'PendaftaranController@pdf');

// app/core/Route.php 
Route::get('/admin/pendaftaran', 'Admin\CalonSiswaController@index');
Route::get('/admin/pendaftaran/fetchAll', 'Admin\CalonSiswaController@fetchAll');
Route::post('/admin/pendaftaran/updateStatus', 'Admin\CalonSiswaController@updateStatus');
Route::get('/admin/pendaftaran/search', 'Admin\CalonSiswaController@search');
Route::get('/admin/pendaftaran/detail/{id}', 'PendaftaranController@detail');

// Halaman calon siswa per unit
Route::get('/unit/{slug}/calon-siswa', 'Unit\UnitCalonSiswaController@index');
Route::get('/unit/{slug}/calon-siswa/fetchAll', 'Unit\UnitCalonSiswaController@fetchAll');
Route::post('/unit/{slug}/calon-siswa/updateStatus', 'Unit\UnitCalonSiswaController@updateStatus');
Route::get('/unit/{slug}/calon-siswa/search', 'Unit\UnitCalonSiswaController@search');

Route::get('/unit/{slug}/mapel', 'Unit\UnitMapelController@index');
Route::get('/unit/{slug}/mapel/fetchAll', 'Unit\UnitMapelController@fetchAll');
Route::post('/unit/{slug}/mapel/store', 'Unit\UnitMapelController@store');
Route::get('/unit/{slug}/mapel/get/{id}', 'Unit\UnitMapelController@get');
Route::post('/unit/{slug}/mapel/update/{id}', 'Unit\UnitMapelController@update');
Route::post('/unit/{slug}/mapel/delete/{id}', 'Unit\UnitMapelController@delete');
Route::get('/unit/{slug}/mapel/search', 'Unit\UnitMapelController@search');