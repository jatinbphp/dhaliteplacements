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
                    <input type="hidden" id="route_name" value="{{ route('vendor-wise.data') }}">
                    <div class="card-body table-responsive" wire:ignore>
                        <table id="vendor-wise" class="table table-bordered table-striped datatable-dynamic">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th >Vendor</th>
                                    <th>Total Candidate</th>
                                    <th>Active</th>
                                    <th>Project End</th>
                                    <th>Rem Hr</th>
                                    <th>Total Hr Due</th>
                                    <th>Amt invoiced</th>
                                    <th>Map$</th>
                                    <th>Over Due</th>
                                    <th>Past Due Hr</th>
                                    <th>Past Due</th>
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
        let table = $('#vendor-wise').DataTable({
            processing: true,
            serverSide: true,
            ajax: $("#route_name").val(),
            columns: [
                { data: null, defaultContent: '', className: 'dt-control', orderable: false },
                { data: 'vendor_name', name: 'vendor_name' },
                { data: 'total_candidate', name: 'total_candidate' },
                { data: 'active_status_candidate', name: 'active_status_candidate' },
                { data: 'project_end_status_candidate', name: 'project_end_status_candidate' },
                { data: 'rem_hrs', name: 'rem_hrs' },
                { data: 'hr_due', name: 'hr_due' },
                { data: 'amt_invoiced', name: 'amt_invoiced' },
                { data: 'map_amount', name: 'map_amount' },
                { data: 'over_due', name: 'over_due' },
                { data: 'post_due_hrs', name: 'post_due_hrs' },
                { data: 'past_due', name: 'past_due' },
            ],
            "order": [[0, "DESC"]]
        });

        function format(rowData) {
            let candidates = rowData.candidates || [];
            if (candidates.length === 0) {
                return `<div class="p-2">No candidates available</div>`;
            }

            let rows = candidates.map((c, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${c.c_name}</td>
                    <td>
                        <span class="text-${c.active_status_candidate == 1 ? 'success' : 'danger'}">
                            <i class="fa ${c.active_status_candidate == 1 ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        </span>
                    </td>
                    <td>
                        <span class="text-${c.project_end_status_candidate == 1 ? 'success' : 'danger'}">
                            <i class="fa ${c.project_end_status_candidate == 1 ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        </span>
                    </td>
                    <td>${parseFloat(c.rem_hrs || 0).toFixed(2)}</td>
                    <td></td>
                    <td>${Number(c.amt_invoiced || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td></td>
                    <td></td>
                    <td>${parseFloat(c.past_due_hours || 0).toFixed(2)}</td>
                    <td>${Number(c.past_due || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                </tr>
            `).join('');

            return `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Candidate Name</th>
                                <th>Active</th>
                                <th>Project End</th>
                                <th>Rem Hr</th>
                                <th>Total Hr Due</th>
                                <th>Amt Invoiced</th>
                                <th>Map$</th>
                                <th>Over Due</th>
                                <th>Past Due Hr</th>
                                <th>Past Due</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
            `;
        }


        $('#vendor-wise tbody').on('click', 'td.dt-control', function () {
            let tr = $(this).closest('tr');
            let row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });

    </script>
@endsection
