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
                        </div>
                    </div>
                    <div class="card-header">
                        <div class="row w-100 align-items-center">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Select Date Range:</label>
                                    <input type="text" class="form-control datepicker" placeholder="Please Select Date Range" wire:model='dateRange' id="dateRange">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Billing Options: <span class="text-danger">*</span></label>
                                    <div wire:ignore>    
                                        <select class="form-control select-data" data-placeholder="Please Select Billing Option Terms" wire:model='billingTypeId'>
                                            <option></option>
                                            @foreach($billingOptions as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('billingTypeId') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end mt-3">
                                <button type="button" class="btn btn-primary mr-2 search-button" wire:click="filter()">Search</button>
                                <button type="button" class="btn btn-secondary clear-button" wire:click="clearFilter()">Clear</button>
                            </div>
                            <div class="col-md-6">
                                <div class="container mt-4">
                                    <div class="card card-info card-outline">
                                        <div class="card-body" wire:ignore>
                                            <table class="table table-hover">
                                                <tr>
                                                    <th># of Total Candidates</th>
                                                    <td id="totalEmployeeCount">-</td>
                                                </tr>
                                                <tr>
                                                    <th># of Total Candidates whose invoice has not been generated</th>
                                                    <td id="totalUninvoicedCandidates">-</td>
                                                </tr>
                                                <tr>
                                                    <th># of Total Timesheet hours in Timecard</th>
                                                    <td id="totalTotalHours">-</td>
                                                </tr>
                                                <tr>
                                                    <th># of Total generated hours in invoice</th>
                                                    <td id="totalInvoicedHours">-</td>
                                                </tr>
                                            </table>
                                            <div class="mt-3">
                                                <div class="float-left">
                                                    <span class="badge bg-danger pr-2">&nbsp;</span>
                                                    <span class="text-bold align-middle">Previous Invoice Pending</span>
                                                </div> 
                                                <div class="float-left ml-2">
                                                    <span class="badge bg-info pr-2 ">&nbsp;</span>
                                                    <span class="text-bold align-middle">All Dates Invoiced</span>  
                                                </div> 
                                                <div class="float-left ml-2">
                                                    <span class="badge bg-warning pr-2">&nbsp;</span>
                                                    <span class="text-bold align-middle">Partial Dates Invoiced</span> 
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="route_name" value="{{ route('invoice.data') }}">
                    <div class="card-body table-responsive" wire:ignore>
                        <table id="invoice" class="table table-bordered datatable-dynamic">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Full Name</th>
                                    <th>Vendor Company Name</th>
                                    <th>User Type</th>
                                    <th>Total Hours</th>
                                    <th>Inv Hours</th>
                                    <th>Remaining Hours</th>
                                   <!--  <th>Time From, Time To</th>
                                    <th>#inv Id</th>
                                    <th>Generated Date</th> -->
                                    <th >Action</th>
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
    <div class="modal" id="invoiceModal" tabindex="-1" wire:ignore>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Hours</h5>
                    <button type="button" class="close" onclick="$('#invoiceModal').modal('hide');">&times;</button>
                </div>
                <div class="modal-body" id="invoiceModalBody">
                    <div class="row mb-3">
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
        Livewire.on('openInvoiceModel', function (event) {
            if (Array.isArray(event) && event.length > 0) {
                let data = event[0]; // Get the first object from the array

                if (data.hasOwnProperty('htmlContent')) {
                    $('#invoiceModalBody').html(data.htmlContent);
                    $('#invoiceModal').modal('show');
                } else {
                    console.error('htmlContent key is missing in the event data');
                }
            }
        });
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

        let table = $('#invoice').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $("#route_name").val(),
                data: function (d) {
                    d.selectedCandidateIds = @this.get('selectedCandidateIds');
                    d.dateRange = @this.get('dateRange');
                    d.billingOption = @this.get('billingTypeId');
                },
                dataSrc: function(json) {
                    $('#totalEmployeeCount').text(json.totalCandidates);
                    $('#totalTotalHours').text(json.totalTotalHours);
                    $('#totalInvoicedHours').text(json.totalInvoicedHours);
                    $('#totalUninvoicedCandidates').text(json.totalUninvoicedCandidates);
                    return json.data;
                }
            },
            columns: [
                {
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: ''
                },
                { data: 'c_name', name: 'c_name' },
                { data: 'vendor_company_name', name: 'vendor_company_name' },
                { data: 'billing_type', name: 'billing_type' },
                { data: 'total_hours', name: 'total_hours' },
                { data: 'invoiced_hours', name: 'invoiced_hours' },
                { data: 'remaining_hours', name: 'remaining_hours' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.status == '1') {
                    $(row).addClass('table-danger');
                } else if (data.status == '2') {
                    $(row).addClass('table-info');
                } else if (data.status == '3') {
                    $(row).addClass('table-warning');
                }
            },
            "order": [[0, "DESC"]]
        });

        function format(d) {
            let uid = `toggleDateWise_${d.id}`; // unique toggle ID based on row ID
            let summaryHtml = generateSummaryView(d);
            let detailedHtml = generateDateWiseView(d);

            return `
                <div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input toggle-date-wise" type="checkbox" id="${uid}" data-rowid="${d.id}">
                        <label class="form-check-label" for="${uid}">Show Date Wise</label>
                    </div>
                    <div id="content-${d.id}">
                        ${summaryHtml}
                    </div>
                </div>
            `;
        }

        function generateSummaryView(d) {
            let rows = '';

            d.time_sheets.forEach((ts, index) => {
                let totalHours = 0;
                let dates = ts.details.map(item => item.date_of_day);
                let startDate = dates.length ? dates[0] : '-';
                let endDate = ts.week_end_date || '-';

                ts.details.forEach(detail => {
                    totalHours += parseFloat(detail.hours || 0);
                });

                rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${startDate}</td>
                        <td>${endDate}</td>
                        <td>${totalHours.toFixed(2)}</td>
                    </tr>
                `;
            });

            return `
                <table class="table table-bordered">
                    <thead >
                        <tr>
                            <th>#</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            `;
        }

        function generateDateWiseView(d) {
            let content = '';

            d.time_sheets.forEach((ts, tsIndex) => {
                let rows = '';
                let totalHours = 0;

                ts.details.forEach((detail, index) => {
                    totalHours += parseFloat(detail.hours || 0);
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${detail.date_of_day}</td>
                            <td>${detail.day_name}</td>
                            <td>${detail.hours}</td>
                            <td>${detail.invoice_id ?? '-'}</td>
                            <td>${detail.invoice_id ? `<span class="badge bg-success">Invoiced</span>` : `<span class="badge bg-secondary">Pending</span>`}</td>
                        </tr>
                    `;
                });

                content += `
                    <div class="mb-3">
                        <h6 class="mb-1">Timesheet #${tsIndex + 1} (Week End: <strong>${ts.week_end_date}</strong>)</h6>
                        <div><strong>Total Hours:</strong> ${totalHours.toFixed(2)}</div>
                        <table class="table table-bordered">
                            <thead >
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Hours</th>
                                    <th>Invoice ID</th>
                                    <th>Invoice Status</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                `;
            });

            return content;
        }
    
        table.on('click', 'td.dt-control', function () {
            let tr = $(this).closest('tr');
            let row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(format(row.data())).show();
                tr.addClass('shown');

                let rowId = row.data().id;
                let rowData = row.data();

                // Now bind the toggle switch and pass in rowData
                let $toggle = $(`#toggleDateWise_${rowId}`);
                $toggle.off('change').on('change', function () {
                    const isChecked = $(this).is(':checked');
                    const $content = $(`#content-${rowId}`);
                    if (isChecked) {
                        $content.html(generateDateWiseView(rowData));
                    } else {
                        $content.html(generateSummaryView(rowData));
                    }
                });
            }
        });


        Livewire.on('refreshDataTable', function () {
            table.ajax.reload();
        });

        window.addEventListener('closeModal', () => {
            $('#invoiceModal').modal('hide'); // Close modal
        });
    });
</script>
@endsection