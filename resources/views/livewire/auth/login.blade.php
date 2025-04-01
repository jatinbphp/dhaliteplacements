<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>{{ config('app.name') }}</b> <br>Admin</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>
            <form wire:submit.prevent="login">
                <input type="hidden" name="_token" value="SRDeSFhrXdPIbd6lHwgQQ2g6h8MlThsHRVwRNTS3">
                <div class="form-group has-feedback">
                    <input type="email" placeholder="Email" wire:model="email" class="form-control" id="email">
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" wire:model="password" class="form-control" id="password" placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-sm-6 offset-3">
                        <button type="submit" class="btn btn-danger btn-block btn-flat">Sign In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>