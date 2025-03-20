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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name:</label>
                                        <input type="text" placeholder="Please Enter Our Company Name" wire:model="name" class="form-control">
                                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address:</label>
                                        <input type="text" placeholder="Please Enter Our Company Address" wire:model="address" class="form-control">
                                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone:</label>
                                        <input type="text" placeholder="Please Enter Our Company Phone" wire:model="phone" class="form-control">
                                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status:</label>
                                        <div class="onoffswitch4" wire:ignore>
                                            <input type="checkbox" {{($status) ? 'checked' : ''}} name="onoffswitch4" class="onoffswitch4-checkbox" id="myonoffswitch4" wire:model="status">
                                            <label class="onoffswitch4-label" for="myonoffswitch4">
                                                <span class="onoffswitch4-inner"></span>
                                                <span class="onoffswitch4-switch"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('our-company') }}" wire:navigate><button class="btn btn-default" type="button">Back</button></a>
                                <button type="submit" class="btn btn-primary">{{($companyId) ? 'Update' : 'Add' }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
