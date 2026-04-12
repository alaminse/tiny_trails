<div class="mb-2">
    <label class="form-label small fw-semibold">Label</label>
    <input type="text" name="label" class="form-control form-control-sm"
           value="{{ old('label', $c?->label) }}"
           placeholder="e.g. Demo Account" required>
</div>

<div class="mb-2">
    <label class="form-label small fw-semibold">Mode</label>
    <select name="mode" class="form-select form-select-sm">
        <option value="demo"       {{ old('mode', $c?->mode) === 'demo'       ? 'selected' : '' }}>🧪 Demo</option>
        <option value="production" {{ old('mode', $c?->mode) === 'production' ? 'selected' : '' }}>🚀 Production</option>
    </select>
</div>

<div class="mb-2">
    <label class="form-label small fw-semibold">Account SID</label>
    <input type="text" name="account_sid" class="form-control form-control-sm"
           value="{{ old('account_sid', $c?->account_sid) }}"
           placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>
    <div class="form-text">Starts with AC — found in Twilio Console</div>
</div>

<div class="mb-2">
    <label class="form-label small fw-semibold">Auth Token</label>
    <div class="input-group input-group-sm">
        <input type="password" name="auth_token" id="authToken"
               class="form-control form-control-sm"
               value="{{ old('auth_token', $c?->auth_token) }}"
               placeholder="32-character token" required>
        <button class="btn btn-outline-secondary" type="button"
                onclick="toggleToken()">👁</button>
    </div>
</div>

<div class="mb-2">
    <label class="form-label small fw-semibold">From Number</label>
    <input type="text" name="from_number" class="form-control form-control-sm"
           value="{{ old('from_number', $c?->from_number) }}"
           placeholder="+1234567890" required>
</div>

<div class="mb-2">
    <label class="form-label small fw-semibold">
        Messaging Service SID
        <span class="text-muted fw-normal">(optional)</span>
    </label>
    <input type="text" name="messaging_service_sid" class="form-control form-control-sm"
           value="{{ old('messaging_service_sid', $c?->messaging_service_sid) }}"
           placeholder="MGxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
    <div class="form-text">If set, this overrides the From number</div>
</div>

<script>
function toggleToken() {
    const input = document.getElementById('authToken');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
