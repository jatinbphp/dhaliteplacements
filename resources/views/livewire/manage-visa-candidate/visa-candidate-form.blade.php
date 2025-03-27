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
                    <form wire:submit.prevent="updateVisaCandidate">
                        <div class="card-body">
                            <div class="callout callout-primary">
                                <p><strong><i class="fa fa-info-circle"></i> Candidate Information</strong></p>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Visa Status: <span class="text-danger">*</span></label>
                                        <div wire:ignore>
                                            <select class="form-control select-data" data-placeholder="Please Select Visa" wire:model='visaStatusId'>
                                                <option></option>
                                                @foreach($visaStatus as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('visaStatusId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                @if($showDate)
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
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City State: <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Please Enter City State" class="form-control" wire:model='cityState'>
                                        @error('cityState') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer p">
                                <a href="{{ route('visa-candidate') }}" wire:navigate><button class="btn btn-default" type="button">Back</button></a>
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
    function initPlugins() {
            console.log("initPlugins");
        $('.select-data').each(function () {
            let $this = $(this);
            if ($this.next('.select2-container').length) {
                $this.next('.select2-container').remove();
                $this.removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
            }

            $this.select2({
                placeholder: $this.data('placeholder') || 'Please select an option',
                allowClear: true
            });
        });

        $('.select-data').on('change', function (e) {
            let fieldName = $(this).attr('wire:model');
            @this.set(fieldName, $(this).val());
        });

        let startVisaDate = flatpickr("#visaStartDate", {
            dateFormat: "m-d-Y",
            onChange: function(selectedDates, dateStr) {
                endVisaDate.set('minDate', dateStr);
                Livewire.dispatch('updatedVisaStartDate', dateStr);
            }
        });

        let endVisaDate = flatpickr("#visaEndDate", {
            dateFormat: "m-d-Y",
            onChange: function(selectedDates, dateStr) {
                startVisaDate.set('maxDate', dateStr);
                Livewire.dispatch('updatedVisaEndDate', dateStr);
            }
        });

        let startIdDateP = flatpickr("#idStartDate", {
            dateFormat: "m-d-Y",
            onChange: function(selectedDates, dateStr) {
                endIdDate.set('minDate', dateStr);
                Livewire.dispatch('updatedVisaStartDate', dateStr);
            }
        });

        let endIdDate = flatpickr("#idEndDate", {
            dateFormat: "m-d-Y",
            onChange: function(selectedDates, dateStr) {
                startIdDateP.set('maxDate', dateStr);
                Livewire.dispatch('updatedVisaEndDate', dateStr);
            }
        });
    }

    document.addEventListener("livewire:navigated", function () {
        setTimeout(initPlugins, 300);
    });

    Livewire.on('initPlugins', function () {
        setTimeout(initPlugins, 300);
    });
</script>
@endsection
