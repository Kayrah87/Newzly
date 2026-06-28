/**
 * Publication layout editor: drag-reorders the header/content/footer sections
 * and edits the colour palette, with a live preview. Data-driven (the preview
 * blocks are rendered with x-for over `order`), so reordering and recolouring
 * are reflected instantly. The order is mirrored into a hidden input as JSON
 * and the palette colours post as palette[<key>] fields.
 *
 *   x-data="structureEditor({ order: [...], palette: {...} })"
 */
export default function structureEditor(initial = {}) {
    return {
        order: Array.isArray(initial.order) ? initial.order : ['header', 'content', 'footer'],
        palette: initial.palette ?? {},
        meta: initial.meta ?? {},
        dragKey: null,

        start(event, key) {
            this.dragKey = key;
            event.dataTransfer.effectAllowed = 'move';
            try {
                event.dataTransfer.setData('text/plain', key);
            } catch (e) {
                // Ignore browsers that disallow setData during dragstart.
            }
        },

        over(event, key) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';

            if (this.dragKey === null || this.dragKey === key) {
                return;
            }

            const from = this.order.indexOf(this.dragKey);
            const to = this.order.indexOf(key);

            if (from === -1 || to === -1) {
                return;
            }

            this.order.splice(to, 0, this.order.splice(from, 1)[0]);
        },

        end() {
            this.dragKey = null;
        },

        moveUp(key) {
            const i = this.order.indexOf(key);
            if (i > 0) {
                this.order.splice(i - 1, 0, this.order.splice(i, 1)[0]);
            }
        },

        moveDown(key) {
            const i = this.order.indexOf(key);
            if (i !== -1 && i < this.order.length - 1) {
                this.order.splice(i + 1, 0, this.order.splice(i, 1)[0]);
            }
        },
    };
}
