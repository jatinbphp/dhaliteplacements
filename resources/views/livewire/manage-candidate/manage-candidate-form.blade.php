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
                        <h3 class="card-title">{{($companyId) ? 'Edit' : 'Add' }} {{$menu}}</h3>
                    </div>
                    <form wire:submit.prevent="updateCompany">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Type:</label>
                                        <select class="form-control select2" wire:model="candidateType"  wire:change='updateCandidateType()'>
                                            @foreach($candidateOptions as $key => $value)
                                                <option {{($candidateType == $key) ? 'selected' : ''}} value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if(in_array($candidateType, ['w2', 'w2_c2c']))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Select L Company:</label>
                                            <select class="form-control select2" wire:change='updateLCompany()' wire:model='lCompanyId'>
                                                <option value="">Please Select L Company</option>
                                                @foreach($lCompanyData as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Address:</label>
                                            <input type="text" placeholder="Please Enter P Company Address" wire:model="selectedLCompanyAddress" class="form-control">
                                            @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>L Rate:</label>
                                            <input type="text" placeholder="Please Enter L Rate" class="form-control">
                                            @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>L Agreement / w9:</label>
                                            <div class="onoffswitch4" wire:ignore>
                                                <input type="checkbox" name="onoffswitch4" class="onoffswitch4-checkbox" id="myonoffswitch4" wire:model="status">
                                                <label class="onoffswitch4-label" for="myonoffswitch4">
                                                    <span class="onoffswitch4-inner" data-on-text="Yes" data-off-text="No"></span>
                                                    <span class="onoffswitch4-switch"></span>
                                                </label>
                                            </div>
                                            @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>C id:</label>
                                        <input type="text" placeholder="Please Enter CId" class="form-control">
                                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>C Name:</label>
                                        <input type="text" placeholder="Please Enter CName" class="form-control">
                                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Visa Status:</label>
                                        <select class="form-control select2">
                                            <option value="">Please Select Visa Status</option>
                                            @foreach($visaStatus as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Visa Status:</label>
                                        <select class="form-control select2">
                                            <option value="">Please Select Visa Status</option>
                                            @foreach($visaStatus as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date:</label>
                                        <input type="text" class="form-control datepicker" placeholder="Select Date">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('l-company') }}" wire:navigate><button class="btn btn-default" type="button">Back</button></a>
                                <button type="submit" class="btn btn-primary">{{($companyId) ? 'Update' : 'Add' }}</button>
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
    $(document).ready(function () {
        flatpickr(".datepicker", {
            dateFormat: "m-d-Y",
        });
    });
</script>
@endsection
