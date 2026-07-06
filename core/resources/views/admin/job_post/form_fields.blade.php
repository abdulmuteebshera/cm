<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>@lang('Job Title')</label>
            <input type="text" class="form-control" name="title" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Department')</label>
            <input type="text" class="form-control" name="department" placeholder="@lang('e.g. Quantitative Research')">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Location')</label>
            <input type="text" class="form-control" name="location" placeholder="@lang('e.g. New York / Remote')">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Employment Type')</label>
            <input type="text" class="form-control" name="employment_type" placeholder="@lang('e.g. Full-time')">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label>@lang('Summary')</label>
            <textarea class="form-control" name="summary" rows="2" placeholder="@lang('Short overview shown on the careers page listing')"></textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label>@lang('Job Description')</label>
            <textarea class="form-control" name="description" rows="6" required></textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label>@lang('Requirements')</label>
            <textarea class="form-control" name="requirements" rows="4" placeholder="@lang('Qualifications, experience, and skills')"></textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label>@lang('Status')</label>
            <select name="status" class="form-control">
                <option value="1">@lang('Active')</option>
                <option value="0">@lang('Inactive')</option>
            </select>
        </div>
    </div>
</div>
