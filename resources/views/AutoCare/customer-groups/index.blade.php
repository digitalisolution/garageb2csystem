@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center gap-3">
                            <div><i class="fa fa-align-justify"></i>Customer Group Details</div>
                            <div class="ml-auto mt-1"><a class="btn btn-primary" href="{{ route('customer-groups.create') }}">Add Group</a></div>
                        </div>
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="card-body">
                        <div class="table-responsive">
                            @if($groups && $groups->isNotEmpty())
                            <table id="datable_1" class="table table-bordered dataTable no-footer"
                                aria-describedby="datable_1_info">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Discount</th>
                                        <th>Due Date Rule</th>
                                        <th>Product Types</th>
                                        <th>Customers</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($groups as $group)
                                        <tr>
                                            <td>{{ $group->name }}</td>
                                            <td>
                                                {{ ucfirst($group->discount_type) }} -
                                                {{ $group->discount_value }}{{ $group->discount_type == 'percentage' ? '%' : '£' }}
                                            </td>
                                            <td>
                                                {{ ucfirst(str_replace('_', ' ', $group->due_date_option)) }}
                                                @if ($group->due_date_option == 'manual')
                                                    ({{ $group->manual_due_date?->format('Y-m-d') }})
                                                @endif
                                            </td>
                                            <td>
                                                @if ($group->product_type)
                                                    {{ implode(', ', $group->product_type) }}
                                                @else
                                                    <em>All</em>
                                                @endif
                                            </td>
                                            <td>{{ $group->customers()->count() }}</td>
                                            <td>
                                                <a href="{{ route('customer-groups.edit', $group->id) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('customer-groups.destroy', $group->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Delete this group?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No groups found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @else
                            <div>No Group Found</div>
                            @endif
                            <div class="col-lg-12 text-center">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection