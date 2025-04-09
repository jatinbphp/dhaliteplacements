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
                    <form wire:submit.prevent="updateCompany">
                        <div class="card-body">
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
                                        <label>Select Candidate: <span class="text-danger">*</span></label>
                                        <div>
                                            <select class="form-control select-data" data-placeholder="Please Select Candidate" wire:model='candidateId'>
                                                <option></option>
                                                @foreach($candidates as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('candidateId') <span class="text-danger">{{ $message }}</span> @enderror
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
    }

    document.addEventListener("livewire:navigated", function () {
        setTimeout(initPlugins, 300);
    });

    Livewire.on('initPlugins', function () {
        setTimeout(initPlugins, 300);
    });
</script>
@endsection
