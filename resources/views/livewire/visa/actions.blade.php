@if(isset($type))
    @if($type == 'action')
        <div class="action-buttons">
            <a href="{{route('visa.edit', $visa->id)}}" class="btn btn-info btn-sm" wire:navigate>
                <i class="fa fa-edit"></i>
            </a>
        </div>
    @endif
    @if($type == 'status')
        <div class="onoffswitch4" wire:ignore>
            <input type="checkbox" class="onoffswitch4-checkbox"
                   id="myonoffswitch4-{{$visa->id}}"
                   name="onoffswitch4"
                   wire:model="status"
                   wire:change="updateStatus({{ $visa->id }})"
                   {{ $visa->status ? 'checked' : '' }}>
                   
            <label class="onoffswitch4-label" for="myonoffswitch4-{{$visa->id}}">
                <span class="onoffswitch4-inner"></span>
                <span class="onoffswitch4-switch"></span>
            </label>
        </div>
    @endif
@endif