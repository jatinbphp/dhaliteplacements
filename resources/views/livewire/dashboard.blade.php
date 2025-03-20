<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6 mt-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{$totalCompanyCounts['l_company'] ?? 0}}</h3>
                            <p>Total L Company</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-building"></i>
                        </div>
                        <a href="{{route('l-company')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6 mt-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{$totalCompanyCounts['c_company'] ?? 0}}</h3>
                            <p>Total C Company</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-building"></i>
                        </div>
                        <a href="{{route('b-company')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6 mt-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{$totalCompanyCounts['p_company'] ?? 0}}</h3>
                            <p>Total P Company</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-building"></i>
                        </div>
                        <a href="{{route('p-company')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6 mt-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{$totalCompanyCounts['our_company'] ?? 0}}</h3>
                            <p>Total Our Company</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-building"></i>
                        </div>
                        <a href="{{route('our-company')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>