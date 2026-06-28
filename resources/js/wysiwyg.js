import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';

/**
 * Alpine component backing the self-hosted Tiptap rich-text editor.
 *
 * Replaces the previous TinyMCE CDN integration. The editable surface is a
 * Tiptap instance; the resulting HTML is mirrored into a hidden <textarea>
 * (referenced as `input`) so the surrounding form posts `content` exactly as
 * before. Storage stays HTML, so the server side is unaffected.
 */
export default function wysiwyg() {
    return {
        editor: null,
        // Bumped on every transaction so Alpine re-evaluates toolbar active state.
        tick: 0,

        init() {
            this.editor = new Editor({
                element: this.$refs.editor,
                extensions: [
                    StarterKit,
                    Link.configure({ openOnClick: false }),
                ],
                content: this.$refs.input.value || '',
                editorProps: {
                    attributes: {
                        class: 'prose max-w-none focus:outline-none min-h-[16rem]',
                    },
                },
                onUpdate: ({ editor }) => {
                    this.$refs.input.value = editor.getHTML();
                },
                onTransaction: () => {
                    this.tick++;
                },
            });

            // Ensure the hidden field is normalised even if the form is
            // submitted without any edit being made.
            this.$refs.input.value = this.editor.getHTML();
        },

        destroy() {
            this.editor?.destroy();
        },

        run(command) {
            command(this.editor.chain().focus()).run();
        },

        isActive(name, attrs = {}) {
            // Reference `tick` so Alpine tracks it as a dependency.
            return this.tick >= 0 && this.editor?.isActive(name, attrs);
        },

        setLink() {
            const previous = this.editor.getAttributes('link').href;
            const url = window.prompt('Link URL', previous || 'https://');

            if (url === null) {
                return;
            }

            if (url === '') {
                this.editor.chain().focus().extendMarkRange('link').unsetLink().run();

                return;
            }

            this.editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
        },
    };
}
