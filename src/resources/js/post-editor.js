import { Editor } from '@tiptap/core';
import Image from '@tiptap/extension-image';
import StarterKit from '@tiptap/starter-kit';

const form = document.querySelector('#post-form');
const content = document.querySelector('#content');
const error = document.querySelector('#upload-error');
const saveButtons = form.querySelectorAll('button[type="submit"]');
const featuredId = document.querySelector('#featured-image-id');
const featuredPreview = document.querySelector('#featured-preview');
const featuredPreviewImage = document.querySelector('#featured-preview-image');
const bodyImage = document.querySelector('#body-image');
const insertImage = document.querySelector('#insert-image');
const featuredImage = document.querySelector('#featured-image');
const removeFeatured = document.querySelector('#remove-featured');
let uploading = false;
const editor = new Editor({
    element: document.querySelector('#editor'),
    extensions: [
        StarterKit.configure({ heading: { levels: [2, 3] }, link: { openOnClick: false } }),
        Image,
    ],
    content: content.value,
    editorProps: { attributes: { 'aria-label': '記事本文', role: 'textbox', 'aria-multiline': 'true' } },
    onUpdate: ({ editor }) => { content.value = editor.getHTML(); },
});

for (const button of saveButtons) button.disabled = false;
form.addEventListener('submit', () => { content.value = editor.getHTML(); });

const commands = {
    heading2: () => editor.chain().focus().toggleHeading({ level: 2 }).run(),
    heading3: () => editor.chain().focus().toggleHeading({ level: 3 }).run(),
    bold: () => editor.chain().focus().toggleBold().run(),
    italic: () => editor.chain().focus().toggleItalic().run(),
    bulletList: () => editor.chain().focus().toggleBulletList().run(),
    orderedList: () => editor.chain().focus().toggleOrderedList().run(),
    blockquote: () => editor.chain().focus().toggleBlockquote().run(),
    link: () => {
        const url = window.prompt('リンク先のURLを入力してください。', editor.getAttributes('link').href || 'https://');
        if (url === null) return;
        if (url.trim() === '') {
            editor.chain().focus().extendMarkRange('link').unsetLink().run();
        } else {
            editor.chain().focus().extendMarkRange('link').setLink({ href: url.trim() }).run();
        }
    },
    unlink: () => editor.chain().focus().extendMarkRange('link').unsetLink().run(),
    undo: () => editor.chain().focus().undo().run(),
    redo: () => editor.chain().focus().redo().run(),
};

document.querySelectorAll('[data-command]').forEach((button) => {
    button.addEventListener('mousedown', (event) => event.preventDefault());
    button.addEventListener('click', () => commands[button.dataset.command]());
});

async function upload(file) {
    if (uploading) return null;
    uploading = true;
    error.hidden = true;
    for (const button of saveButtons) button.disabled = true;
    insertImage.disabled = true;
    featuredImage.disabled = true;
    removeFeatured.disabled = true;
    const data = new FormData();
    data.append('image', file);

    try {
        const response = await fetch(form.dataset.uploadUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, Accept: 'application/json' },
            body: data,
        });
        if (!response.ok) {
            if (response.status === 413 || response.status === 422) throw new Error('画像はJPEG・PNG・GIF・WebP形式で、2MB以下にしてください。');
            if (response.status === 419 || response.status === 401) throw new Error('セッションが切れました。再ログインしてください。');
            throw new Error('画像をアップロードできませんでした。時間をおいて再度お試しください。');
        }
        return await response.json();
    } catch (exception) {
        error.textContent = exception.message || '画像をアップロードできませんでした。';
        error.hidden = false;
        return null;
    } finally {
        uploading = false;
        for (const button of saveButtons) button.disabled = false;
        insertImage.disabled = false;
        featuredImage.disabled = false;
        removeFeatured.disabled = false;
    }
}

insertImage.addEventListener('click', () => bodyImage.click());
bodyImage.addEventListener('change', async () => {
    const file = bodyImage.files[0];
    if (!file) return;
    const image = await upload(file);
    if (image) editor.chain().focus().setImage({ src: image.url, alt: '' }).run();
    bodyImage.value = '';
});

featuredImage.addEventListener('change', async () => {
    const file = featuredImage.files[0];
    if (!file) return;
    const image = await upload(file);
    if (image) {
        featuredId.value = image.id;
        featuredPreviewImage.src = image.url;
        featuredPreview.hidden = false;
    }
    featuredImage.value = '';
});
removeFeatured.addEventListener('click', () => {
    featuredId.value = '';
    featuredPreviewImage.removeAttribute('src');
    featuredPreview.hidden = true;
});
