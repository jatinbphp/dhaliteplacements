@if(isset($type))
    @if($type == 'action')
        <div class="action-buttons">
            <a href="{{route('visa-candidate.edit', $candidate->id)}}" class="btn btn-info btn-sm" wire:navigate>
                <i class="fa fa-edit"></i>
            </a>
        </div>
    @endif
@endif
