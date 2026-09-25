<label>Name<input name="name" required></label>
<label>Email<input type="email" name="email"></label>
<label>Phone<input name="phone"></label>
<label>Company<input name="company"></label>
<label>Country<input name="country"></label>
<label>City<input name="city"></label>
<label>Source<input name="source" placeholder="Referral / Event / Inbound"></label>
<label>Stage
    <select name="stage">
        @foreach(($stages ?? \App\Support\CrmOfficerProgram::leadStages()) as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
        @endforeach
    </select>
</label>
<label>Expected AUM<input type="number" step="0.01" name="expected_aum"></label>
<label>Committed AUM<input type="number" step="0.01" name="committed_aum" placeholder="Used for 2% upfront when Funded"></label>
<label>Next follow-up<input type="date" name="next_follow_up"></label>
@if(isset($agents) && (auth('crm')->user()->isSuper() || auth('crm')->user()->portal() === 'manager'))
<label>Investment officer
    <select name="owner_staff_id">
        <option value="">Assign later</option>
        @foreach($agents as $agent)
            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
        @endforeach
    </select>
</label>
@endif
<label>Notes<textarea name="notes" rows="3"></textarea></label>
<p class="crm-muted" style="grid-column:1/-1;margin:0">When stage is set to <strong>Funded / Active</strong>, a <strong>2% upfront</strong> commission is auto-created. Retention is <strong>1% yearly</strong> while capital stays invested.</p>
