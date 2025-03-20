<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\LCompany;
use App\Livewire\LCompany\LcompanyForm;
use App\Livewire\BCompany;
use App\Livewire\BCompany\BcompanyForm;
use App\Livewire\PCompany;
use App\Livewire\PCompany\PcompanyForm;
use App\Livewire\OurCompany;
use App\Livewire\OurCompany\OurCompanyForm;
use App\Livewire\ManageCandidate;
use App\Livewire\ManageCandidate\ManageCandidateForm;
use App\Livewire\Visa;
use App\Livewire\Visa\ManageVisaForm;

Route::get('/', Dashboard::class)->name('dashboard');

/* Manage Dashboard Start */
Route::get('/dashboard', Dashboard::class)->name('dashboard');
/* Manage Dashboard End */

/* Manage L Company Start */
Route::get('/l-company', LCompany::class)->name('l-company');
Route::get('/l-company-data', [LCompany::class, 'getLCompanysData'])->name('l-company.data');
Route::get('/company/create', LCompanyForm::class)->name('lcompany.create');
Route::get('/company/{id}/edit', LCompanyForm::class)->name('lcompany.edit');
/* Manage L Company End */

/* Manage B Company Start */
Route::get('/b-company', BCompany::class)->name('b-company');
Route::get('/b-company-data', [BCompany::class, 'getBCompanysData'])->name('b-company.data');
Route::get('/bcompany/create', BcompanyForm::class)->name('bcompany.create');
Route::get('/bcompany/{id}/edit', BcompanyForm::class)->name('bcompany.edit');
/* Manage B Company End */

/* Manage p Company Start */
Route::get('/p-company', PCompany::class)->name('p-company');
Route::get('/p-company-data', [PCompany::class, 'getPCompanysData'])->name('p-company.data');
Route::get('/pcompany/create', PcompanyForm::class)->name('pcompany.create');
Route::get('/pcompany/{id}/edit', PcompanyForm::class)->name('pcompany.edit');
/* Manage P Company End */

/* Manage Our Company Start */
Route::get('/our-company', OurCompany::class)->name('our-company');
Route::get('/our-company-data', [OurCompany::class, 'getOurCompanysData'])->name('our-company.data');
Route::get('/our-company/create', OurCompanyForm::class)->name('ourCompany.create');
Route::get('/our-company/{id}/edit', OurCompanyForm::class)->name('ourCompany.edit');
/* Manage Our Company End */

/* Manage Visa Start */
Route::get('/visa', Visa::class)->name('visa');
Route::get('/visa-data', [Visa::class, 'getVisaData'])->name('visa.data');
Route::get('/visa/create', ManageVisaForm::class)->name('visa.create');
Route::get('/visa/{id}/edit', ManageVisaForm::class)->name('visa.edit');
/* Manage Visa End */


/* Manage Users Start */
Route::get('candidate', ManageCandidate::class)->name('candidate');
Route::get('/candidate/create', ManageCandidateForm::class)->name('candidate.create');
/* Manage Users End */