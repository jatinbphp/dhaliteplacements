<div class="content-wrapper" style="min-height: 946px;">
    @include('common.header', [
        'menu' => $menu,
        'breadcrumb' =>  $breadcrumb,
        'active' => $activeMenu
    ])
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Add</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form wire:submit.prevent="addPayment">
                                    <div class="callout callout-info">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Select Vendor: <span class="text-danger">*</span></label>
                                                    <div wire:ignore>
                                                        <select class="form-control select-data" data-placeholder="Please Select Vendor" wire:model='vendorId'>
                                                            <option></option>
                                                            @foreach($vendors as $key => $value)
                                                                <option value="{{ $key }}">{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @error('vendorId') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Amount Received: <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" wire:model="amountReceived" placeholder="Please Enter Amount">
                                                    @error('amountReceived') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Amount Date: <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control datepicker" placeholder="Please Select Date Range" wire:model='amountDate' id="amountDate">
                                                    @error('amountDate') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>CEO Reference:</label>
                                                    <textarea class="form-control" placeholder="Please Enter CEO Reference" rows="3" wire:model='ceoReference'></textarea>
                                                    @error('ceoReference') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <a href="{{ route('payment') }}" wire:navigate><button class="btn btn-default" type="button">Back</button></a>
                                            <button type="submit" class="btn btn-primary">Add</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @if($candidateData)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="callout callout-info">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped datatable-dynamic">
                                                    <thead>
                                                        <tr>
                                                            <th>Candidate Name</th>
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
                                                        @foreach($candidateData as $data)
                                                            </tr>
                                                                <td>{{$data->c_name ?? ''}}</td>
                                                                <td>
                                                                    <span class="text-{{ $data->active_status_candidate == 1 ? 'success' : 'danger' }}">
                                                                        <i class="fa {{ $data->active_status_candidate == 1 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span class="text-{{ $data->project_end_status_candidate == 1 ? 'success' : 'danger' }}">
                                                                        <i class="fa {{ $data->project_end_status_candidate == 1 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                                                    </span>
                                                                </td>
                                                                <td>{{$data->project_end_status_candidate ?? ''}}</td>
                                                                <td>{{$data->rem_hrs ?? ''}}</td>
                                                                <td>{{number_format($data->amt_invoiced ?? 0, 2)}}</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td>{{$data->past_due_hours ?? ''}}</td>
                                                                <td>{{number_format($data->past_due ?? 0, 2)}}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@section('js')
<script>
    function initPlugins() {
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

        flatpickr("#amountDate", {
            dateFormat: "m-d-Y",
            locale: {
                firstDayOfWeek: 1, // Set Monday as the first day
            },
            onChange: function(selectedDates, dateStr) {
                @this.set('amountDate', dateStr);
            }
        });
    }

    document.addEventListener("livewire:navigated", function () {
        setTimeout(initPlugins, 300);
    });

    Livewire.on('initPlugins', function () {
        setTimeout(initPlugins, 300);
    });
</script>
@endsection
