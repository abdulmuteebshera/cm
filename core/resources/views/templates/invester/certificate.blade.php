<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} — {{ gs('site_name') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ getImage(getFilePath('logoIcon').'/favicon.png') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #eef1f5;
            color: #1a2733;
            min-height: 100vh;
            padding: 24px 16px 60px;
        }
        .cert-toolbar {
            max-width: 1000px;
            margin: 0 auto 22px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .tbtn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border: none;
            border-radius: 10px;
            padding: 11px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: Arial, sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.15s ease;
        }
        .tbtn:hover { opacity: 0.9; }
        .tbtn--primary { background: #1989BE; color: #fff; }
        .tbtn--gold { background: #d4a017; color: #fff; }
        .tbtn--ghost { background: #fff; color: #334155; border: 1px solid #cbd5e1; }

        .cert-stage { max-width: 1000px; margin: 0 auto; }

        .certificate {
            position: relative;
            background:
                radial-gradient(circle at 12% 12%, rgba(212,160,23,0.06), transparent 40%),
                radial-gradient(circle at 88% 88%, rgba(25,137,190,0.06), transparent 40%),
                #fffdf8;
            border: 2px solid #d4a017;
            padding: 56px 60px;
            box-shadow: 0 24px 60px rgba(15, 61, 87, 0.18);
            aspect-ratio: 1.414 / 1;
        }
        .certificate::before {
            content: "";
            position: absolute;
            inset: 12px;
            border: 1px solid rgba(212, 160, 23, 0.55);
            pointer-events: none;
        }
        .cert-corner {
            position: absolute;
            width: 42px;
            height: 42px;
            border: 3px solid #1989BE;
        }
        .cert-corner--tl { top: 20px; left: 20px; border-right: none; border-bottom: none; }
        .cert-corner--tr { top: 20px; right: 20px; border-left: none; border-bottom: none; }
        .cert-corner--bl { bottom: 20px; left: 20px; border-right: none; border-top: none; }
        .cert-corner--br { bottom: 20px; right: 20px; border-left: none; border-top: none; }

        .cert-inner {
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .cert-logo { height: 58px; width: auto; object-fit: contain; margin-bottom: 10px; }
        .cert-tagline {
            font-style: italic;
            font-size: 0.92rem;
            letter-spacing: 0.5px;
            color: #b8860b;
            margin-bottom: 22px;
        }
        .cert-heading {
            font-size: 2.1rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: #0f3d57;
            text-transform: uppercase;
        }
        .cert-rule {
            width: 90px;
            height: 3px;
            background: #d4a017;
            margin: 12px auto 22px;
        }
        .cert-presented {
            font-size: 0.95rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #64748b;
            font-family: Arial, sans-serif;
            margin-bottom: 12px;
        }
        .cert-name {
            font-size: 2.6rem;
            font-weight: 700;
            color: #1a2733;
            padding-bottom: 8px;
            border-bottom: 2px solid rgba(212, 160, 23, 0.6);
            margin-bottom: 22px;
            max-width: 80%;
        }
        .cert-body {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #334155;
            max-width: 75%;
            margin-bottom: 8px;
        }
        .cert-strategy-name {
            color: #1989BE;
            font-weight: 700;
        }
        .cert-spacer { flex: 1 1 auto; }
        .cert-footer {
            width: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
        }
        .cert-foot-block { flex: 1 1 0; }
        .cert-foot-block--center { flex: 0 0 auto; }
        .cert-foot-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1a2733;
            padding-bottom: 6px;
            border-bottom: 1px solid #94a3b8;
            font-family: Arial, sans-serif;
        }
        .cert-foot-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-top: 6px;
            font-family: Arial, sans-serif;
        }
        .cert-seal {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            background: radial-gradient(circle, #d4a017, #b8860b);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 6px 16px rgba(184, 134, 11, 0.4);
            border: 3px solid #fffdf8;
            outline: 2px solid #d4a017;
        }
        .cert-seal__top { font-size: 0.6rem; letter-spacing: 1px; font-family: Arial, sans-serif; }
        .cert-seal__mid { font-size: 1.5rem; font-weight: 700; line-height: 1; }
        .cert-seal__bot { font-size: 0.55rem; letter-spacing: 1px; font-family: Arial, sans-serif; }

        @media (max-width: 768px) {
            .certificate { padding: 32px 22px; aspect-ratio: auto; }
            .cert-heading { font-size: 1.4rem; }
            .cert-name { font-size: 1.7rem; max-width: 100%; }
            .cert-body { font-size: 0.92rem; max-width: 100%; }
            .cert-footer { flex-direction: column; align-items: center; gap: 22px; }
            .cert-foot-block { text-align: center; }
        }
        @media print {
            body { background: #fff; padding: 0; }
            .cert-toolbar { display: none !important; }
            .certificate { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="cert-toolbar" id="certToolbar">
        <button type="button" class="tbtn tbtn--primary" id="downloadBtn"><span>&#8681;</span> Download</button>
        <button type="button" class="tbtn tbtn--gold" id="shareBtn"><span>&#10150;</span> Share</button>
        <button type="button" class="tbtn tbtn--ghost" onclick="window.print()"><span>&#128424;</span> Print</button>
        <a class="tbtn tbtn--ghost" href="{{ url('/') }}"><span>&#8592;</span> Home</a>
    </div>

    <div class="cert-stage">
        <div class="certificate" id="certificate">
            <span class="cert-corner cert-corner--tl"></span>
            <span class="cert-corner cert-corner--tr"></span>
            <span class="cert-corner cert-corner--bl"></span>
            <span class="cert-corner cert-corner--br"></span>

            <div class="cert-inner">
                <img class="cert-logo" src="{{ asset(getImage(getFilePath('logoIcon').'/logo.png')) }}" alt="{{ gs('site_name') }}" crossorigin="anonymous">
                <div class="cert-tagline">The Intelligence Behind Modern Wealth</div>

                <div class="cert-heading">{{ $certificate->isWelcome() ? 'Certificate of Membership' : 'Certificate of Strategy Membership' }}</div>
                <div class="cert-rule"></div>

                <div class="cert-presented">This certificate is proudly presented to</div>
                <div class="cert-name">{{ $holderName }}</div>

                @if($certificate->isWelcome())
                    <p class="cert-body">
                        In recognition of joining <strong>{{ gs('site_name') }}</strong> as a valued member of our
                        private investor community. We welcome you to the future of intelligent, data-driven wealth.
                    </p>
                @else
                    <p class="cert-body">
                        In recognition of becoming a member of the
                        <span class="cert-strategy-name">{{ $certificate->strategy_name }}</span>
                        investment strategy at <strong>{{ gs('site_name') }}</strong>, and joining an exclusive
                        circle of forward-thinking investors.
                    </p>
                @endif

                <div class="cert-spacer"></div>

                <div class="cert-footer">
                    <div class="cert-foot-block">
                        <div class="cert-foot-value">{{ showDateTime($certificate->issued_at, 'd M Y') }}</div>
                        <div class="cert-foot-label">Date of Issue</div>
                    </div>
                    <div class="cert-foot-block cert-foot-block--center">
                        <div class="cert-seal">
                            <span class="cert-seal__top">CROWNMAIRE</span>
                            <span class="cert-seal__mid">&#9733;</span>
                            <span class="cert-seal__bot">CAPITAL</span>
                        </div>
                    </div>
                    <div class="cert-foot-block" style="text-align:right;">
                        <div class="cert-foot-value">{{ $certificate->certificate_number }}</div>
                        <div class="cert-foot-label">Certificate No.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        (function () {
            var fileName = "{{ \Illuminate\Support\Str::slug(($certificate->isWelcome() ? 'welcome' : $certificate->strategy_name).'-certificate-'.$certificate->certificate_number) }}.png";

            document.getElementById('downloadBtn').addEventListener('click', function () {
                var btn = this;
                btn.disabled = true;
                var node = document.getElementById('certificate');
                html2canvas(node, { scale: 2, backgroundColor: '#fffdf8', useCORS: true }).then(function (canvas) {
                    var link = document.createElement('a');
                    link.download = fileName;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    btn.disabled = false;
                }).catch(function () {
                    btn.disabled = false;
                    window.print();
                });
            });

            document.getElementById('shareBtn').addEventListener('click', function () {
                var shareUrl = window.location.href;
                if (navigator.share) {
                    navigator.share({ title: 'My Crownmaire Capital Certificate', url: shareUrl });
                } else {
                    navigator.clipboard.writeText(shareUrl).then(function () {
                        var btn = document.getElementById('shareBtn');
                        var original = btn.innerHTML;
                        btn.innerHTML = 'Link Copied';
                        setTimeout(function () { btn.innerHTML = original; }, 2000);
                    });
                }
            });
        })();
    </script>
</body>
</html>
