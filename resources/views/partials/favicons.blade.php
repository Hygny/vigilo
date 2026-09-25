{{-- Favicons e ícones do app (brand kit Vigilo). Fonte única — incluído em
     todos os layouts. Claro por padrão; variante escura via prefers-color-scheme. --}}
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('brand/favicon-32.png') }}" media="(prefers-color-scheme: light)">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('brand/favicon-16.png') }}" media="(prefers-color-scheme: light)">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('brand/favicon-32-dark.png') }}" media="(prefers-color-scheme: dark)">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('brand/favicon-16-dark.png') }}" media="(prefers-color-scheme: dark)">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('brand/apple-touch-icon.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<meta name="theme-color" media="(prefers-color-scheme: light)" content="#FFFFFF">
<meta name="theme-color" media="(prefers-color-scheme: dark)" content="#1B1A13">
