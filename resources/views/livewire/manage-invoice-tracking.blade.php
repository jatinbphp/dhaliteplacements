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
                                                    <td id="totalInvoiceAmount">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Amount Received</th>
                                                    <td id="totalReceivedAmount">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Amount Due</th>
                                                    <td id="totalDueAmount">-</td>
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
                                    <th></th>
                                    <th >Full Name</th>
                                    <th>Vendor Company Name</th>
                                    <th>Total Hours</th>
                                    <th>Generated Hours</th>
                                    <!-- <th>Rate</th> -->
                                    <th>Invoice Amount</th>
                                    <th>Amount Received</th>
                                    <th>Amount Due</th>
                                    <!-- <th>Mail</th>
                                    <th>Mapping</th>
                                    <th>Mapping Date</th> -->
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
                },
                dataSrc: function(json) {
                    $('#totalInvoiceAmount').text(json.totalInvoiceAmount);
                    $('#totalDueAmount').text(json.totalDueAmount);
                    $('#totalReceivedAmount').text(json.totalReceivedAmount);
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
                { data: 'total_hours', name: 'total_hours' },
                { data: 'generated_hours', name: 'generated_hours' },
                // { data: 'b_rate', name: 'b_rate' },
                { data: 'invoice_amount', name: 'invoice_amount' },
                { data: 'received_amount', name: 'received_amount' },
                { data: 'amount_due', name: 'amount_due' },
                // { data: 'mail', name: 'mail' },
                // { data: 'mapping', name: 'mapping' },
                // { data: 'mapping_date', name: 'mapping_date' },
            ],
            "order": [[0, "DESC"]]
        });

        function format(d) {
            let uid = `toggleInvoiceWise_${d.id}`;
            let summaryHtml = generateInvoiceSummaryView(d);
            let detailedHtml = generateInvoiceDateWiseView(d);

            return `
                <div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input toggle-invoice-wise" type="checkbox" id="${uid}" data-rowid="${d.id}">
                        <label class="form-check-label" for="${uid}">Show Date Wise</label>
                    </div>
                    <div id="content-${d.id}">
                        ${summaryHtml}
                    </div>
                </div>
            `;
        }

        function groupDetailsByInvoice(details = []) {
            const result = {};
            details.forEach(detail => {
                if (!detail.invoice_id) return;
                const key = detail.invoice_id;
                if (!result[key]) result[key] = [];
                result[key].push(detail);
            });
            return result;
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            if (isNaN(date)) return '-';
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const year = date.getFullYear();
            return `${month}-${day}-${year}`;
        }


        function generateInvoiceSummaryView(d) {
            const allDetails = (d.time_sheets || []).flatMap(ts => ts.details || []);
            const grouped = groupDetailsByInvoice(allDetails);

            let rows = '';
            let index = 1;

            for (const [invoiceId, details] of Object.entries(grouped)) {
                const invoice = details[0]?.invoice ?? null;
                const fromDate = formatDate(invoice?.from_date);
                const toDate = formatDate(invoice?.to_date);
                const generatedDate = formatDate(invoice?.generated_date);
                const totalHours = details.reduce((sum, item) => sum + parseFloat(item.hours || 0), 0);
                const rate = invoice?.rate

                rows += `
                    <tr>
                        <td>${index++}</td>
                        <td>${invoiceId}</td>
                        <td>${fromDate}</td>
                        <td>${toDate}</td>
                        <td>${generatedDate}</td>
                        <td>${rate}</td>
                        <td>${totalHours.toFixed(2)}</td>
                        <td><span class="badge bg-success">Invoiced</span></td>
                    </tr>
                `;
            }

            return `
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice ID</th>
                            <th>Time From</th>
                            <th>Time To</th>
                            <th>Generated Date</th>
                            <th>Rate</th>
                            <th>Total Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            `;
        }

        function generateInvoiceDateWiseView(d) {
            const allDetails = (d.time_sheets || []).flatMap(ts => ts.details || []);
            const grouped = groupDetailsByInvoice(allDetails);

            let content = '';

            for (const [invoiceId, details] of Object.entries(grouped)) {
                let rows = '';
                let total = 0;

                details.forEach((item, idx) => {
                    total += parseFloat(item.hours || 0);
                    rows += `
                        <tr>
                            <td>${idx + 1}</td>
                            <td>${item.date_of_day}</td>
                            <td>${item.day_name}</td>
                            <td>${item.hours}</td>
                            <td>${item.invoice_id || '-'}</td>
                            <td>
                                ${item.invoice_id 
                                    ? `<span class="badge bg-success">Invoiced</span>` 
                                    : `<span class="badge bg-secondary">Pending</span>`}
                            </td>
                        </tr>
                    `;
                });

                content += `
                    <div class="mb-3">
                        <h6 class="mb-1">Invoice: ${invoiceId === 'unbilled' ? 'Unbilled Entries' : invoiceId}</h6>
                        <div><strong>Total Hours:</strong> ${total.toFixed(2)}</div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Hours</th>
                                    <th>Invoice ID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                `;
            }

            return content || `<div>No time sheet entries found.</div>`;
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
                let $toggle = $(`#toggleInvoiceWise_${rowId}`);
                $toggle.off('change').on('change', function () {
                    const isChecked = $(this).is(':checked');
                    const $content = $(`#content-${rowId}`);
                    if (isChecked) {
                        $content.html(generateInvoiceDateWiseView(rowData));
                    } else {
                        $content.html(generateInvoiceSummaryView(rowData));
                    }
                });
            }
        });

        Livewire.on('refreshDataTable', function () {
            table.ajax.reload();
        });
    });
</script>
@endsection