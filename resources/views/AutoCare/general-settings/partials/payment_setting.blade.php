<div>
    <h4>Payment Settings</h4>
    <form action="" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="payment_gateway" class="form-label">Payment Gateway</label>
            <select class="form-select" id="payment_gateway" name="payment_gateway">
                <option value="stripe" {{ get_option('payment_gateway', 'stripe') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                <option value="paypal" {{ get_option('payment_gateway', 'stripe') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                <option value="razorpay" {{ get_option('payment_gateway', 'stripe') === 'razorpay' ? 'selected' : '' }}>Razorpay</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="payment_api_key" class="form-label">API Key</label>
            <input type="text" class="form-control" id="payment_api_key" name="payment_api_key" value="{{ get_option('payment_api_key', '') }}">
        </div>

        <div class="mb-3">
            <label for="payment_secret_key" class="form-label">Secret Key</label>
            <input type="text" class="form-control" id="payment_secret_key" name="payment_secret_key" value="{{ get_option('payment_secret_key', '') }}">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>