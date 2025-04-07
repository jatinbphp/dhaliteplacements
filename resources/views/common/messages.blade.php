<script>
    document.addEventListener("DOMContentLoaded", function () {
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        @if(Session::has('success'))
            Toast.fire({
                iconHtml: '<img src="{{asset('sweetalert2/image/success.png')}}" width="30">',
                title: '{{Session::get('success')}}',
                iconColor: 'transparent'
              })
        @elseif(Session::has('danger'))
            Toast.fire({
                icon: 'error',
                title: '{{Session::get('danger')}}'
            });
        @elseif(Session::has('warning'))
            Toast.fire({
                icon: 'warning',
                title: '{{Session::get('danger')}}'
            });
        @endif
    });
</script>