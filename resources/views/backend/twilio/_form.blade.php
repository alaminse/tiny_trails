<div class="mb-2">
    <label class="form-label small fw-semibold">Provider</label>
    <select name="provider" class="form-select form-select-sm" onchange="toggleProviderFields(this)">
        <option value="twilio"     {{ old('provider', $c?->provider) === 'twilio'     ? 'selected' : '' }}>📱 Twilio</option>
        <option value="clicksend"  {{ old('provider', $c?->provider) === 'clicksend'  ? 'selected' : '' }}>📨 ClickSend</option>
    </select>
</div>

<div class="mb-2">
    <label class="form-label small fw-semibold">Label</label>
    <input type="text" name="label" class="form-control form-control-sm"
           value="{{ old('label', $c?->label) }}"
           placeholder="e.g. Twilio Production" required>
</div>

<div class="mb-2">
    <label class="form-label small fw-semibold">Mode</label>
    <select name="mode" class="form-select form-select-sm">
        <option value="demo"       {{ old('mode', $c?->mode) === 'demo'       ? 'selected' : '' }}>🧪 Demo</option>
        <option value="production" {{ old('mode', $c?->mode) === 'production' ? 'selected' : '' }}>🚀 Production</option>
    </select>
</div>

<div class="mb-2">
    <label class="form-label small fw-semibold" id="sidLabel">
        Account SID <span class="text-muted">(Twilio) / Username (ClickSend)</span>
    </label>
    <input type="text" name="account_sid" class="form-control form-control-sm"
           value="{{ old('account_sid', $c?->account_sid) }}"
           placeholder="Account SID or Username" required>
</div>

<div class="mb-2">
    <label class="form-label small fw-semibold">
        Auth Token <span class="text-muted">(Twilio) / API Key (ClickSend)</span>
    </label>
    <div class="input-group input-group-sm">
        <input type="password" name="auth_token" id="authToken"
               class="form-control form-control-sm"
               value="{{ old('auth_token', $c?->auth_token) }}"
               placeholder="Auth Token or API Key" required>
        <button class="btn btn-outline-secondary" type="button"
                onclick="toggleToken()">👁</button>
    </div>
</div>

<div class="mb-2">
    <label class="form-label small fw-semibold">From Number / Sender ID</label>
    <input type="text" name="from_number" class="form-control form-control-sm"
           value="{{ old('from_number', $c?->from_number) }}"
           placeholder="+1234567890 or SenderName" required>
</div>

{{-- Twilio only field --}}
<div class="mb-2" id="messagingServiceField">
    <label class="form-label small fw-semibold">
        Messaging Service SID
        <span class="text-muted fw-normal">(Twilio only, optional)</span>
    </label>
    <input type="text" name="messaging_service_sid" class="form-control form-control-sm"
           value="{{ old('messaging_service_sid', $c?->messaging_service_sid) }}"
           placeholder="MGxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
</div>

<script>
function toggleToken() {
    const input = document.getElementById('authToken');
    input.type = input.type === 'password' ? 'text' : 'password';
}

function toggleProviderFields(select) {
    const isTwilio = select.value === 'twilio';
    document.getElementById('messagingServiceField').style.display = isTwilio ? 'block' : 'none';
}

// Run on page load
document.addEventListener('DOMContentLoaded', function () {
    const select = document.querySelector('select[name="provider"]');
    if (select) toggleProviderFields(select);
});
</script>
