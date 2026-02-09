<form action="{{ route('settings.payment.update') }}" method="POST">
    @csrf
    @method('PUT')

    {{-- PAY AT FITTING --}}
    <h5 class="mb-3">Pay At Fitting Centre</h5>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label><strong>Label</strong></label>
            <input type="text" name="paymentmethod_payatfitting_label" class="form-control"
                   value="{{ get_option('paymentmethod_payatfitting_label') ?? 'Pay At Center' }}">
        </div>
        <div class="col-md-3">
            <label>Active</label>
            <select name="paymentmethod_payatfitting_active" class="form-control">
                <option value="1">Enable</option>
                <option value="0">Disable</option>
            </select>
        </div>        
    </div>
    <hr>

    <h5 class="mb-3">GlobalPay Payment Gateway</h5>
    <div class="row">
        {{-- Label --}}
        <div class="col-md-3 mb-3">
            <label><strong>Label</strong></label>
            <input type="text" name="paymentmethod_globalpay_label" class="form-control"
                   value="{{ get_option('paymentmethod_globalpay_label') ?? 'Globalpay' }}">
        </div>

        {{-- Merchant ID --}}
        <div class="col-md-3 mb-3">
            <label><strong>Merchant ID</strong></label>
            <input type="text" name="paymentmethod_globalpay_globalpay_merchant_id"
                   class="form-control"
                   value="{{ get_option('paymentmethod_globalpay_globalpay_merchant_id') ?? 'Gectech' }}">
        </div>

        {{-- Secret Key --}}
        <div class="col-md-3 mb-3">
            <label><strong>Secret Key</strong></label>
            <input type="text" name="paymentmethod_globalpay_globalpay_secrete_key"
                   class="form-control"
                   value="{{ get_option('paymentmethod_globalpay_globalpay_secrete_key') ?? 'En52thIc33' }}">
        </div>

        {{-- Active --}}
        <div class="col-md-3 mb-3">
            <label><strong>Active</strong></label>
            <select name="paymentmethod_globalpay_active" class="form-control">
                <option value="1" {{ get_option('paymentmethod_globalpay_active') == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ get_option('paymentmethod_globalpay_active') == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        {{-- Currencies --}}
        <div class="col-md-3 mb-3">
            <label><strong>Currencies</strong></label>
            <input type="text" name="paymentmethod_globalpay_currencies" 
                   class="form-control"
                   value="{{ get_option('paymentmethod_globalpay_currencies') ?? 'GBP' }}">
        </div>

        {{-- Currency Code --}}
        <div class="col-md-3 mb-3">
            <label><strong>Currency Code</strong></label>
            <input type="text" name="paymentmethod_globalpay_currencies_code" 
                   class="form-control"
                   value="{{ get_option('paymentmethod_globalpay_currencies_code') ?? '826' }}">
        </div>

        {{-- Test Mode --}}
        <div class="col-md-3 mb-3">
            <label><strong>Test Mode</strong></label>
            <select name="paymentmethod_globalpay_globalpay_test" class="form-control">
                <option value="1" {{ get_option('paymentmethod_globalpay_globalpay_test') == 1 ? 'selected' : '' }}>Enable</option>
                <option value="0" {{ get_option('paymentmethod_globalpay_globalpay_test') == 0 ? 'selected' : '' }}>Disable</option>
            </select>
        </div>

        {{-- Display on Website --}}
        <div class="col-md-3 mb-3">
            <label><strong>Display on Website</strong></label>
            <select name="paymentmethod_globalpay_display_on_website" class="form-control">
                <option value="1" {{ get_option('paymentmethod_globalpay_display_on_website') == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ get_option('paymentmethod_globalpay_display_on_website') == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        {{-- Default Selected --}}
        <div class="col-md-3 mb-3">
            <label><strong>Default Selected</strong></label>
            <select name="paymentmethod_globalpay_default_selected" class="form-control">
                <option value="1" {{ get_option('paymentmethod_globalpay_default_selected') == 1 ? 'selected' : '' }}>Enable</option>
                <option value="0" {{ get_option('paymentmethod_globalpay_default_selected') == 0 ? 'selected' : '' }}>Disable</option>
            </select>
        </div>

        {{-- Initialized --}}
        <div class="col-md-3 mb-3">
            <label><strong>Initialized</strong></label>
            <select name="paymentmethod_globalpay_initialized" class="form-control">
                <option value="1" {{ get_option('paymentmethod_globalpay_initialized') == 1 ? 'selected' : '' }}>Enable</option>
                <option value="0" {{ get_option('paymentmethod_globalpay_initialized') == 0 ? 'selected' : '' }}>Disable</option>
            </select>
        </div>

    </div>

    <hr>

    <h5 class="mb-3">Dojo Payment Gateway</h5>
    <div class="row">
        {{-- Label --}}
        <div class="col-md-3 mb-3">
            <label><strong>Label</strong></label>
            <input type="text" name="paymentmethod_dojo_label" class="form-control"
                   value="{{ get_option('paymentmethod_dojo_label') ?? 'Dojo' }}">
        </div>

        {{-- Test API Key --}}
        <div class="col-md-6 mb-3">
            <label><strong>Test API Key</strong></label>
            <input type="text" name="paymentmethod_dojo_test_api_key" class="form-control"
                   value="{{ get_option('paymentmethod_dojo_test_api_key') }}">
        </div>

        {{-- Test Mode --}}
        <div class="col-md-3 mb-3">
            <label><strong>Test Mode</strong></label>
            <select name="paymentmethod_dojo_test_mode_enabled" class="form-control">
                <option value="1" {{ get_option('paymentmethod_dojo_test_mode_enabled') == 1 ? 'selected' : '' }}>Enable</option>
                <option value="0" {{ get_option('paymentmethod_dojo_test_mode_enabled') == 0 ? 'selected' : '' }}>Disable</option>
            </select>
        </div>

        {{-- Live API Key --}}
        <div class="col-md-6 mb-3">
            <label><strong>Live API Key</strong></label>
            <input type="text" name="paymentmethod_dojo_live_api_key" class="form-control"
                   value="{{ get_option('paymentmethod_dojo_live_api_key') }}">
        </div>

        {{-- Active --}}
        <div class="col-md-3 mb-3">
            <label><strong>Active</strong></label>
            <select name="paymentmethod_dojo_active" class="form-control">
                <option value="1" {{ get_option('paymentmethod_dojo_active') == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ get_option('paymentmethod_dojo_active') == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        {{-- Display on Website --}}
        <div class="col-md-3 mb-3">
            <label><strong>Display on Website</strong></label>
            <select name="paymentmethod_dojo_display_on_website" class="form-control">
                <option value="1" {{ get_option('paymentmethod_dojo_display_on_website') == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ get_option('paymentmethod_dojo_display_on_website') == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>        

        {{-- Currencies --}}
        <div class="col-md-3 mb-3">
            <label><strong>Currencies</strong></label>
            <input type="text" name="paymentmethod_dojo_currencies" class="form-control"
                   value="{{ get_option('paymentmethod_dojo_currencies') ?? 'GBP' }}">
        </div>

        {{-- Default Selected --}}
        <div class="col-md-3 mb-3">
            <label><strong>Default Selected</strong></label>
            <select name="paymentmethod_dojo_default_selected" class="form-control">
                <option value="1" {{ get_option('paymentmethod_dojo_default_selected') == 1 ? 'selected' : '' }}>Enable</option>
                <option value="0" {{ get_option('paymentmethod_dojo_default_selected') == 0 ? 'selected' : '' }}>Disable</option>
            </select>
        </div>

        {{-- Initialized --}}
        <div class="col-md-3 mb-3">
            <label><strong>Initialized</strong></label>
            <select name="paymentmethod_dojo_initialized" class="form-control">
                <option value="1" {{ get_option('paymentmethod_dojo_initialized') == 1 ? 'selected' : '' }}>Enable</option>
                <option value="0" {{ get_option('paymentmethod_dojo_initialized') == 0 ? 'selected' : '' }}>Disable</option>
            </select>
        </div>

    </div>
    <hr>

    <h5 class="mb-3">Clover Payment Gateway</h5>
    <div class="row">
        {{-- Label --}}
        <div class="col-md-3 mb-3">
            <label><strong>Label</strong></label>
            <input type="text" name="paymentmethod_clover_label" class="form-control"
                   value="{{ get_option('paymentmethod_clover_label') ?? 'Clover' }}">
        </div>

        {{-- Store ID --}}
        <div class="col-md-3 mb-3">
            <label><strong>Store ID</strong></label>
            <input type="text" name="paymentmethod_clover_store_id" class="form-control"
                   value="{{ get_option('paymentmethod_clover_store_id') }}">
        </div>

        {{-- Shared Secret --}}
        <div class="col-md-3 mb-3">
            <label><strong>Shared Secret</strong></label>
            <input type="text" name="paymentmethod_clover_shared_secret" class="form-control"
                   value="{{ get_option('paymentmethod_clover_shared_secret') }}">
        </div>

        {{-- Active --}}
        <div class="col-md-3 mb-3">
            <label><strong>Active</strong></label>
            <select name="paymentmethod_clover_active" class="form-control">
                <option value="1" {{ get_option('paymentmethod_clover_active') == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ get_option('paymentmethod_clover_active') == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        {{-- Test Mode --}}
        <div class="col-md-3 mb-3">
            <label><strong>Test Mode</strong></label>
            <select name="paymentmethod_clover_test" class="form-control">
                <option value="1" {{ get_option('paymentmethod_clover_test') == 1 ? 'selected' : '' }}>Enable</option>
                <option value="0" {{ get_option('paymentmethod_clover_test') == 0 ? 'selected' : '' }}>Disable</option>
            </select>
        </div>

        {{-- Currencies --}}
        <div class="col-md-3 mb-3">
            <label><strong>Currencies</strong></label>
            <input type="text" name="paymentmethod_clover_currencies" class="form-control"
                   value="{{ get_option('paymentmethod_clover_currencies') ?? '826' }}">
        </div>

    </div>
    <hr>

    <h5 class="mb-3">PaymentAssist Payment Gateway</h5>

    <div class="row">
        {{-- Label --}}
        <div class="col-md-3 mb-3">
            <label><strong>Label</strong></label>
            <input type="text" name="paymentmethod_paymentassist_label" class="form-control"
                   value="{{ get_option('paymentmethod_paymentassist_label') ?? 'Payment Assist' }}">
        </div>

        {{-- API Key --}}
        <div class="col-md-3 mb-3">
            <label><strong>API Key</strong></label>
            <input type="text" name="paymentmethod_paymentassist_api_key" class="form-control"
                   value="{{ get_option('paymentmethod_paymentassist_api_key') }}">
        </div>

        {{-- Secret Key --}}
        <div class="col-md-3 mb-3">
            <label><strong>Secret Key</strong></label>
            <input type="text" name="paymentmethod_paymentassist_Secret_key" class="form-control"
                   value="{{ get_option('paymentmethod_paymentassist_Secret_key') }}">
        </div>

        {{-- Active --}}
        <div class="col-md-3 mb-3">
            <label><strong>Active</strong></label>
            <select name="paymentmethod_paymentassist_active" class="form-control">
                <option value="1" {{ get_option('paymentmethod_paymentassist_active') == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ get_option('paymentmethod_paymentassist_active') == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        {{-- Test Mode --}}
        <div class="col-md-3 mb-3">
            <label><strong>Test Mode</strong></label>
            <select name="paymentmethod_paymentassist_test_mode_enabled" class="form-control">
                <option value="1" {{ get_option('paymentmethod_paymentassist_test_mode_enabled') == 1 ? 'selected' : '' }}>Enable</option>
                <option value="0" {{ get_option('paymentmethod_paymentassist_test_mode_enabled') == 0 ? 'selected' : '' }}>Disable</option>
            </select>
        </div>

        {{-- Display on Website --}}
        <div class="col-md-3 mb-3">
            <label><strong>Display on Website</strong></label>
            <select name="paymentmethod_paymentassist_display_on_website" class="form-control">
                <option value="1" {{ get_option('paymentmethod_paymentassist_display_on_website') == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ get_option('paymentmethod_paymentassist_display_on_website') == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        {{-- Currencies --}}
        <div class="col-md-3 mb-3">
            <label><strong>Currencies</strong></label>
            <input type="text" name="paymentmethod_paymentassist_currencies" class="form-control"
                   value="{{ get_option('paymentmethod_paymentassist_currencies') ?? 'GBP' }}">
        </div>

        {{-- Default Selected --}}
        <div class="col-md-3 mb-3">
            <label><strong>Default Selected</strong></label>
            <select name="paymentmethod_paymentassist_default_selected" class="form-control">
                <option value="1" {{ get_option('paymentmethod_paymentassist_default_selected') == 1 ? 'selected' : '' }}>Enable</option>
                <option value="0" {{ get_option('paymentmethod_paymentassist_default_selected') == 0 ? 'selected' : '' }}>Disable</option>
            </select>
        </div>

        {{-- Initialized --}}
        <div class="col-md-3 mb-3">
            <label><strong>Initialized</strong></label>
            <select name="paymentmethod_paymentassist_initialized" class="form-control">
                <option value="1" {{ get_option('paymentmethod_paymentassist_initialized') == 1 ? 'selected' : '' }}>Enable</option>
                <option value="0" {{ get_option('paymentmethod_paymentassist_initialized') == 0 ? 'selected' : '' }}>Disable</option>
            </select>
        </div>
    </div>
    <hr>
    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
