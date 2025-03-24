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

                    <input type="hidden" id="route_name" value="{{ route('candidate.data') }}">
                    <div class="card-body table-responsive" wire:ignore>
                        <table id="candidate" class="table table-bordered table-striped datatable-dynamic">
                            <thead>
                                <tr>
                                    <th >#</th>
                                    <th>Candidate Name</th>
                                    <th>Visa</th>
                                    <th>W2,C2C</th>
                                    <th>B Rate</th>
                                    <th>C Rate</th>
                                    <th>Margin</th>
                                    <th>B Vendor</th>
                                    <th>HR Ts</th>
                                    <th>Hrs Inv</th>
                                    <th>Rem Hr</th>
                                    <th>L Invoiced Date</th>
                                    <th>Last Time</th>
                                    <th>Client</th>
                                    <th>Amt inv</th>
                                    <th>Mapped Rec Amt</th>
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
