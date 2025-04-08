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
                        <h3 class="card-title">{{($timeSheetId ? "Edit" : "Add")}} {{$menu}}</h3>
                    </div>
                    <form wire:submit.prevent="addTimeSheet">
                        <div class="card-body">
                            @if($selectedCandidateData)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="callout callout-info">
                                            <h5><i class="fas fa-info"></i> Candidate info:</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span><strong>Candidate Name: </strong></span><span>{{$selectedCandidateData->c_name}}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span><strong>Candidate Id: </strong></span><span>{{$selectedCandidateData->c_id}}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span><strong>Vender Name: </strong></span><span>{{$selectedCandidateData->bCompany->company_name}}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span><strong>Candidate Location: </strong></span><span>{{$selectedCandidateData->city_state}}</span>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                    <label>Select Candidate: <span class="text-danger">*</span></label>
                                        <div wire:ignore>    
                                            <select class="form-control select-data" data-placeholder="Please Select Candidate" wire:model='candidateId'>
                                                <option></option>
                                                @foreach($activeCandidate as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('lCompanyId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            @if($selectedCandidateData)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="callout callout-info">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Select Week-End Date: <span class="text-danger">*</span></label>
                                                        <input placeholder="Please Select Date" type="text" class="form-control datepicker" id="week-end-date" wire:model='weekEndDate'>
                                                        @error('weekEndDate') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                @if($weekDate)
                                                    @foreach($weekDate as $day =>$date)
                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                               <center><label>{{ucfirst($day)}}:</label></center>
                                                                <input type="number" class="form-control" wire:keyup="updateDate" wire:model="weekDays.{{ $day }}" min="0" max="24" step="0.5">
                                                                @error(strtolower($day)) <span class="text-danger">{{ $message }}</span> @enderror
                                                                <center><label>{{$date}}</label></center>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            @if($timeSheetData && count($timeSheetData))
                                                <h5><i class="fas fa-history"></i> History:</h5>
                                                <table class="table table-bordered table-striped datatable-dynamic">
                                                    <thead>
                                                        <tr>
                                                            <th>Week-End Date</th>
                                                            <th>Mon</th>
                                                            <th>Tue</th>
                                                            <th>Wed</th>
                                                            <th>Thu</th>
                                                            <th>Fri</th>
                                                            <th>Sat</th>
                                                            <th>Sun</th>
                                                            <th>Total</th>
                                                        </tr>
                                                </thead>
                                                    <tbody>
                                                        @foreach($timeSheetData as $data)
                                                            </tr>
                                                                <td>{{$data['week_end_date'] ?? ''}}</td>
                                                                @php $sumArray = []; @endphp
                                                                @foreach($data['details'] ?? [] as $dateWiseData)
                                                                    <td>{{$dateWiseData['hours'] ?? 0}}</td>
                                                                    @php $sumArray[] = $dateWiseData['hours'] ?? 0; @endphp

                                                                @endforeach
                                                                    <td>
                                                                        <strong>{{ number_format(array_sum($sumArray ?? []), 2) }}</strong>
                                                                    </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="card-footer">
                                <a href="{{ route('time-sheet') }}" wire:navigate><button class="btn btn-default" type="button">Back</button></a>
                                <button type="submit" class="btn btn-primary">{{($timeSheetId ? "Update" : "Add")}}</button>
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

        let disabledDates = @this.get('candidateAddedTimesheetDates');

        flatpickr("#week-end-date", {
            dateFormat: "m-d-Y",
            enable: [
                function(date) {
                    if(date.getDay() === 0){
                        var currentDate = flatpickr.formatDate(date, "m-d-Y");
                        return !disabledDates.includes(currentDate);
                    }
                }
            ],
            locale: {
                firstDayOfWeek: 1,
            },
            onChange: function(selectedDates, dateStr) {
                @this.set('weekEndDate', dateStr);
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