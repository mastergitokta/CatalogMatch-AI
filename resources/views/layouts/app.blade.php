<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CatalogMatch AI')</title>
    <style>
        :root {
            --bg: #f4efe6;
            --panel: #fffdf9;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #e5dccf;
            --brand: #b45309;
            --brand-dark: #7c2d12;
            --danger: #b91c1c;
            --success: #166534;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            background:
                radial-gradient(circle at top left, rgba(245, 158, 11, 0.18), transparent 30%),
                linear-gradient(180deg, #f8f2e8 0%, var(--bg) 100%);
            color: var(--ink);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: min(1100px, calc(100% - 32px));
            margin: 0 auto;
            padding: 32px 0 48px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
        }

        .brand {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .nav-link,
        .button,
        button {
            border: 0;
            border-radius: 999px;
            padding: 10px 16px;
            background: var(--brand);
            color: #fff;
            cursor: pointer;
            font: inherit;
        }

        .button.secondary,
        .nav-link.secondary {
            background: #fff;
            color: var(--brand-dark);
            border: 1px solid var(--line);
        }

        .button.danger {
            background: var(--danger);
        }

        .panel {
            background: rgba(255, 253, 249, 0.88);
            border: 1px solid rgba(229, 220, 207, 0.9);
            border-radius: 24px;
            box-shadow: 0 18px 60px rgba(124, 45, 18, 0.08);
            padding: 24px;
            backdrop-filter: blur(8px);
        }

        .hero {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: center;
            margin-bottom: 24px;
        }

        .hero h1 {
            margin: 0 0 8px;
            font-size: clamp(2rem, 4vw, 3.25rem);
            line-height: 1;
        }

        .hero p,
        .meta,
        .empty-state {
            color: var(--muted);
        }

        .alert {
            margin-bottom: 16px;
            padding: 14px 18px;
            border-radius: 16px;
        }

        .alert.success {
            background: #ecfdf3;
            color: var(--success);
            border: 1px solid #bbf7d0;
        }

        .grid {
            display: grid;
            gap: 16px;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
        }

        .card-image {
            aspect-ratio: 16 / 9;
            width: 100%;
            object-fit: cover;
            background: linear-gradient(135deg, #fcd34d, #fed7aa);
        }

        .card-body {
            padding: 18px;
        }

        .badge {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.82rem;
            background: #fffbeb;
            color: var(--brand-dark);
        }

        .badge.off {
            background: #f3f4f6;
            color: #4b5563;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 16px;
        }

        .form-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .field,
        .field-full {
            display: grid;
            gap: 8px;
        }

        .field-full {
            grid-column: 1 / -1;
        }

        label {
            font-weight: 700;
        }

        .section-label {
            margin: 12px 0 4px;
            font-size: 1.05rem;
        }

        input,
        textarea,
        select {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 12px 14px;
            font: inherit;
            background: #fff;
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        input[type="file"] {
            padding: 14px;
        }

        input[type="radio"],
        input[type="checkbox"] {
            accent-color: var(--brand);
        }

        .error-list {
            margin: 0 0 16px;
            padding: 14px 18px;
            border-radius: 16px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .detail-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
            align-items: start;
        }

        .detail-stage {
            display: grid;
            gap: 20px;
        }

        .detail-card {
            padding: 20px;
        }

        .detail-card img {
            width: 100%;
            border-radius: 20px;
            object-fit: cover;
            aspect-ratio: 16 / 10;
            background: linear-gradient(135deg, #fcd34d, #fed7aa);
            display: block;
        }

        .detail-info {
            display: grid;
            gap: 18px;
        }

        .detail-info h1 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 3rem);
            line-height: 1.05;
            overflow-wrap: anywhere;
        }

        .detail-copy {
            margin: 0;
            font-size: 1.02rem;
            line-height: 1.7;
            color: #374151;
        }

        .stats-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .stat-card {
            padding: 14px 16px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fff;
        }

        .stat-card strong,
        .gallery-meta strong {
            display: block;
            margin-bottom: 4px;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
        }

        .stat-card span,
        .gallery-meta span {
            display: block;
            overflow-wrap: anywhere;
        }

        .gallery-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .gallery-card {
            display: grid;
            gap: 10px;
            padding: 12px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: #fff;
            align-content: start;
        }

        .gallery-card img,
        .gallery-preview {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 14px;
            background: linear-gradient(135deg, #fcd34d, #fed7aa);
        }

        .gallery-meta {
            display: grid;
            gap: 8px;
            color: #4b5563;
            font-size: 0.94rem;
        }

        .gallery-editor {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .gallery-editor .gallery-card {
            gap: 12px;
        }

        .gallery-controls {
            display: grid;
            gap: 10px;
            padding-top: 2px;
        }

        .control-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fffdf8;
            font-weight: 600;
        }

        .control-chip input {
            width: auto;
            margin: 0;
        }

        .file-help {
            margin: 0;
            line-height: 1.6;
        }

        .muted-panel {
            padding: 16px 18px;
            border: 1px dashed var(--line);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.55);
        }

        .inline-option {
            display: flex;
            align-items: center;
            gap: 10px;
            width: auto;
            border: 0;
            padding: 0;
        }

        .inline-option input {
            width: auto;
            margin: 0;
        }

        .stack {
            display: grid;
            gap: 10px;
        }

        .pagination {
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .topbar,
            .hero,
            .detail-grid,
            .form-grid {
                grid-template-columns: 1fr;
                display: grid;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .gallery-editor {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <a href="{{ route('products.index') }}" class="brand">CatalogMatch AI</a>
            <a href="{{ route('products.create') }}" class="nav-link">Tambah Produk</a>
        </div>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>
