@extends($activeTemplate . 'layouts.' . $layout)

@section('content')
    <section>
        <div class="{{ auth()->check() ? '' : 'container mt-5' }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="card custom--card">
                        <div class="card-header flex-wrap d-flex justify-content-between align-items-center">
                            <h5>
                                @php echo $myTicket->statusBadge; @endphp
                                [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
                            </h5>
                            @if ($myTicket->status != 3 && $myTicket->user)
                                <button class="btn btn-danger btn--sm confirmationBtn" type="button"
                                    data-question="@lang('Are you sure to close this ticket?')"
                                    data-action="{{ route('ticket.close', $myTicket->id) }}"><i
                                        class="fa fa-times-circle"></i>
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            @if ($myTicket->status != 4)
                                <form method="post" action="{{ route('ticket.reply', $myTicket->id) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row justify-content-between">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <textarea name="message" class="form--control" rows="4">{{ old('message') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <a href="javascript:void(0)" class="btn btn--base btn--sm addFile"><i
                                                class="las la-plus"></i> @lang('Add New')</a>
                                    </div>
                                    <div class="form-group">
                                        <label class="form--label">@lang('Attachments')</label> <small
                                            class="text--danger">@lang('Max 5 files can be uploaded'). @lang('Maximum upload size is')
                                            {{ ini_get('upload_max_filesize') }}</small>
                                        <input type="file" name="attachments[]" class="form--control" />
                                        <div id="fileUploadsContainer"></div>
                                        <p class="my-2 ticket-attachments-message text-muted">
                                            @lang('Allowed File Extensions'): .@lang('jpg'), .@lang('jpeg'), .@lang('png'),
                                            .@lang('pdf'), .@lang('doc'), .@lang('docx')
                                        </p>
                                    </div>
                                    <button type="submit" class="btn btn--base w-100"> <i class="fa fa-reply"></i>
                                        @lang('Reply')</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="card custom--card mt-4">
                        <div class="card-body">
                            @foreach ($messages as $message)
                                @if ($message->admin_id == 0)
                                    <div class="row border border-primary border-radius-3 my-3 py-3 mx-2">
                                        <div class="col-md-3 border-end text-end">
                                            <h5 class="my-3">{{ $message->ticket->name }}</h5>
                                        </div>
                                        <div class="col-md-9">
                                            <small class="text-muted fw-bold my-3">
                                                @lang('Posted on')
                                                {{ $message->created_at->format('l, dS F Y @ H:i') }}</small>
                                            <p>{{ $message->message }}</p>
                                            @if ($message->attachments->count() > 0)
                                                <div class="mt-2">
                                                    @foreach ($message->attachments as $k => $image)
                                                        <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                            class="me-3"><i class="fa fa-file"></i> @lang('Attachment')
                                                            {{ ++$k }} </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="row admin-answer my-3 py-3 mx-2">
                                        <div class="col-md-3 border-end text-end">
                                            <h5 class="my-3">{{ $message->admin->name }}</h5>
                                            <p class="lead text-muted">@lang('Staff')</p>
                                        </div>
                                        <div class="col-md-9">
                                            <small class="text-muted fw-bold my-3">
                                                @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}
                                            </small>
                                            <p>{{ $message->message }}</p>
                                            @if ($message->attachments->count() > 0)
                                                <div class="mt-2">
                                                    @foreach ($message->attachments as $k => $image)
                                                        <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                            class="me-3"><i class="fa fa-file"></i> @lang('Attachment')
                                                            {{ ++$k }} </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-confirmation-modal closeBtn="btn btn--danger btn--sm pill" submitBtn="btn btn--base pill btn--sm">
    </x-confirmation-modal>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addFile').on('click', function() {
                if (fileAdded >= 4) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadsContainer").append(`
                    <div class="input-group my-3">
                        <input type="file" name="attachments[]" class="form-control form--control" required />
                        <button type="button" class="input-group-text btn--danger remove-btn"><i class="las la-times"></i></button>
                    </div>
                `)
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.input-group').remove();
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .input-group-text {
            border: unset !important
        }

        .admin-answer {
            border-radius: 3px;
            background-color: #0d0b32;
            border: 1px solid #7d5203
        }

        style="background-color: #0d0b32"
    </style>
@endpush
