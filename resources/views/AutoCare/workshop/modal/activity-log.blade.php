@if($logs->isEmpty())
    <p>No activity logs found.</p>
@else
    @foreach($logs as $log)
        <div class="mb-3 border-bottom pb-2">
            <strong>{{ $log->action }}</strong> 
            by <span class="text-primary">{{ $log->user->name ?? 'Unknown User' }}</span><br>

            <small class="text-muted">{{ $log->created_at->format('d M Y, h:i A') }}</small>

            @if($log->description)
                <p class="mb-1">{{ $log->description }}</p>
            @endif

            @if(!empty($log->changes_array))
                <div class="ps-3">
                    <ul class="list-unstyled">
                        @foreach($log->changes_array as $field => $change)
                            @php
                                $old = is_array($change) && isset($change['old']) ? $change['old'] : '-';
                                $new = is_array($change) && isset($change['new']) ? $change['new'] : $change;
                            @endphp
                            <li>
                                <strong class="text-capitalize">{{ str_replace('_', ' ', $field) }}:</strong>
                                <span class="text-danger text-decoration-line-through">{{ $old }}</span> 
                                <span class="text-success">→ {{ $new }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endforeach
@endif
