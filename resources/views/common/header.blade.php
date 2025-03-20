<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $menu }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    @if($breadcrumb)
                        @foreach($breadcrumb as $item)
                            <li class="breadcrumb-item"><a href="{{ route($item['route']) }}" wire:navigate>{{ $item['title'] }}</a></li>
                        @endforeach
                    @endif
                    <li class="breadcrumb-item active">{{ $active }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>  