<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-[1000px] space-y-5 px-5 sm:px-8">
            <h1 class="text-[26px] font-bold tracking-[-0.03em] text-ink">Perfil</h1>

            <div class="rounded-card border border-line bg-surface p-[22px] shadow-card sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="rounded-card border border-line bg-surface p-[22px] shadow-card sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="rounded-card border border-line bg-surface p-[22px] shadow-card sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
