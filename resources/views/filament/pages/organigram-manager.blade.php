<x-filament-panels::page>
    {{ $this->table }}

    <div class="mt-10 flex flex-col items-center">
        <h2 class="text-lg font-bold mb-2">Vista de organigrama</h2>
        <div id="vanilla-tree-org" class="w-full max-w-4xl bg-white rounded p-4 overflow-x-auto text-left" style="min-height: 400px;">
            <div id="org-tree-html">{!! $this->getVanillaTreeHtml() !!}</div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanilla-tree@1.6.0/dist/vanilla-tree.min.css" />
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/vanilla-tree@1.6.0/dist/vanilla-tree.min.js"></script>
        <script>
            function renderVanillaTreeOrg() {
                const treeHtml = @json($this->getVanillaTreeHtml());
                document.getElementById('org-tree-html').innerHTML = treeHtml;
                new VanillaTree('#org-tree-html ul');
            }
            document.addEventListener("livewire:update", renderVanillaTreeOrg);
            document.addEventListener("DOMContentLoaded", renderVanillaTreeOrg);
        </script>
    @endpush
</x-filament-panels::page>
