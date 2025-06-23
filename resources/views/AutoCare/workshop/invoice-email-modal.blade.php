<!-- Email Modal -->
<div class="modal fade" id="emailModal{{ $value->id }}" tabindex="-1" role="dialog"
    aria-labelledby="emailModalLabel{{ $value->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel{{ $value->id }}">Send Invoice via Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('/AutoCare/workshop/send-invoice-email') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $value->id }}">
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label for="email_to">Email To:</label>
                        <input type="email" class="form-control" id="email_to" value="{{ $value->email }}"
                            name="email_to" required>
                    </div>
                    <div class="form-group">
                        <label for="email_cc">CC:</label>
                        <input type="email" class="form-control" id="email_cc" name="email_cc">
                    </div>
                    <div class="form-group">
                        <label for="attach_pdf">Attach PDF:</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" checked="checked" id="attach_pdf"
                                name="attach_pdf" value="1">
                            <label class="form-check-label p-0" for="attach_pdf">Attach Invoice PDF</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email_body">Body:</label>
                        <textarea id="email_body" name="email_body">{!! getDefaultEmailBody($value) !!}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .tox-statusbar__branding, .tox-promotion {
        display: none;
    }
</style>
