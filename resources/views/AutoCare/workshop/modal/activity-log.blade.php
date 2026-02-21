@if($logs->isEmpty())
    <div class="alert alert-info mb-0">
        <i class="fa fa-info-circle me-2"></i>No activity logs found for this workshop.
    </div>
@else
@if(!$logs->isEmpty())
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
                <input type="text" id="activity-search" class="form-control" placeholder="Search activities...">
            </div>
        </div>
        <div class="col-md-6 text-end">
            <small class="text-muted">Total: {{ $logs->count() }} activities</small>
        </div>
    </div>

    <script>
        document.getElementById('activity-search').addEventListener('keyup', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
@endif
    <div class="table-responsive">
        <table id="" class="table table-hover table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th width="15%">Date & Time</th>
                    <th width="20%">Action</th>
                    <th width="15%">User</th>
                    <th width="30%">Description</th>
                    <th width="20%">Changes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>
                            <small>
                                <i class="fa fa-clock-o me-1"></i>
                                {{ $log->created_at->format('d M Y') }}<br>
                                <span class="fw-bold">{{ $log->created_at->format('h:i A') }}</span>
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $log->action }}</span>
                        </td>
                        <td>
                            <span class="text-primary">
                                <i class="fa fa-user me-1"></i>
                                {{ $log->user->name ?? 'System/User' }}
                            </span>
                        </td>
                        <td>
                            @if($log->description)
                                <div>
                                    {{ $log->description }}
                                </div>
                            @else
                                <span class="text-muted">No description</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($log->changes_array) && is_array($log->changes_array))
                                <button class="btn btn-sm btn-outline-secondary" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#changes-{{ $log->id }}">
                                    <i class="fa fa-eye me-1"></i>View Details
                                </button>
                                
                                <div class="collapse mt-2" id="changes-{{ $log->id }}">
                                    <div class="card card-body bg-light p-2">
                                        <ul class="list-unstyled mb-0">
                                            @foreach($log->changes_array as $field => $change)
                                                <li class="mb-1">
                                                    <small>
                                                        <strong class="text-capitalize">
                                                            {{ str_replace('_', ' ', $field) }}:
                                                        </strong>
                                                        @if(is_array($change))
                                                            @if(isset($change['old']) || isset($change['new']))
                                                                <span class="text-danger text-decoration-line-through">
                                                                    {{ $change['old'] ?? '-' }}
                                                                </span>
                                                                <i class="fa fa-arrow-right mx-1 text-muted"></i>
                                                                <span class="text-success">
                                                                    {{ $change['new'] ?? '-' }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">{{ json_encode($change) }}</span>
                                                            @endif
                                                        @else
                                                            <span class="text-info">{{ $change }}</span>
                                                        @endif
                                                    </small>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @elseif($log->changes)
                                <small class="text-muted">{{ $log->changes }}</small>
                            @else
                                <span class="text-muted">No changes</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif