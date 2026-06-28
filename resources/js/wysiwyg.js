import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

/**
 * Alpine component backing the self-hosted Tiptap rich-text editor.
 *
 * Replaces the previous TinyMCE CDN integration. The editable surface is a
 * Tiptap instance; the resulting HTML is mirrored into a hidden <textarea>
 * (referenced as `input`) so the surrounding form posts `content` exactly as
 * before. Storage stays HTML, so the server side is unaffected.
 *
 * The Editor instance is held in a closure variable, NOT on Alpine's reactive
 * `this`. If Alpine deep-proxies the editor, ProseMirror's internal identity
 * (`===`) checks on its document/state fail and dispatching a transaction throws
 * "Applying a mismatched transaction". Only the primitive `tick` is reactive,
 * which is all the toolbar needs to refresh its active state.
 */
export default function wysiwyg() {
    let editor = null;

    return {
        // Bumped on every transaction so Alpine re-evaluates toolbar active state.
        tick: 0,

        init() {
            editor = new Editor({
                element: this.$refs.editor,
                extensions: [
                    // StarterKit (v3) already bundles the Link extension, so we
                    // configure it here rather than adding it again — registering
                    // a duplicate 'link' mark corrupts the ProseMirror schema.
                    StarterKit.configure({
                        link: { openOnClick: false },
                    }),
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
            this.$refs.input.value = editor.getHTML();
        },

        destroy() {
            editor?.destroy();
            editor = null;
        },

        run(command) {
            command(editor.chain().focus()).run();
        },

        isActive(name, attrs = {}) {
            // Reference `tick` so Alpine tracks it as a dependency.
            return this.tick >= 0 && editor?.isActive(name, attrs);
        },

        setLink() {
            const previous = editor.getAttributes('link').href;
            const url = window.prompt('Link URL', previous || 'https://');

            if (url === null) {
                return;
            }

            if (url === '') {
                editor.chain().focus().extendMarkRange('link').unsetLink().run();

                return;
            }

            editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
        },
    };
}
