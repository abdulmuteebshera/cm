<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview — {{ $template->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #e8edf6;
            color: #0c1222;
        }
        .ec-prev-top {
            background: #ffffff;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            padding: 16px 24px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }
        .ec-prev-top h1 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            flex: 1;
        }
        .ec-prev-top a, .ec-prev-top button {
            font: inherit;
            font-size: 0.8125rem;
            font-weight: 600;
            padding: 10px 16px;
            border-radius: 10px;
            border: 1px solid rgba(0, 51, 173, 0.15);
            background: #fff;
            color: #0033ad;
            cursor: pointer;
            text-decoration: none;
        }
        .ec-prev-top a.primary, .ec-prev-top button.primary {
            background: #0033ad;
            color: #fff;
            border-color: #0033ad;
        }
        .ec-prev-wrap {
            max-width: 920px;
            margin: 0 auto;
            padding: 32px 20px 48px;
        }
        .ec-inbox {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.12);
            overflow: hidden;
            border: 1px solid rgba(15, 23, 42, 0.06);
        }
        .ec-inbox__meta {
            padding: 24px 28px 20px;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(180deg, #fafbfd 0%, #fff 100%);
        }
        .ec-inbox__meta dl {
            margin: 0;
            display: grid;
            grid-template-columns: 72px 1fr;
            gap: 8px 16px;
            font-size: 0.875rem;
        }
        .ec-inbox__meta dt {
            margin: 0;
            color: #7b8ba8;
            font-weight: 600;
        }
        .ec-inbox__meta dd {
            margin: 0;
            color: #0c1222;
        }
        .ec-inbox__meta .subject dd {
            font-weight: 700;
            font-size: 1.05rem;
        }
        .ec-inbox__frame {
            background: #dfe8f4;
            padding: 0;
            line-height: 0;
        }
        .ec-inbox__frame iframe {
            width: 100%;
            height: min(1200px, calc(100vh - 280px));
            min-height: 720px;
            border: 0;
            display: block;
        }
        .ec-prev-note {
            margin: 16px 0 0;
            font-size: 0.8125rem;
            color: #7b8ba8;
            text-align: center;
        }
        .ec-text-panel {
            display: none;
            padding: 28px;
            background: #fff;
            font-family: ui-monospace, 'Consolas', monospace;
            font-size: 13px;
            line-height: 1.65;
            white-space: pre-wrap;
            color: #334155;
            max-height: calc(100vh - 280px);
            overflow: auto;
        }
        body.mode-text .ec-inbox__frame { display: none; }
        body.mode-text .ec-text-panel { display: block; }
    </style>
</head>
<body>
    <header class="ec-prev-top">
        <h1>{{ $template->name }}</h1>
        <a href="{{ route('ec.admin.templates.index') }}">← Templates</a>
        <button type="button" id="btn-html" class="primary">HTML email</button>
        <button type="button" id="btn-text">Plain text</button>
    </header>

    <div class="ec-prev-wrap">
        <div class="ec-inbox">
            <div class="ec-inbox__meta">
                <dl>
                    <dt>From</dt>
                    <dd>Crownmaire Capital &lt;noreply@crownmairecapital.com&gt;</dd>
                    <dt>To</dt>
                    <dd>{{ $sample['name'] }} &lt;{{ $sample['email'] }}&gt;</dd>
                    <dt class="subject">Subject</dt>
                    <dd class="subject">{{ $renderedSubject }}</dd>
                </dl>
            </div>
            <div class="ec-inbox__frame">
                <iframe
                    title="Email HTML preview"
                    src="{{ route('ec.admin.templates.preview_frame', $template->id) }}"
                ></iframe>
            </div>
            <div class="ec-text-panel" id="text-panel">@if($renderedText){{ $renderedText }}@else No plain-text version.@endif</div>
        </div>
        <p class="ec-prev-note">Live preview with sample recipient data. Logo and assets load from crownmairecapital.com as in production sends.</p>
    </div>

    <script>
        const btnHtml = document.getElementById('btn-html');
        const btnText = document.getElementById('btn-text');
        btnHtml.addEventListener('click', () => {
            document.body.classList.remove('mode-text');
            btnHtml.classList.add('primary');
            btnText.classList.remove('primary');
        });
        btnText.addEventListener('click', () => {
            document.body.classList.add('mode-text');
            btnText.classList.add('primary');
            btnHtml.classList.remove('primary');
        });
    </script>
</body>
</html>
