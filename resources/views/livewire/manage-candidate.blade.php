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
                                <a href="{{route('candidate.create')}}" class="btn btn-sm btn-info" wire:navigate>
                                    <i class="fa fa-plus pr-1"></i> Add New
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-header">
                        <div class="row w-100 align-items-center">
                            <div class="col-md-4">
                                @php
                                    $rows = $statusWiseHoursNotInvoicedCandidateData['rows'] ?? [];
                                    $statusLabels = $statusWiseHoursNotInvoicedCandidateData['statusLabels'] ?? [];
                                    $statusTotals = $statusWiseHoursNotInvoicedCandidateData['statusTotals'] ?? [];
                                    $grandTotal = $statusWiseHoursNotInvoicedCandidateData['grandTotal'] ?? 0;
                                @endphp

                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Hours Not Invoiced</th>
                                            @foreach ($statusLabels as $label)
                                                <th>{{ $label }}</th>
                                            @endforeach
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rows as $row)
                                            <tr>
                                                <td>{{ $row['label'] }}</td>
                                                @foreach ($statusLabels as $status => $label)
                                                    <td>{{ $row['values'][$status] > 0 ? number_format($row['values'][$status], 2) : '' }}</td>
                                                @endforeach
                                                <td>{{ $row['total'] > 0 ? number_format($row['total'], 2) : '' }}</td>
                                            </tr>
                                        @endforeach

                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            @foreach ($statusLabels as $status => $label)
                                                <td>{{ number_format($statusTotals[$status], 2) }}</td>
                                            @endforeach
                                            <td>{{ number_format($grandTotal, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Amount Pending -->
                            <div class="col-md-4">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Amount Pending</th>
                                            <th>Active</th>
                                            <th>Proj End</th>
                                            <th>Clear</th>
                                            <th>Not Clear</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Before</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Aug</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Sept</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Oct</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Nov</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Dec</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Jan</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Feb</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>March (Current Month)</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Hours Due -->
                            <div class="col-md-4">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Hours Due</th>
                                            <th>Active</th>
                                            <th>Proj End</th>
                                            <th>Clear</th>
                                            <th>Not Clear</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Before</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Aug</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Sept</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Oct</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr><td>Nov</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Dec</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Jan</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr><td>Feb</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>March (Current Month)</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="route_name" value="{{ route('candidate.data') }}">
                    <div class="card-body table-responsive" wire:ignore>
                        <table id="candidate" class="table table-bordered table-striped datatable-dynamic">
                            <thead>
                                <tr>
                                    <th >#</th>
                                    <th>Candidate Name</th>
                                    <th>C Id</th>
                                    <th>Visa</th>
                                    <th>W2,C2C</th>
                                    <th>B Rate</th>
                                    <th>C Rate</th>
                                    <th>Margin</th>
                                    <th>B Vendor</th>
                                    <th>HR Ts</th>
                                    <th>Hrs Inv</th>
                                    <th>Rem Hr</th>
                                    <th>L Inv</th>
                                    <th>Last Time</th>
                                    <th>Client</th>
                                    <th>Amt inv</th>
                                    <th>Map$</th>
                                    <th>Due$</th>
                                    <th>Hrs Due</th>
                                    <th>Start Date</th>
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
