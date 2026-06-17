@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center mt-2">
        @foreach ($banners as $banner)
            <div class="col-md-4">
                <div class="card custom--card p-3">
                    <div class="h-350">

                            <img src="{{ getImage(fileManager()->promotions()->path . '/' . @$banner->banner) }}"
                                class="h-100 w-100">

                    </div>
                    <div class="card-body">

                        @php
                            $string = '<a href="' . route('home') . '?reference=' . auth()->user()->username . '" target="_blank"> <img src="' . getImage(fileManager()->promotions()->path . '/' . @$banner->banner) . '" alt="image"/></a>';
                        @endphp

                        <textarea type="url" id="reflink{{ $banner->id }}" class="form--control form-control from-control-lg" rows="5"
                            readonly>@php echo  $string @endphp</textarea>
                        <button type="button" data-copytarget="#reflink{{ $banner->id }}"
                            class="btn btn--base w-100 mt-3 copybtn btn-block"><i class="fa fa-copy"></i> &nbsp;
                            @lang('Copy')</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('style')
    <style>
        textarea.form--control {
            min-height: auto !important;
        }
        .custom--card {
            background-color: hsl(var(--white)/.1);
        }
        .custom--card .card-body {
            border-top: 15px solid hsl(var(--white)/.05);
            border-radius: 0;
        }
    </style>
@endpush

@push('script')
    <script>
        document.querySelectorAll('.copybtn').forEach((element) => {
            element.addEventListener('click', copy, true);
        })

        function copy(e) {
            var
                t = e.target,
                c = t.dataset.copytarget,
                inp = (c ? document.querySelector(c) : null);
            if (inp && inp.select) {
                inp.select();
                try {
                    document.execCommand('copy');
                } catch (err) {
                    alert(`@lang('Please press Ctrl/Cmd+C to copy')`);
                }
            }
        }
    </script>
@endpush
