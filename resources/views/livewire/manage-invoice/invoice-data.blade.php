<div class="invoice p-4 bg-white rounded shadow">
    <div class="row">
        <div class="col-12 text-center">
            <h4 class="fw-bold">INVOICE</h4>
        </div>
    </div>

    <!-- <div class="row invoice-info mt-3">
        <div class="col-sm-6">
            <strong>{{$candidate->ourCompany->company_name ?? ''}}</strong><br>
            {{$candidate->ourCompany->address ?? ''}}<br>
            P: {{$candidate->ourCompany->phone ?? ''}}<br>
        </div>
        <div class="col-sm-6 text-end">
            <b>Period:</b> {{$startDate}} - {{$endDate}}<br>
            <b>Invoice #:</b> {{$invoiceId ?? ''}}<br>
            <b>Date:</b>{{$today}}
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-6">
            <strong>To:</strong><br>
            {{$candidate->bCompany->company_name ?? ''}}<br>
            {{$candidate->bCompany->address ?? ''}}
        </div>
        <div class="col-6 text-end">
            <b>FOR:</b> {{$candidate->c_name}}<br>
            Consultant Position: {{$candidate->position}}<br>
            Client: {{$candidate->client}}<br>
            Rate: ${{$candidate->b_rate}} per hour
        </div>
    </div> -->

    <div class="row invoice-info mt-3">
        <div class="col-sm-6" style="width: 50%; float: left;">
            <strong>{{$candidate->ourCompany->company_name ?? ''}}</strong><br>
            {{$candidate->ourCompany->address ?? ''}}<br>
            P: {{$candidate->ourCompany->phone ?? ''}}<br>
        </div>
        <div class="col-sm-6 text-end" style="width: 50%; float: right; text-align: right;">
            <b>Period:</b> {{$startDate}} - {{$endDate}}<br>
            <b>Invoice #:</b> {{$invoiceId ?? ''}}<br>
            <b>Date:</b> {{$today}}
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-6" style="width: 50%; float: left;">
            <strong>To:</strong><br>
            {{$candidate->bCompany->company_name ?? ''}}<br>
            {{$candidate->bCompany->address ?? ''}}
        </div>
        <div class="col-6 text-end" style="width: 50%; float: right; text-align: right;">
            <b>FOR:</b> {{$candidate->c_name}}<br>
            Consultant Position: {{$candidate->position}}<br>
            Client: {{$candidate->client}}<br>
            Rate: ${{$candidate->b_rate}} per hour
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date - Description</th>
                        <th>HOURS</th>
                        <th>TOTAL AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeSheetData as $date => $hours)
                    <tr>
                        <td>{{$date}}</td>
                        <td>{{$hours}}</td>
                        <td>${{number_format(($candidate->b_rate * $hours), 2)}}</td>
                    </tr>
                    @endforeach
                    <tr class="fw-bold">
                        <th>Total Hours</th>
                        <th>{{number_format(array_sum($timeSheetData), 2)}}</th>
                        <th>${{number_format(array_sum($timeSheetData) * $candidate->b_rate, 2)}}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="callout callout-primary">
                <p><strong><i class="fa fa-info-circle"></i> Make all checks payable to Company Name</strong></p>
            </div>
        </div>
    </div>

    <div class="row mt-4 text-center">
        <p><strong>Thank you for your business!</strong></p>
    </div>
    @if(isset($pdf) && $pdf == 0)
        <div class="row">
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-primary generate-pdf-btn mr-2" wire:click="generatePdf()">
                    <i class="fas fa-print"></i> Generate PDF
                </button>
                <button type="button" class="btn btn-success submit-invoice-btn mr-2" wire:click="saveInvoice()">
                    <i class="fas fa-save"></i> Submit
                </button>
                <button type="button" class="btn btn-danger cancel-invoice-btn mr-2" onclick="$('#invoiceModal').modal('hide');">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    @endif
</div>