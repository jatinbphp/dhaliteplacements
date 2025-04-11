<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
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
use App\Livewire\ManageVisaCandidate;
use App\Livewire\ManageVisaCandidate\VisaCandidateForm;
use App\Livewire\ManageTimeSheet;
use App\Livewire\ManageTimeSheet\TimeSheetForm;
use App\Livewire\ManageInvoiceTracking;
use App\Livewire\ManageInvoice;
use App\Livewire\ManageDateWiseInvoice;
use App\Livewire\ManagePayment;
use App\Livewire\ManagePayment\PaymentForm;
use App\Livewire\ManageVendorWiseData;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::middleware(['auth'])->group(function () {
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


	/* Manage Candidate Start */
	Route::get('candidate', ManageCandidate::class)->name('candidate');
	Route::get('/candidate-data', [ManageCandidate::class, 'getCandidateData'])->name('candidate.data');
	Route::get('/candidate/create', ManageCandidateForm::class)->name('candidate.create');
	Route::get('/candidate/{id}/edit', ManageCandidateForm::class)->name('candidate.edit');

	Route::get('visa-candidate', ManageVisaCandidate::class)->name('visa-candidate');
	Route::get('/visa-candidate-data', [ManageVisaCandidate::class, 'getVisaCandidateData'])->name('visa-candidate.data');
	Route::get('/visa-candidate/{id}/edit', VisaCandidateForm::class)->name('visa-candidate.edit');
	/* Manage Candidate End */

	/* Manage Time Sheet Start */
	Route::get('/time-sheet', ManageTimeSheet::class)->name('time-sheet');
	Route::get('/time-sheet-data', [ManageTimeSheet::class, 'getTimeSheetData'])->name('time-sheet.data');
	Route::get('/time-sheet/create', TimeSheetForm::class)->name('time-sheet.create');
	Route::get('/time-sheet/{id}/edit', TimeSheetForm::class)->name('time-sheet.edit');
	/* Manage Time Sheet End */

	/* Manage Invoice Start */
	Route::get('/invoice', ManageInvoice::class)->name('invoice');
	Route::get('/invoice-data', [ManageInvoice::class, 'getInvoiceData'])->name('invoice.data');
	/* Manage Invoice End */

	/* Manage Invoice Tracking Start */
	Route::get('/invoice-tracking', ManageInvoiceTracking::class)->name('invoice-tracking');
	Route::get('/invoice-tracking-data', [ManageInvoiceTracking::class, 'getInvoiceTrackingData'])->name('invoice-tracking.data');
	/* Manage Invoice Tracking End */

	/* Manage Invoice Tracking Start */
	Route::get('/date-wise-invoice-tracking', ManageDateWiseInvoice::class)->name('date-wise-invoice-tracking');
	Route::get('/date-wise-invoice-tracking-data', [ManageDateWiseInvoice::class, 'getDateWiseInvoiceTrackingData'])->name('date-wise-invoice-tracking.data');
	/* Manage Invoice Tracking End */

	/* Manage Payment Start */
	Route::get('/payment', ManagePayment::class)->name('payment');
	Route::get('/payment-data', [ManagePayment::class, 'getPaymentData'])->name('payment.data');
	Route::get('/payment/create', PaymentForm::class)->name('payment.create');
	/* Manage Payment End */

	/* Manage Vendor wise Data Start */
	Route::get('/vendor-wise', ManageVendorWiseData::class)->name('vendor-wise');
	Route::get('/vendor-wise-data', [ManageVendorWiseData::class, 'getVendorWiseData'])->name('vendor-wise.data');
	/* Manage Vendor wise Data End */
});