{{-- Aplica o tema salvo antes do render (evita flash) e o reaplica após cada
     navegação SPA do Livewire: wire:navigate troca o DOM e o HTML novo vem sem
     data-theme, então reidratamos no evento livewire:navigated (que também
     dispara no load inicial). Fonte única — incluído em todos os layouts. --}}
<script>
    (function () {
        function applyTheme() {
            try {
                var t = localStorage.getItem('vigilo-theme');
                document.documentElement.setAttribute('data-theme', t === 'dark' ? 'dark' : 'light');
            } catch (e) {}
        }
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    })();
</script>
