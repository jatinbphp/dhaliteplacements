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
                    <input type="hidden" id="route_name" value="{{ route('date-wise-invoice-tracking.data') }}">
                    <div class="card-body table-responsive" wire:ignore>
                        <table id="date-wise-invoice-tracking" class="table table-bordered table-striped datatable-dynamic">
                            <thead>
                                <tr>
                                    <th >Inv Date</th>
                                    <th >Inv Id</th>
                                    <th>Candidate Name</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>B Vender</th>
                                    <th>Time From</th>
                                    <th>Time To</th>
                                    <th>Inv Hr</th>
                                    <th>Rate</th>
                                    <th>Inv Amt</th>
                                    <th>Map$</th>
                                    <th>Due$</th>
                                    <th>Due In</th>
                                    <th>Sent Days</th>
                                    <th>Net Terms</th>
                                    <th >Payment Id</th>
                                    <th >TTL Hr Due</th>
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
        let table = $('#date-wise-invoice-tracking').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $("#route_name").val(),
            },
            columns: [
                { data: 'generated_date', name: 'generated_date'},
                { data: 'id', name: 'id' },
                { data: 'candidate_name', name: 'candidate_name', searchable: false},
                { data: 'status', name: 'candidate_status' },
                { data: 'type', name: 'candidate_type' },
                { data: 'b_vender', name: 'b_companies.company_name' },
                { data: 'from_date', name: 'from_date' },
                { data: 'to_date', name: 'to_date' },
                { data: 'inv_hr', name: 'inv_hr' },
                { data: 'rate', name: 'rate' },
                { data: 'inv_amt', name: 'inv_amt' },
                { data: 'map', name: 'map' },
                { data: 'due', name: 'due' },
                { data: 'due_in', name: 'due_in' },
                { data: 'sent_days', name: 'sent_days' },
                { data: 'net_terms', name: 'net_terms' },
                { data: 'payment_id', name: 'payment_id' },
                { data: 'ttl_hr_due', name: 'ttl_hr_due' },
            ],
            "order": [[13, "ASC"]]
        });

        Livewire.on('refreshDataTable', function () {
            table.ajax.reload();
        });
    });
</script>
@endsection