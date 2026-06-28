/**
 * Generic DOM-based drag-and-drop reorderer (no external dependency).
 *
 * Markup contract:
 *   <div x-data="sortable({ url: '/…/reorder' })">
 *     <ul x-ref="list" @dragover="over" @drop="drop" @dragstart="start" @dragend="end">
 *       <li data-sort-item draggable="true" data-sort-id="123"> … </li>
 *       …
 *     </ul>
 *     <input type="hidden" x-ref="input" name="order">   {{-- optional --}}
 *   </div>
 *
 * On drop it reads the resulting DOM order of `data-sort-id`s and either writes
 * it (JSON) into x-ref="input", PATCHes it to `config.url` as { order: [...] },
 * or both. `config.url` persistence shows a brief "saved" flag (this.saved).
 */
export default function sortable(config = {}) {
    return {
        dragId: null,
        saved: false,
        failed: false,

        start(event) {
            const item = event.target.closest('[data-sort-item]');
            if (! item) {
                return;
            }
            this.dragId = item.getAttribute('data-sort-id');
            item.classList.add('opacity-40');
            event.dataTransfer.effectAllowed = 'move';
            try {
                event.dataTransfer.setData('text/plain', this.dragId);
            } catch (e) {
                // Some browsers disallow setData here; the drag still works.
            }
        },

        end(event) {
            const item = event.target.closest('[data-sort-item]');
            if (item) {
                item.classList.remove('opacity-40');
            }
            this.dragId = null;
        },

        over(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';

            if (this.dragId === null) {
                return;
            }

            const list = this.$refs.list;
            const dragged = list.querySelector(`[data-sort-id="${CSS.escape(this.dragId)}"]`);
            const target = event.target.closest('[data-sort-item]');

            if (! dragged || ! target || target === dragged || target.parentElement !== list) {
                return;
            }

            const rect = target.getBoundingClientRect();
            const after = (event.clientY - rect.top) / rect.height > 0.5;
            list.insertBefore(dragged, after ? target.nextElementSibling : target);
        },

        drop(event) {
            event.preventDefault();
            this.persist();
        },

        order() {
            return [...this.$refs.list.querySelectorAll('[data-sort-item]')]
                .map((el) => el.getAttribute('data-sort-id'));
        },

        persist() {
            const ids = this.order();

            if (this.$refs.input) {
                this.$refs.input.value = JSON.stringify(ids);
            }

            if (! config.url) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            fetch(config.url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token ?? '',
                },
                body: JSON.stringify({ order: ids }),
            })
                .then((response) => {
                    if (! response.ok) {
                        throw new Error('Reorder failed');
                    }
                    this.flash('saved');
                })
                .catch(() => this.flash('failed'));
        },

        flash(which) {
            this[which] = true;
            setTimeout(() => {
                this[which] = false;
            }, 1800);
        },
    };
}
