<script setup lang="ts">
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import { Bold, Italic, List, ListOrdered, Link2, Quote, Heading2, Heading3 } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Link.configure({ openOnClick: false }),
        Placeholder.configure({ placeholder: props.placeholder ?? 'Digite aqui...' }),
    ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

function setLink() {
    const url = prompt('Digite a URL:');
    if (url) {
        editor.value?.chain().focus().setLink({ href: url }).run();
    }
}
</script>

<template>
    <div class="border border-slate-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition-all">
        <!-- Toolbar -->
        <div class="flex flex-wrap gap-1 p-2 bg-slate-50 border-b border-slate-200">
            <button
                v-for="{ action, icon, title, isActive } in [
                    { title: 'Negrito', icon: Bold, action: () => editor?.chain().focus().toggleBold().run(), isActive: editor?.isActive('bold') },
                    { title: 'Itálico', icon: Italic, action: () => editor?.chain().focus().toggleItalic().run(), isActive: editor?.isActive('italic') },
                    { title: 'Título 2', icon: Heading2, action: () => editor?.chain().focus().toggleHeading({ level: 2 }).run(), isActive: editor?.isActive('heading', { level: 2 }) },
                    { title: 'Título 3', icon: Heading3, action: () => editor?.chain().focus().toggleHeading({ level: 3 }).run(), isActive: editor?.isActive('heading', { level: 3 }) },
                    { title: 'Lista', icon: List, action: () => editor?.chain().focus().toggleBulletList().run(), isActive: editor?.isActive('bulletList') },
                    { title: 'Lista numerada', icon: ListOrdered, action: () => editor?.chain().focus().toggleOrderedList().run(), isActive: editor?.isActive('orderedList') },
                    { title: 'Citação', icon: Quote, action: () => editor?.chain().focus().toggleBlockquote().run(), isActive: editor?.isActive('blockquote') },
                    { title: 'Link', icon: Link2, action: setLink, isActive: editor?.isActive('link') },
                ]"
                :key="title"
                type="button"
                :title="title"
                :class="[
                    'p-1.5 rounded transition-colors',
                    isActive ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-200'
                ]"
                @click="action"
            >
                <component :is="icon" :size="16" />
            </button>
        </div>
        <!-- Editor -->
        <EditorContent
            :editor="editor"
            class="prose prose-sm max-w-none p-4 min-h-[180px] focus:outline-none [&_.ProseMirror]:outline-none [&_.ProseMirror]:min-h-[140px]"
        />
    </div>
</template>
