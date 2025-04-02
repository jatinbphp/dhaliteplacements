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
                                        <div class="card-body">
                                            <table class="table table-hover">
                                                <tr>
                                                    <th>Amount Invoiced</th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th>Amount Received</th>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <th>Amount Due</th>
                                                    <td>-</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="route_name" value="{{ route('invoice-tracking.data') }}">
                    <div class="card-body table-responsive" wire:ignore>
                        <table id="invoice-tracking" class="table table-bordered table-striped datatable-dynamic">
                            <thead>
                                <tr>
                                    <th >Full Name</th>
                                    <th>Vendor Company Name</th>
                                    <th>Total Hours</th>
                                    <th>Generated Hours</th>
                                    <th>Time From, Time To</th>
                                    <th>#inv Id</th>
                                    <th>#inv No</th>
                                    <th>Rate</th>
                                    <th>Generated Date</th>
                                    <th>Invoice Amount</th>
                                    <th>Amount Received</th>
                                    <th>Amount Due</th>
                                    <th>Mail</th>
                                    <th>Mapping</th>
                                    <th>Mapping Date</th>
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
</div>
@section('js')
<script>
    $(document).ready(function () {
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

        let table = $('#invoice-tracking').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $("#route_name").val(),
                data: function (d) {
                    d.selectedCandidateIds = @this.get('selectedCandidateIds');
                    d.dateRange = @this.get('dateRange');
                    d.billingOption = @this.get('billingTypeId');
                }
            },
            columns: [
                { data: 'c_name', name: 'c_name' },
                { data: 'vendor_company_name', name: 'vendor_company_name' },
                { data: 'total_hours', name: 'total_hours' },
                { data: 'generated_hours', name: 'generated_hours' },
                { data: 'time_from_to', name: 'time_from_to' },
                { data: 'invoice_id', name: 'invoice_id' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'rate', name: 'rate' },
                { data: 'generated_date', name: 'generated_date' },
                { data: 'invoice_amount', name: 'invoice_amount' },
                { data: 'received_amount', name: 'received_amount' },
                { data: 'amount_due', name: 'amount_due' },
                { data: 'mail', name: 'mail' },
                { data: 'mapping', name: 'mapping' },
                { data: 'mapping_date', name: 'mapping_date' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            "order": [[0, "DESC"]]
        });

        Livewire.on('refreshDataTable', function () {
            table.ajax.reload();
        });
    });
</script>
@endsection