const richEditors = document.querySelectorAll('textarea.rich-editor-input');

if (window.tinymce && richEditors.length > 0) {
    const csrfToken = document.querySelector('input[name="_token"]')?.value || '';
    const uploadUrl = richEditors[0].dataset.uploadUrl || '/merchant/editor/upload';

    tinymce.init({
        selector: 'textarea.rich-editor-input',
        height: 360,
        menubar: false,
        plugins: 'lists link image table code autoresize',
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | blockquote | link image | removeformat | code',
        content_style: 'img {max-width: 100%; height: auto; display: block; margin: 12px auto;}',
        images_upload_handler: (blobInfo) => new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            fetch(uploadUrl, {
                method: 'POST',
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.location) {
                        resolve(data.location);
                    } else {
                        reject(data.error || 'تعذر رفع الصورة.');
                    }
                })
                .catch(() => reject('تعذر رفع الصورة.'));
        }),
    });
}
