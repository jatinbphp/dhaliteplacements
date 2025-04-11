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
                                <a href="{{route('payment.create')}}" class="btn btn-sm btn-info" wire:navigate>
                                    <i class="fa fa-plus pr-1"></i> Add New
                                </a>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="route_name" value="{{ route('payment.data') }}">
                    <div class="card-body table-responsive" wire:ignore>
                        <table id="payment" class="table table-bordered table-striped datatable-dynamic">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Payment Date</th>
                                    <th>Vender Name</th>
                                    <th>Amount Received</th>
                                    <th>Paid Invoice</th>
                                    <th>Remaining Amount</th>
                                    <th>Mapped Invoice Id</th>
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
    <!-- Mapping Modal -->
    <div wire:ignore class="modal fade" id="mappingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Map Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="$('#mappingModal').modal('hide');">&times;</button>
                </div>
                <div class="modal-body" id="mappingModalBody">
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="mappingAmountModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Map Amount</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="$('#mappingAmountModal').modal('hide');">&times;</button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveMappingAmount">
                        <div class="form-group">
                            <label for="oneAmount">Amount</label>
                            <input type="number" step="0.01" wire:model.defer="mappedAmount" wire:keyup="checkAmount" id="mappedAmount" class="form-control" placeholder="Enter amount">
                        </div>
                        <span class="text-danger">{{$errorMessage}}</span>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary mr-2" onclick="$('#mappingAmountModal').modal('hide'); $('#mappedAmount').val('');">Cancel</button>
                            <button type="submit" class="btn btn-primary" @if($errorMessage) disabled @endif>Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
<script>
    Livewire.on('openMappingModal', () => {
        $('#mappingModal').modal('show');
    });
    $(document).ready(function () {
        let table = $('#payment').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $("#route_name").val(),
            },
            columns: [
                { data: null, defaultContent: '', className: 'dt-control', orderable: false },
                { data: 'id', name: 'id' },
                { data: 'amount_date', name: 'amount_date' },
                { data: 'vendor_name', name: 'vendor_name' },
                { data: 'amount', name: 'amount' },
                { data: 'paid_invoice', name: 'paid_invoice' },
                { data: 'remaining_amount', name: 'remaining_amount' },
                { data: 'mapped_invoice_id', name: 'mapped_invoice_id' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            "order": [[1, "DESC"]]
        });
    });

    function format(rowData) {
        let invoiceDetails = rowData.invoice_details;

        if (typeof invoiceDetails === 'string') {
            try {
                invoiceDetails = JSON.parse(invoiceDetails);
            } catch (e) {
                console.error("Error parsing invoice_details:", e);
                return '<div class="p-2">Invalid invoice data.</div>';
            }
        }

        if (!Array.isArray(invoiceDetails) || invoiceDetails.length === 0) {
            return '<div class="p-2">No mapped invoices.</div>';
        }

        const grouped = {};
        invoiceDetails.forEach(item => {
            const key = item.invoice_id;
            if (!grouped[key]) {
                grouped[key] = {
                    invoice_id: item.invoice_id,
                    mapped_amount: 0,
                    remaining_amount: 0,
                    total_amount: 0,
                    candidates: []
                };
            }
            grouped[key].mapped_amount += parseFloat(item.mapped_amount);
            grouped[key].remaining_amount += parseFloat(item.remaining_amount || 0);
            grouped[key].total_amount = parseFloat(item.total_amount || 0);
            grouped[key].candidates.push({
                name: item.candidate_name,
                amount: parseFloat(item.mapped_amount).toFixed(2)
            });
        });

        let html = `
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Invoice ID</th>
                        <th>Candidate</th>
                        <th>Total Amount</th>
                        <th>Mapped Amount</th>
                        <th>Remaining Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>`;

        Object.values(grouped).forEach(group => {
            const collapseId = `collapse-${rowData.id}-${group.invoice_id}`;
            const candidateNames = [...new Set(group.candidates.map(c => c.name))].join(', ');
            const remainingAmount = group.total_amount - group.mapped_amount;

            let statusLabel = '';
            if (remainingAmount === 0) {
                statusLabel = `<span style="border: 1px solid #28a745; color: #28a745; padding: 2px 6px; border-radius: 4px;">Payment Done</span>`;
            } else if (remainingAmount < group.total_amount) {
                statusLabel = `<span style="border: 1px solid #ffc107; color: #ffc107; padding: 2px 6px; border-radius: 4px;">Partial Payment</span>`;
            } else {
                statusLabel = `<span style="border: 1px solid #dc3545; color: #dc3545; padding: 2px 6px; border-radius: 4px;">All Remaining</span>`;
            }

            html += `
                <tr>
                    <td onclick="toggleCollapse('${collapseId}', this)">
                        <span class="collapse-icon" style="font-weight: bold;">+</span>
                    </td>
                    <td>${group.invoice_id}</td>
                    <td>${candidateNames}</td>
                    <td>${group.total_amount.toFixed(2)}</td>
                    <td>${group.mapped_amount.toFixed(2)}</td>
                    <td>${remainingAmount.toFixed(2)}</td>
                    <td>${statusLabel}</td>
                </tr>
                <tr id="${collapseId}" style="display: none;">
                    <td colspan="7">
                        <div style="padding: 10px;">
                            <strong>Details:</strong>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Candidate Name</th>
                                        <th>Mapped Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${group.candidates.map(c => `
                                        <tr>
                                            <td>${c.name}</td>
                                            <td>${c.amount}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>`;
        });

        html += `</tbody></table>`;
        return html;
    }

    $('#payment tbody').on('click', 'td.dt-control', function () {
        let tr = $(this).closest('tr');
        let row = $('#payment').DataTable().row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.find('td.details-control i').removeClass('fa-minus-square').addClass('fa-plus-square');
        } else {
            row.child(format(row.data())).show();
            tr.find('td.details-control i').removeClass('fa-plus-square').addClass('fa-minus-square');
        }
    });

    function toggleCollapse(id, el) {
        const row = document.getElementById(id);
        const icon = el.querySelector('.collapse-icon');

        if (row.style.display === 'none') {
            row.style.display = '';
            icon.textContent = '−';
        } else {
            row.style.display = 'none';
            icon.textContent = '+';
        }
    }

    Livewire.on('openMapping', function (event) {
        console.log(event);
        if (Array.isArray(event) && event.length > 0) {
            let data = event[0]; // Destructure

            if (data.hasOwnProperty('htmlContent')) {
                console.log(data.htmlContent);
                $('#mappingModalBody').html(data.htmlContent);
                $('#mappingModal').modal('show');
            } else {
                console.error('htmlContent is missing in the event data');
            }
        }
    });

    Livewire.on('openMappAmount', function (event) {
        $('#mappingAmountModal').modal('show');
    });

    Livewire.on('closeMappAmount', function (event) {
        $('#mappingAmountModal').modal('hide')
        $('#mappingModal').modal('hide');
        $('#payment').DataTable().ajax.reload(null, false);
    });

</script>
@endsection

