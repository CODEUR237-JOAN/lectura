<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lectura | Modération des avis</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/lectura-logo-3d.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --bg: #05080f;
            --panel: rgba(12, 20, 38, 0.75);
            --soft: rgba(18, 28, 52, 0.6);
            --line: rgba(100, 170, 255, 0.1);
            --text: #eef3ff;
            --muted: #607a99;
            --accent: #f4a44a;
            --ok: #4dd89a;
            --danger: #ff7070;
            font-family: 'Inter', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--text);
            background:
                radial-gradient(ellipse at 10% -5%, rgba(120, 190, 255, .12), transparent 45%),
                radial-gradient(ellipse at 90% 5%, rgba(167, 139, 255, .1), transparent 40%),
                radial-gradient(ellipse at 50% 100%, rgba(244, 164, 74, .06), transparent 50%),
                linear-gradient(180deg, #030710 0%, #060d1a 60%, #040a14 100%);
            overflow-x: hidden;
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(1180px, calc(100% - 28px));
            margin: 0 auto;
            padding: 24px 0 40px;
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 24px 28px;
            border-radius: 28px;
            border: 1px solid var(--line);
            background: var(--panel);
            backdrop-filter: blur(20px);
        }

        .hero h1 {
            margin: 10px 0 8px;
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-family: 'Playfair Display', serif;
            line-height: 1.1;
        }

        .hero p {
            max-width: 700px;
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(126, 184, 245, 0.1);
            border: 1px solid rgba(126, 184, 245, 0.18);
            color: var(--blue, #7eb8f5);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.05);
            font-weight: 500;
            transition: all 0.2s;
        }
        .button:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .panel {
            padding: 24px;
            border-radius: 26px;
            border: 1px solid var(--line);
            background: var(--panel);
            backdrop-filter: blur(20px);
            box-shadow: 0 24px 72px rgba(0, 0, 0, 0.24);
            margin-top: 24px;
        }

        .list {
            display: grid;
            gap: 16px;
        }

        .item {
            padding: 20px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.02);
            transition: border-color 0.2s;
        }
        .item:hover {
            border-color: rgba(244, 164, 74, 0.2);
        }

        .item-head {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .item-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .item-subtitle {
            color: var(--muted);
            font-size: 0.85rem;
        }

        .item-text {
            color: var(--text);
            line-height: 1.6;
            margin: 12px 0;
            padding: 14px;
            background: var(--soft);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            color: var(--muted);
            font-size: 0.75rem;
            border: 1px solid transparent;
        }

        .stars {
            display: flex;
            gap: 2px;
            color: rgba(255, 255, 255, 0.2);
            font-size: 1.1rem;
        }
        .stars .filled {
            color: var(--accent);
        }

        .item-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 16px;
            border-top: 1px solid var(--line);
            padding-top: 16px;
        }

        .link-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 0 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            font-size: 0.85rem;
            transition: background 0.2s;
            cursor: pointer;
        }
        .link-button:hover { background: rgba(255, 255, 255, 0.1); }

        .danger-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 0 16px;
            border-radius: 999px;
            border: 1px solid rgba(255, 112, 112, 0.2);
            background: rgba(255, 112, 112, 0.1);
            color: var(--danger);
            font-size: 0.85rem;
            transition: all 0.2s;
            cursor: pointer;
        }
        .danger-button:hover {
            background: rgba(255, 112, 112, 0.2);
        }

        .status {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: rgba(77, 216, 154, 0.1);
            border: 1px solid rgba(77, 216, 154, 0.2);
            color: var(--ok);
            font-size: 0.9rem;
        }

        @media (max-width: 720px) {
            .shell { width: min(100% - 20px, 1180px); }
            .hero { padding: 20px; align-items: start; flex-direction: column; }
            .item-head { flex-direction: column; }
            .item-actions { flex-direction: column; }
            .item-actions form, .item-actions button, .item-actions a { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="hero">
            <div>
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px;">
                    <img src="{{ asset('images/branding/lectura-logo-3d.png') }}" alt="Lectura" style="width:50px;height:50px;border-radius:14px;object-fit:cover;border:1px solid rgba(255,255,255,.08);box-shadow:0 12px 30px rgba(0,0,0,.2);">
                    <span class="badge">Espace Admin</span>
                </div>
                <h1>Avis lecteurs</h1>
                <p>Gérez et modérez les commentaires laissés par les utilisateurs sur les livres de la plateforme.</p>
            </div>
            <div style="display:flex;gap:10px;">
                <a class="button" href="{{ route('admin.dashboard') }}">Tableau de bord</a>
                <a class="button" href="{{ route('reader.index') }}">Bibliothèque</a>
            </div>
        </section>

        @include('partials.flash-messages')

        <section class="panel">
            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            <div class="list">
                @forelse ($reviews as $review)
                    <article class="item" style="{{ !$review->is_visible ? 'opacity: 0.6;' : '' }}">
                        <div class="item-head">
                            <div>
                                <div class="item-title">{{ $review->user?->name ?? 'Utilisateur supprimé' }}</div>
                                <div class="item-subtitle">sur <a href="{{ route('reader.show', $review->book_id) }}" style="color: var(--accent); text-decoration: underline;">{{ $review->book?->title ?? 'Livre introuvable' }}</a> • {{ $review->created_at->diffForHumans() }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div class="stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                                    @endfor
                                </div>
                                @if (!$review->is_visible)
                                    <span class="tag" style="background: rgba(255,112,112,0.1); color: var(--danger); margin-top: 6px;">Masqué au public</span>
                                @else
                                    <span class="tag" style="background: rgba(77,216,154,0.1); color: var(--ok); margin-top: 6px;">Visible au public</span>
                                @endif
                            </div>
                        </div>

                        <div class="item-text">
                            {{ $review->review_text ?: 'Aucun commentaire écrit, seulement une note.' }}
                        </div>

                        <div class="item-actions">
                            <form method="POST" action="{{ route('admin.reviews.visibility', $review) }}" style="margin: 0;">
                                @csrf
                                @method('PUT')
                                <button class="link-button" type="submit">
                                    {{ $review->is_visible ? 'Masquer l\'avis' : 'Rendre visible' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" style="margin: 0;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis définitivement ?');">
                                @csrf
                                @method('DELETE')
                                <button class="danger-button" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div style="color: var(--muted); padding: 20px 0;">Aucun avis laissé par les lecteurs pour le moment.</div>
                @endforelse
            </div>

            <div style="margin-top: 24px;">
                {{ $reviews->links() }}
            </div>
        </section>

        @include('partials.app-footer')
    </div>
</body>
</html>
