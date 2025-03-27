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
                    <input type="hidden" id="route_name" value="{{ route('visa-candidate.data') }}">
                    <div class="card-body table-responsive" wire:ignore>
                        <table id="visa-candidate" class="table table-bordered table-striped datatable-dynamic">
                            <thead>
                                <tr>
                                    <th >#</th>
                                    <th >Created Date</th>
                                    <th >C Id</th>
                                    <th>Candidate Name</th>
                                    <th>Status</th>
                                    <th>Visa</th>
                                    <th>W2,C2C</th>
                                    <th>Start Date</th>
                                    <th>Last Time Entry</th>
                                    <th>C Agmt.</th>
                                    <th>Mec Sent Date</th>
                                    <th>Lap Rec.</th>
                                    <th>Address</th>
                                    <th>Visa Start</th>
                                    <th>Visa End</th>
                                    <th>Id Start</th>
                                    <th>Id End</th>
                                    <th>Rem Visa</th>
                                    <th>Rem Id</th>
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


