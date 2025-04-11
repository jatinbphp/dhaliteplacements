<div class="d-flex justify-content-end mb-2">
    <div class="border border-success rounded px-3 py-2 bg-light">
        <strong>Total Payment Balance:</strong> {{ number_format($remaionngPaymentAmount, 2) }}
    </div>
</div>

<table class="table table-bordered table-striped datatable-dynamic">
    <thead>
        <tr>
            <th>Invoice Id</th>
            <th>Candidate Name</th>
            <th>Time From</th>
            <th>Time To</th>
            <th>Generated Date</th>
            <th>Total Hours</th>
            <th>Total Amount</th>
            <th>Mapped Amount</th>
            <th>Remaining Amount</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($candidateData) && $candidateData)
            @foreach($candidateData as $data)
                @php
                    $totalAmount = ($data['total_hours'] ?? 0) * ($data['rate'] ?? 0);
                    $mappedAmount = $data['mapped_amount'] ?? 0;
                    $remainingAmount = ($totalAmount - $mappedAmount);
                @endphp
                <tr>
                    <td>{{$data['id'] ?? '' }}</td>
                    <td>{{$data['c_name'] ?? '' }}</td>
                    <td>{{formateDate($data['from_date'] ?? '', 'Y-m-d', 'm-d-y') }}</td>
                    <td>{{formateDate($data['to_date'] ?? '', 'Y-m-d', 'm-d-y') }}</td>
                    <td>{{formateDate($data['generated_date'] ?? '', 'Y-m-d', 'm-d-y') }}</td>
                    <td>{{$data['total_hours'] ?? '' }}</td>
                    <td>{{number_format($totalAmount, 2) }}</td>
                    <td>{{number_format($mappedAmount, 2)}}</td>
                    <td>{{number_format($remainingAmount, 2) }}</td>
                    <td>
                        @if($remainingAmount == 0)
                            <span class="badge border border-success text-success bg-transparent">
                                Payment Done
                            </span>
                        @else
                            <button class="btn btn-sm btn-success" wire:click="linkPayment({{$data['id'] ?? ''}})">
                                <i class="fa fa-link"></i>
                            </button>
                            @if($remainingAmount == $totalAmount)
                                <span class="badge border border-danger text-danger bg-transparent">
                                    All Remaining
                                </span>
                            @else
                                <span class="badge border border-warning text-warning bg-transparent">
                                    Partial Done
                                </span>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>