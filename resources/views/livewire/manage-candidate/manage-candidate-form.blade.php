<div class="content-wrapper" style="min-height: 946px;">
    @include('common.header', [
        'menu' => $menu,
        'breadcrumb' =>  $breadcrumb,
        'active' => $activeMenu
    ])
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{($candidateId) ? 'Edit' : 'Add' }} {{$menu}}</h3>
                    </div>
                    <form wire:submit.prevent="updateCandidate">
                        <div class="card-body">
                            <div class="callout callout-primary">
                                <p><strong><i class="fa fa-info-circle"></i> Candidate Information</strong></p>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Type: <span class="text-danger">*</span></label>
                                        <div wire:ignore>    
                                            <select class="form-control select2" data-placeholder="Please Select Type" wire:model="candidateType">
                                                <option></option>
                                                @foreach($candidateOptions as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('candidateType') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                @if($candidateType && in_array($candidateType, ['c2c', 'w2_c2c']))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>L Company: <span class="text-danger">*</span></label>
                                            <div wire:ignore>    
                                                <select class="form-control select2" data-placeholder="Please Select L Company" wire:model='lCompanyId'>
                                                    <option></option>
                                                    @foreach($lCompanyData as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('lCompanyId') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>L Company Address: <span class="text-danger">*</span></label>
                                            <input type="text" placeholder="Please Enter L Company Address" wire:model="selectedLCompanyAddress" class="form-control" disabled>
                                            @error('selectedLCompanyAddress') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>L Rate: <span class="text-danger">*</span></label>
                                            <input type="text" placeholder="Please Enter L Rate" class="form-control" wire:model='lRate'>
                                            @error('lRate') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>L Agreement / w9: <span class="text-danger">*</span></label>
                                            <div class="onoffswitch4">
                                                <input type="checkbox" name="onoffswitch4" class="onoffswitch4-checkbox" id="myonoffswitch4" wire:model="lAggrement" {{($lAggrement) ? 'checked' : ''}}>
                                                <label class="onoffswitch4-label" for="myonoffswitch4">
                                                    <span class="onoffswitch4-inner" data-on-text="Yes" data-off-text="No"></span>
                                                    <span class="onoffswitch4-switch"></span>
                                                </label>
                                            </div>
                                            @error('lAggrement') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>C Id: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter CId" class="form-control {{($isCidAvailable == 1) ? 'is-valid' : ''}} {{($isCidAvailable == 0) ? 'is-invalid' : ''}}" wire:model='cId' wire:keyUp='checkExistiongCid' >
                                        @error('cId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>C Name: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter CName" class="form-control" wire:model='cName'>
                                        @error('cName') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Visa Status: <span class="text-danger">*</span></label>
                                        <div wire:ignore>
                                            <select class="form-control select2" data-placeholder="Please Select Visa" wire:model='visaStatusId'>
                                                <option></option>
                                                @foreach($visaStatus as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('visaStatusId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Visa Start: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker" placeholder="Please Select Visa Start Date" wire:model='visaStartDate' id="visaStartDate">
                                        @error('visaStartDate') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Visa End: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker" placeholder="Please Select Visa End Date" wire:model='visaEndDate' id="visaEndDate">
                                        @error('visaEndDate') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Id Start: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker" placeholder="Please Select Id Start Date" wire:model='idStartDate' id="idStartDate">
                                        @error('idStartDate') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Id End: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker" placeholder="Please Select Id End Date" wire:model='idEndDate' id="idEndDate">
                                        @error('idEndDate') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City State: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter City State" class="form-control" wire:model='cityState'>
                                        @error('cityState') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Project: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter Project" class="form-control" wire:model='project'>
                                        @error('project') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>C Rate: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter C Rate" class="form-control" wire:model='cRate'>
                                        @error('cRate') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Candidate Notes:</label>
                                        <textarea class="form-control" placeholder="Please Enter Candidate Notes" rows="3" wire:model='candidateNote'></textarea>
                                        @error('candidateNote') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>C Rate Notes:</label>
                                        <textarea class="form-control" placeholder="Please Enter C Rate Notes" rows="3" wire:model='cRateNote'></textarea>
                                        @error('cRateNote') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Position: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter Position" class="form-control" wire:model='position'>
                                        @error('position') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Client: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter Client" class="form-control" wire:model='client'>
                                        @error('client') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Lapt Received: <span class="text-danger">*</span></label>
                                        <div class="onoffswitch4">
                                            <input type="checkbox" class="onoffswitch4-checkbox" id="lapt-received" wire:model='laptReceived' {{($laptReceived) ? 'checked' : ''}}>
                                            <label class="onoffswitch4-label" for="lapt-received">
                                                <span class="onoffswitch4-inner" data-on-text="Yes" data-off-text="No"></span>
                                                <span class="onoffswitch4-switch"></span>
                                            </label>
                                        </div>
                                        @error('laptReceived') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="callout callout-primary">
                                <p><strong><i class="fa fa-info-circle"></i> Vendor Information</strong></p>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Pv Company: <span class="text-danger">*</span></label>
                                        <div wire:ignore>    
                                            <select class="form-control select2" data-placeholder="Please Select Pv Company" wire:model='pvCompanyId'>
                                                <option></option>
                                                @foreach($pvCompanyData as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('pvCompanyId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Pv Address: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter Pv Company Address" wire:model="selectedPvCompanyAddress" class="form-control" disabled>
                                        @error('selectedPvCompanyAddress') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select B Company: <span class="text-danger">*</span></label>
                                        <div wire:ignore>    
                                            <select class="form-control select2" data-placeholder="Please Select B Company" wire:model='bCompanyId'>
                                                <option></option>
                                                @foreach($bCompanyData as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('bCompanyId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>B Address: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter B Company Address" wire:model="selectedBCompanyAddress" class="form-control" disabled>
                                        @error('selectedBCompanyAddress') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>B Due Terms: <span class="text-danger">*</span></label>
                                        <div wire:ignore>    
                                            <select class="form-control select2" data-placeholder="Please Select B Due Terms" wire:model='bDueTermsId'>
                                                <option></option>
                                                @foreach($bDueTerms as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('bDueTermsId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>B Rate: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter B Rate" class="form-control" wire:model='bRate'>
                                        @error('bRate') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>B Rate Notes:</label>
                                        <textarea class="form-control" placeholder="Please Enter B Rate Notes" rows="3" wire:model='bRateNote'></textarea>
                                        @error('bRateNote') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Our Company: <span class="text-danger">*</span></label>
                                        <div wire:ignore>    
                                            <select class="form-control select2" data-placeholder="Please Select Our Company" wire:model='ourCompanyId'>
                                                <option></option>
                                                @foreach($ourCompanyData as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('ourCompanyId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Our Company Address: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter Our Company Address" wire:model="selectedOurCompanyAddress" class="form-control" disabled>
                                        @error('selectedOurCompanyAddress') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Our Company Phone: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter Our Company Phone" wire:model="selectedOurCompanyPhone" class="form-control" disabled>
                                        @error('selectedOurCompanyPhone') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>B Agreement: <span class="text-danger">*</span></label>
                                        <div class="onoffswitch4">
                                            <input type="checkbox" class="onoffswitch4-checkbox" id="b-agreement" wire:model='bAggrement' {{($bAggrement) ? 'checked' : ''}}>
                                            <label class="onoffswitch4-label" for="b-agreement">
                                                <span class="onoffswitch4-inner" data-on-text="Yes" data-off-text="No"></span>
                                                <span class="onoffswitch4-switch"></span>
                                            </label>
                                        </div>
                                        @error('bAggrement') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>C Agreement: <span class="text-danger">*</span></label>
                                        <div class="onoffswitch4">
                                            <input type="checkbox" class="onoffswitch4-checkbox" id="c-agreement" wire:model='cAggrement' {{($cAggrement) ? 'checked' : ''}}>
                                            <label class="onoffswitch4-label" for="c-agreement">
                                                <span class="onoffswitch4-inner" data-on-text="Yes" data-off-text="No"></span>
                                                <span class="onoffswitch4-switch"></span>
                                            </label>
                                        </div>
                                        @error('cAggrement') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Marketer: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter Marketer" class="form-control" wire:model='marketer'>
                                        @error('marketer') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Recruiter: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter Recruiter" class="form-control" wire:model='recruiter'>
                                        @error('recruiter') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer p">
                                <a href="{{ route('candidate') }}" wire:navigate><button class="btn btn-default" type="button">Back</button></a>
                                <button type="submit" class="btn btn-primary">{{($candidateId) ? 'Update' : 'Add' }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@section('js')
<script>
    $(document).ready(function () {
        let startVisaDate = flatpickr("#visaStartDate", {
            dateFormat: "m-d-Y",
            onChange: function(selectedDates, dateStr) {
                endVisaDate.set('minDate', dateStr); // Disable dates before start date in end date picker
                Livewire.dispatch('updatedVisaStartDate', dateStr);
            }
        });

        let endVisaDate = flatpickr("#visaEndDate", {
            dateFormat: "m-d-Y",
            onChange: function(selectedDates, dateStr) {
                startVisaDate.set('maxDate', dateStr); // Disable dates after end date in start date picker
                Livewire.dispatch('updatedVisaEndDate', dateStr);
            }
        });

        let startIdDateP = flatpickr("#idStartDate", {
            dateFormat: "m-d-Y",
            onChange: function(selectedDates, dateStr) {
                endIdDate.set('minDate', dateStr); // Disable dates before start date in end date picker
                Livewire.dispatch('updatedVisaStartDate', dateStr);
            }
        });

        let endIdDate = flatpickr("#idEndDate", {
            dateFormat: "m-d-Y",
            onChange: function(selectedDates, dateStr) {
                startIdDateP.set('maxDate', dateStr); // Disable dates after end date in start date picker
                Livewire.dispatch('updatedVisaEndDate', dateStr);
            }
        });
        function initPlugins() {
            $('.select2').each(function () {
                let placeholderText = $(this).data('placeholder') || 'Please select an option'; // Default if not set

                $(this).select2({
                    placeholder: placeholderText,
                    allowClear: true // Optional: allows clearing the selection
                });
            });

            $('.select2').on('change', function (e) {
                let fieldName = $(this).attr('wire:model');
                @this.set(fieldName, $(this).val());
            });
        }

        initPlugins();

        Livewire.on('initPlugins', function () {
            setTimeout(initPlugins, 300);
        });
    });
</script>
@endsection
