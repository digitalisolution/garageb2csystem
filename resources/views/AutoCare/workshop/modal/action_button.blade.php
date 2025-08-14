 @php
        $role_id = Auth::user()->role_id;
    @endphp
<div class="btn-group" role="group">
    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
        Actions
    </button>
    <ul class="dropdown-menu btngroup-dropdown"
                                                    aria-labelledby="btnGroupDrop{{ $row->id }}">
                                                    @if ($row->is_converted_to_invoice == 1)
                                                        <li>
                                                            <a href="{{ url('/') }}/AutoCare/workshop/addinvoice/{{ $row->id }}"
                                                                class="dropdown-item btn btn-warning btn-sm">
                                                                <i class="fa fa-pencil" aria-hidden="true"></i> Update Invoice
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a target="_blank"
                                                                href="{{ url('/') }}/AutoCare/workshop/invoice/{{ $row->id }}"
                                                                class="dropdown-item btn btn-primary btn-sm">
                                                                <i class="fa fa-eye"></i> View Invoice
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item btn btn-info btn-sm"
                                                                data-toggle="modal" data-target="#emailModal{{ $row->id }}">
                                                                <i class="fa fa-envelope"></i> Email Invoice
                                                            </button>
                                                        </li>
                                                        @if ($role_id == 1)
                                                            <li>
                                                                <a href="{{ route('invoice.preview', $row->id) }}" target="_blank"
                                                                    class="dropdown-item btn btn-info btn-sm">
                                                                    <i class="fa fa-eye"></i> Preview PDF
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if($row->is_void === 0)
                                                            <li>
                                                                <form action="{{ url('/AutoCare/workshop/void/' . $row->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Are you sure you want to void this workshop?');">
                                                                    @csrf
                                                                    @method('POST')
                                                                    <button type="submit" class="dropdown-item btn text-danger btn-sm">
                                                                        <i class="fa fa-remove"></i> Void Invoice
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    @else
                                                        <li>
                                                            <a href="{{ url('/') }}/AutoCare/workshop/addinvoice/{{ $row->id }}"
                                                                class="dropdown-item btn btn-primary btn-sm">
                                                                <i class="fa fa-upload"></i> Convert to Invoice
                                                            </a>
                                                        </li>
                                                    @endif

                                                    <li>
                                                        <a data-toggle="modal" id="{{ $row->id }}"
                                                            data-target="#workshopDiscount"
                                                            data-balance-total="{{ $row->balance_price }}"
                                                            class="dropdown-item btn btn-success openDiscountModelForWorkshop btn-sm">
                                                            <i class="fa fa-money" aria-hidden="true"></i> Discount
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a data-toggle="modal" id="{{ $row->id }}"
                                                            data-target="#workshopPayment"
                                                            class="dropdown-item btn btn-success openPayentModelForWorkshop btn-sm"
                                                            data-grand-total="{{ $row->balance_price }}">
                                                            <i class="fa fa-money" aria-hidden="true"></i> Receive Payment
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a target="_blank"
                                                            href="{{ url('/') }}/AutoCare/workshop/view/{{ $row->id }}"
                                                            class="dropdown-item btn btn-primary btn-sm">
                                                            <i class="fa fa-eye"></i> Job View
                                                        </a>
                                                    </li>

                                                    @if ($row->is_workshop == 1)
                                                        <li>
                                                            <a target="_blank"
                                                                href="{{ url('/') }}/AutoCare/workshop/payment_history/{{ $row->id }}"
                                                                class="dropdown-item btn btn-danger btn-sm" title="Payment History">
                                                                <i class="fa fa-eye"></i> Payment History
                                                            </a>
                                                        </li>
                                                        @if($row->is_void === 0)
                                                            <li>
                                                                <a href="{{ url('/') }}/AutoCare/workshop/add/{{ $row->id }}"
                                                                    class="dropdown-item btn btn-success btn-sm">
                                                                    <i class="fa fa-edit"></i> Edit
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endif
                                                    <li>
                                                        <a href="#"
                                                            class="dropdown-item btn btn-success btn-sm open-activity-log-modal"
                                                            data-id="{{ $row->id }}">
                                                            <i class="fa fa-eye" aria-hidden="true"></i> Activity Log
                                                        </a>
                                                    </li>

                                                    @if ($role_id == 1)
                                                        <li>
                                                            <form action="{{ url('/AutoCare/workshop/trash/' . $row->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Are you sure you want to delete this workshop?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item btn text-danger btn-sm">
                                                                    <i class="fa fa-remove"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
</div>
