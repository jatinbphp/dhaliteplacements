<div class="content-wrapper">
    @include('common.header', [
        'menu' => $menu,
        'breadcrumb' =>  $breadcrumb,
        'active' => $activeMenu
    ])
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <div class="row w-100 align-items-center">
                            <div class="col">
                                <span class="h6 mb-0">Manage {{$menu}}</span>
                            </div>
                            <div class="col-auto">
                                <a href="{{route('time-sheet.create')}}" class="btn btn-sm btn-info" wire:navigate>
                                    <i class="fa fa-plus pr-1"></i> Add New
                                </a>
                                <button wire:click="exportTimeSheetData" class="btn btn-sm btn-warning">
                                    <i class="fa fa-solid fa-file-export pr-1"></i>Export
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-header">
                        <div class="row w-100 align-items-center">
                            <div class="col-md-4">
                                <div class="form-group">
                                <label>Select Candidate Name:</label>
                                <div wire:ignore>
                                    <select class="form-control select-data" multiple data-placeholder="Please Select Candidate Name" wire:model='selectedCandidateIds'>
                                        <option></option>
                                        @foreach($candidateData as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Select Date Range:</label>
                                    <input type="text" class="form-control datepicker" placeholder="Please Select Date Range" wire:model='dateRange' id="dateRange">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end mt-3">
                                <button type="button" class="btn btn-primary mr-2 search-button" wire:click="filter()">Search</button>
                                <button type="button" class="btn btn-secondary clear-button" wire:click="clearFilter()">Clear</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="route_name" value="{{ route('time-sheet.data') }}">
                    <div class="card-body table-responsive" wire:ignore>
                        <table id="time-sheet" class="table table-bordered table-striped datatable-dynamic">
                            <thead>
                                <tr>
                                    <th>Date Of Week</th>
                                    <th>C Id</th>
                                    <th>C Name</th>
                                    <th>Visa</th>
                                    <th>W2, C2C</th>
                                    <th>HR Ts</th>
                                    <th>Last Time</th>
                                    <th>Mon</th>
                                    <th>Tue</th>
                                    <th>Wed</th>
                                    <th>Thu</th>
                                    <th>Fri</th>
                                    <th>Sat</th>
                                    <th>Sun</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal" id="calendarModal" tabindex="-1" wire:ignore>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Hours</h5>
                    <button type="button" class="close" onclick="$('#calendarModal').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6 d-flex align-items-center justify-content-center">
                            <span class="badge badge-warning pt-2 pr-3">&nbsp;</span>
                            <span class="pl-2">Selected Week</span>
                        </div>
                        <div class="col-md-6 d-flex align-items-center justify-content-center">
                            <span class="badge badge-primary pt-2 pr-3">&nbsp;</span>
                            <span class="pl-2">Other Weeks</span>
                        </div>
                    </div>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
<script>
    $(document).ready(function () {
        Livewire.on('openCalendarModel', function () {
            $('#calendarModal').modal('show');
            let hoursByDate = @this.get('hoursByDate');
            let highlightedDates = Object.keys(hoursByDate.current || {}).map(date => ({
                date: flatpickr.parseDate(date, "Y-m-d"),
                hours: hoursByDate.current[date],
                type: 'current' // Mark as current data
            })).concat(
                Object.keys(hoursByDate.other || {}).map(date => ({
                    date: flatpickr.parseDate(date, "Y-m-d"),
                    hours: hoursByDate.other[date],
                    type: 'other' // Mark as other data
                }))
            );

            flatpickr("#calendar", {
                inline: true,
                onReady: function(selectedDates, dateStr, instance) {
                    $('.flatpickr-day').removeClass('today');
                },
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    let dateStr = fp.formatDate(dayElem.dateObj, "Y-m-d");
                    let match = highlightedDates.find(h => h.date.getTime() === dayElem.dateObj.getTime());
                    if (match) {
                        let badge = document.createElement("span");
                        badge.className = match.type === 'current' ? "badge badge-warning badge-data pt-2" : "badge badge-primary badge-data pt-2";
                        badge.innerText = match.hours + " h";
                        dayElem.appendChild(badge);
                    }
                },
                locale: {
                    firstDayOfWeek: 1,
                },
            });
        })

        $('.select-data').each(function () {
            $(this).select2({
                placeholder: $(this).data('placeholder') || 'Please select an option',
                allowClear: true
            }).on('change', function (e) {
                let selectedValues = $(this).val(); // Get selected values
                @this.set('selectedCandidateIds', selectedValues); // Update Livewire component
            });
        });

        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "m-d-Y",
            locale: {
                firstDayOfWeek: 1, // Set Monday as the first day
            },
            onChange: function(selectedDates, dateStr) {
                @this.set('dateRange', dateStr);
            }
        });

        let table = $('#time-sheet').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $("#route_name").val(),
                data: function (d) {
                    d.selectedCandidateIds = @this.get('selectedCandidateIds');
                    d.dateRange = @this.get('dateRange');
                }
            },
            columns: [
                { data: 'week_end_date', name: 'week_end_date' },
                { data: 'c_id', name: 'c_id' },
                { data: 'c_name', name: 'c_name' },
                { data: 'visa', name: 'visa' },
                { data: 'candidate_type', name: 'candidate_type' },
                { data: 'hr_ts', name: 'hr_ts' },
                { data: 'last_time', name: 'last_time' },
                { 
                    data: 'mon', 
                    name: 'mon',
                    render: function(data, type, row) {
                        let date = row['mon_date'] ? formatDate(row['mon_date']) : ''; 
                        return `<span title="Date: ${date}" data-bs-toggle="tooltip">${data ?? '-'}</span>`;
                    }
                },
                { 
                    data: 'tue', 
                    name: 'tue',
                    render: function(data, type, row) {
                        let date = row['tue_date'] ? formatDate(row['tue_date']) : ''; 
                        return `<span title="Date: ${date}" data-bs-toggle="tooltip">${data ?? '-'}</span>`;
                    }
                },
                { 
                    data: 'wed', 
                    name: 'wed',
                    render: function(data, type, row) {
                        let date = row['wed_date'] ? formatDate(row['wed_date']) : ''; 
                        return `<span title="Date: ${date}" data-bs-toggle="tooltip">${data ?? '-'}</span>`;
                    }
                },
                { 
                    data: 'thu', 
                    name: 'thu',
                    render: function(data, type, row) {
                        let date = row['thu_date'] ? formatDate(row['thu_date']) : ''; 
                        return `<span title="Date: ${date}" data-bs-toggle="tooltip">${data ?? '-'}</span>`;
                    }
                },
                { 
                    data: 'fri', 
                    name: 'fri',
                    render: function(data, type, row) {
                        let date = row['fri_date'] ? formatDate(row['fri_date']) : ''; 
                        return `<span title="Date: ${date}" data-bs-toggle="tooltip">${data ?? '-'}</span>`;
                    }
                },
                { 
                    data: 'sat', 
                    name: 'sat',
                    render: function(data, type, row) {
                        let date = row['sat_date'] ? formatDate(row['sat_date']) : ''; 
                        return `<span title="Date: ${date}" data-bs-toggle="tooltip">${data ?? '-'}</span>`;
                    }
                },
                { 
                    data: 'sun', 
                    name: 'sun',
                    render: function(data, type, row) {
                        let date = row['sun_date'] ? formatDate(row['sun_date']) : ''; 
                        return `<span title="Date: ${date}" data-bs-toggle="tooltip">${data ?? '-'}</span>`;
                    }
                },
                { 
                    data: 'total_hours', 
                    name: 'total_hours',
                    render: function(data, type, row) {
                        let fromDate = row['sun_date'] ? formatDate(row['mon_date']) : ''; 
                        let toDate = row['sun_date'] ? formatDate(row['sun_date']) : ''; 
                        return `<span title="From: ${fromDate} - To: ${toDate}" data-bs-toggle="tooltip">${data ?? '-'}</span>`;
                    }
                },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            "order": [[0, "DESC"]]
        });

        Livewire.on('refreshDataTable', function () {
            table.ajax.reload();
        });

        function formatDate(dateStr) {
            let [year, month, day] = dateStr.split("-");
            return `${month}-${day}-${year}`;
        }
    });
</script>
@endsection