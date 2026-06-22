<div class="form-group">
    <label>@lang('Asset Class')</label>
    <input type="text" class="form-control" name="name" placeholder="@lang('e.g. Forex, Indices, Commodities')" required>
</div>
<div class="row">
    <div class="col-md-6 form-group">
        <label>@lang('Allocation (%)')</label>
        <div class="input-group">
            <input type="number" step="any" min="0" max="100" class="form-control" name="percentage" required>
            <span class="input-group-text">%</span>
        </div>
    </div>
    <div class="col-md-6 form-group">
        <label>@lang('Color')</label>
        <input type="color" class="form-control form-control-color w-100" name="color" value="#1989BE">
    </div>
</div>
<div class="form-group">
    <label>@lang('Description') <small class="text-muted">(@lang('optional'))</small></label>
    <input type="text" class="form-control" name="description" placeholder="@lang('Short note shown to investors')">
</div>
<div class="row">
    <div class="col-md-6 form-group">
        <label>@lang('Display Order')</label>
        <input type="number" class="form-control" name="sort_order" value="0">
    </div>
    <div class="col-md-6 form-group">
        <label>@lang('Status')</label>
        <select name="status" class="form-control">
            <option value="1">@lang('Active')</option>
            <option value="0">@lang('Inactive')</option>
        </select>
    </div>
</div>
