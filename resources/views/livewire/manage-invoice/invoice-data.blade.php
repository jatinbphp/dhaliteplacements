<div style="padding: 20px; background: white; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); color: black;">
    <div style="text-align: center; font-weight: bold;">
        <h4 style="margin: 0;">INVOICE</h4>
    </div>

    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>{{$candidate->ourCompany->company_name ?? ''}}</strong><br>
                {{$candidate->ourCompany->address ?? ''}}<br>
                P: {{$candidate->ourCompany->phone ?? ''}}<br>
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                <b>Period:</b> {{$startDate}} - {{$endDate}}<br>
                <b>Invoice #:</b> {{$invoiceId ?? ''}}<br>
                <b>Date:</b> {{$today}}
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>To:</strong><br>
                {{$candidate->bCompany->company_name ?? ''}}<br>
                {{$candidate->bCompany->address ?? ''}}
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                <b>FOR:</b> {{$candidate->c_name}}<br>
                Consultant Position: {{$candidate->position}}<br>
                Client: {{$candidate->client}}<br>
                Rate: ${{$candidate->b_rate}} per hour
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px;">
        <table style="width: 100%; border-spacing: 0; table-layout: fixed; border: 1px solid black;">
            <thead>
                <tr style="background: #f0f0f0;">
                    <th style="border: 1px solid black; padding: 8px; text-align: left;">Date - Description</th>
                    <th style="border: 1px solid black; padding: 8px; text-align: center;">HOURS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timeSheetData as $date => $hours)
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">{{$date}}</td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">{{$hours}}</td>
                </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <th style="border: 1px solid black; padding: 8px;">Total Hours</th>
                    <th style="border: 1px solid black; padding: 8px; text-align: center;">{{number_format(array_sum($timeSheetData), 2)}}</th>
                </tr>
                <tr style="font-weight: bold;">
                    <th style="border: 1px solid black; padding: 8px;">Total Amount</th>
                    <th style="border: 1px solid black; padding: 8px; text-align: center;">${{number_format(array_sum($timeSheetData) * $candidate->b_rate, 2)}}</th>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px; padding: 10px; background: #cce5ff; border-left: 5px solid #004085;">
        <p><strong> Make all checks payable to Company Name</strong></p>
    </div>

    <div style="margin-top: 20px; text-align: center;">
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (document.body.classList.contains("darkmode")) {
            document.querySelector("div").style.background = "#333";
            document.querySelector("div").style.color = "#fff";
            document.querySelector("table").style.border = "1px solid white";
            document.querySelectorAll("th, td").forEach(el => el.style.border = "1px solid white");
            document.querySelector("tr").style.background = "#444";
            document.querySelector("div[style*='background: #cce5ff']").style.background = "#444";
        }
    });
</script>


<!-- <div class="invoice p-4 bg-white rounded shadow">
    <div class="row">
        <div class="col-12 text-center">
            <h4 class="fw-bold">INVOICE</h4>
        </div>
    </div>

    <div class="row invoice-info mt-3">
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
</div> -->