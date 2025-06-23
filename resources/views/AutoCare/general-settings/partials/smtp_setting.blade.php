<div>
    <h4>SMTP Settings</h4>
    <form action="{{ route('settings.smtp.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="smtp_host" class="form-label">SMTP Host</label>
            <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="{{ get_option('smtp_host', '') }}">
        </div>

        <div class="mb-3">
            <label for="smtp_port" class="form-label">SMTP Port</label>
            <input type="text" class="form-control" id="smtp_port" name="smtp_port" value="{{ get_option('smtp_port', '') }}">
        </div>
         <div class="mb-3">
            <label for="smtp_encrpt" class="form-label">SMTP Encrpt</label>
            <input type="text" class="form-control" id="smtp_encrpt" name="smtp_encrpt" value="{{ get_option('smtp_encrpt', '') }}">
        </div>

        <div class="mb-3">
            <label for="smtp_username" class="form-label">SMTP Username</label>
            <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="{{ get_option('smtp_username', '') }}">
        </div>

        <div class="mb-3">
            <label for="smtp_from_name" class="form-label">SMTP From Name</label>
            <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name" value="{{ get_option('smtp_from_name', $garage->garage_name) }}">
        </div>

         <div class="mb-3">
            <label for="smtp_email" class="form-label">SMTP Email</label>
            <input type="text" class="form-control" id="smtp_email" name="smtp_email" value="{{ get_option('smtp_email', '') }}">
        </div>

        <div class="mb-3">
            <label for="smtp_password" class="form-label">SMTP Password</label>
            <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="{{ get_option('smtp_password', '') }}">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>