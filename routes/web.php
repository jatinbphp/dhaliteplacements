<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\LCompany;
use App\Livewire\LCompany\LcompanyForm;
use App\Livewire\CCompany;
use App\Livewire\CCompany\CcompanyForm;
use App\Livewire\PCompany;
use App\Livewire\PCompany\PcompanyForm;
use App\Livewire\OurCompany;
use App\Livewire\OurCompany\OurCompanyForm;

Route::get('/', Dashboard::class)->name('dashboard');

/* Dashboard Start */
Route::get('/dashboard', Dashboard::class)->name('dashboard');
/* Dashboard End */

/* L Company Start */
Route::get('/l-company', LCompany::class)->name('l-company');
Route::get('/l-company-data', [LCompany::class, 'getLCompanysData'])->name('l-company.data');
Route::get('/{id}/edit', LCompany::class)->name('edit');
Route::get('/company/create', LCompanyForm::class)->name('lcompany.create');
Route::get('/company/{id}/edit', LCompanyForm::class)->name('lcompany.edit');
/* L Company End */

/* L Company Start */
Route::get('/c-company', CCompany::class)->name('c-company');
Route::get('/c-company-data', [CCompany::class, 'getCCompanysData'])->name('c-company.data');
Route::get('/{id}/edit', CCompany::class)->name('edit');
Route::get('/ccompany/create', CcompanyForm::class)->name('ccompany.create');
Route::get('/ccompany/{id}/edit', CcompanyForm::class)->name('ccompany.edit');
/* L Company End */

/* p Company Start */
Route::get('/p-company', PCompany::class)->name('p-company');
Route::get('/p-company-data', [PCompany::class, 'getPCompanysData'])->name('p-company.data');
Route::get('/{id}/edit', PCompany::class)->name('edit');
Route::get('/pcompany/create', PcompanyForm::class)->name('pcompany.create');
Route::get('/pcompany/{id}/edit', PcompanyForm::class)->name('pcompany.edit');
/* L Company End */

/* Our Company Start */
Route::get('/our-company', OurCompany::class)->name('our-company');
Route::get('/our-company-data', [OurCompany::class, 'getOurCompanysData'])->name('our-company.data');
Route::get('/{id}/edit', OurCompany::class)->name('edit');
Route::get('/our-company/create', OurCompanyForm::class)->name('ourCompany.create');
Route::get('/our-company/{id}/edit', OurCompanyForm::class)->name('ourCompany.edit');
/* Our Company End */