<div x-data="iconPicker(@js($getState()))" x-init="init()" class="icon-grid-wrapper">

    @php
        $icons = config('icons.available', []);
    @endphp

    <style>
        /* Scoped styles for the preview */
        .icon-preview-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            border: 1px dashed #d1d5db;
            /* gray-300 */
            border-radius: 0.5rem;
            background-color: #f9fafb;
            /* gray-50 */
            transition: all 0.2s;
            min-height: 100px;
        }

        .icon-preview-box svg {
            width: 3rem !important;
            /* 48px */
            height: 3rem !important;
            color: #4f46e5;
            /* indigo-600 */
        }

        /* Ensure the source grid is absolutely hidden from view but accessible to JS */
        .icon-source-grid {
            display: none !important;
        }
    </style>

    <div class="mt-2">
        <div id="icon-select-preview" class="icon-preview-box">
            <span class="text-gray-400 text-sm">Select an icon to preview</span>
        </div>
    </div>

    <!-- Hidden Source Grid -->
    <div class="icon-source-grid" aria-hidden="true">
        @foreach ($icons as $value => $label)
            <div data-icon="{{ $value }}">
                @include('filament.components.icon-svg', ['icon' => $value])
            </div>
        @endforeach
    </div>

</div>

<script>
    function iconPicker(initialValue) {
        return {
            selected: initialValue,
            init() {
                // Try to find the select element by ID
                const sel = document.getElementById('icon-select');
                if (sel) {
                    // If initialValue is not provided, try to get it from the select
                    if (!this.selected) {
                        this.selected = sel.value || null;
                    }

                    // Listen for changes on the select element
                    sel.addEventListener('change', (e) => {
                        this.selected = e.target.value || null;
                        this.updatePreview();
                    });

                    // Also listen for input events just in case
                    sel.addEventListener('input', (e) => {
                        this.selected = e.target.value || null;
                        this.updatePreview();
                    });
                }

                this.updatePreview();
            },
            updatePreview() {
                const preview = document.getElementById('icon-select-preview');
                if (!preview) return;

                if (!this.selected) {
                    preview.innerHTML = '<span class="text-gray-400 text-sm">Select an icon to preview</span>';
                    return;
                }

                // Find the SVG in the hidden grid
                const node = document.querySelector('.icon-source-grid [data-icon="' + this.selected + '"] svg');

                if (node) {
                    // Clone the node to avoid moving it
                    const clone = node.cloneNode(true);
                    preview.innerHTML = '';
                    preview.appendChild(clone);
                } else {
                    preview.innerHTML = '<span class="text-gray-400 text-sm">Icon not found</span>';
                }
            }
        }
    }
</script>
