@if(isset($type))
    @if($type == 'action')
        <div class="action-buttons">
            <a href="{{route('time-sheet.edit', $timesheet->id)}}" class="btn btn-info btn-sm" wire:navigate>
                <i class="fa fa-edit"></i>
            </a>
            <button class="btn btn-warning btn-sm" wire:click="openCalendar('{{ $timesheet->id }}')">
                <i class="fa fa-eye"></i>
            </button>
        </div>
    @endif
@endif
