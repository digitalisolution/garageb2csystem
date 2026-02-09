@extends('layouts.app')

@section('content')
    <div class="pt-60 pb-60">
        <div class="container">
            @include('garage.menu')
            <div class="short__item mb-4">
                <div class="bg-gray p-3 text-center rounded mb-4">
                    <h2 class="m-0"><strong>Orders</strong></h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped border text-center">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="no-wrap">Job Number</th>
                                <th>VRM</th>
                                <th class="no-wrap">Booking Date</th>
                                <th class="no-wrap">Booking Time</th>
                                <th class="no-wrap">Order Type</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($workshops as $order)
                                                <tr>
                                                    <td class="no-wrap">JOB-{{ $order->id }}</td>
                                                    <td class="text-uppercase">{{ $order->vehicle_reg_number }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($order->due_in)->format('d-m-Y') }}</td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($order->due_in)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($order->due_out)->format('H:i') }}
                                                    </td>
                                                    <td class="no-wrap">
                                                        {{ optional($order->items->first())->fitting_type
                                ? ucwords(str_replace('_', ' ', $order->items->first()->fitting_type))
                                : 'N/A' }}
                                                    </td>
                                                    <td>
										@if ($order->status === 'completed')
											<badge class="bg-success text-white rounded px-2 no-wrap">{{ strtoupper($order->status) }}</badge>
										@elseif ($order->status === 'cancelled')
											<badge class="bg-danger text-white rounded px-2 no-wrap">{{ strtoupper($order->status) }}</badge>
										@elseif ($order->status === 'processing')
											<badge class="bg-warning text-white rounded px-2 no-wrap">{{ strtoupper($order->status)}}</badge>
										@else
											<badge class="bg-primary text-white rounded px-2 no-wrap">{{strtoupper($order->status) }}</badge>
										@endif
									</td>
                                                    <td>£{{ number_format($order->grandTotal, 2) }}</td>
                                                    <td class="no-wrap">
                                                        <a href="{{ route('garage.orders.view', $order->id) }}"
                                                            class="btn btn-info btn-sm text-white" target="_blank">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <!-- <button type="submit" class="btn btn-danger btn-sm">Cancel</button> -->
                                                        @if ($order->status === 'booked' || $order->status === 'processing')
                                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                                data-bs-target="#verifyOrderModal" data-order-id="{{ $order->id }}">
                                                                Verify
                                                            </button>
                                                        @endif

                                                        <!-- Modal -->
                                                        <div class="modal fade" id="verifyOrderModal" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <form id="verifyOrderForm" method="POST">
                                                                    @csrf
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Verify Job</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <label for="code" class="form-label">Enter Verification Code</label>
                                                                            <input type="text" name="code" id="verifyCodeInput"
                                                                                class="form-control" required>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-success">Verify</button>
                                                                            <button type="button" id="resendCodeBtn"
                                                                                class="btn btn-warning">Resend Code</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        No Order Details Found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    {{ $workshops->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const verifyModal = document.getElementById('verifyOrderModal');
        const verifyForm = document.getElementById('verifyOrderForm');
        const resendBtn = document.getElementById('resendCodeBtn');
        let currentOrderId = null;

        verifyModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            currentOrderId = button.getAttribute('data-order-id');
            verifyForm.action = "/garage/auth/orders/" + currentOrderId + "/verify";
        });

        verifyForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(verifyForm);

            fetch(verifyForm.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Verified!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: true
                        });

                        // Close modal
                        const modal = bootstrap.Modal.getInstance(verifyModal);
                        modal.hide();

                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || "Invalid verification code."
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Something went wrong. Try again."
                    });
                });
        });

        resendBtn.addEventListener('click', function () {
            if (!currentOrderId) return;

            fetch("/garage/auth/orders/" + currentOrderId + "/resend-code", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Code Resent!',
                            text: data.message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Something went wrong. Try again."
                    });
                });
        });
    });
</script>