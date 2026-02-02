<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/merchant-registrations">طلبات التجار</a>'
    . '<a class="nav-link" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link active" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'منشئ القالب المتقدم';
$subtitle = 'منشئ بصري شبيه بـ Shopify Sections مع معاينة حية وتوليد قالب PHP.';
ob_start();
?>
<style>
    .builder-shell {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 24px;
    }

    .builder-panel {
        background: rgba(17, 24, 39, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 20px;
        height: calc(100vh - 180px);
        overflow: auto;
    }

    .builder-panel h6 {
        font-weight: 700;
        margin-bottom: 12px;
    }

    .sections-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .builder-section {
        background: rgba(255, 255, 255, 0.05);
        border: 1px dashed rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: grab;
    }

    .builder-section .section-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .builder-section.dragging {
        opacity: 0.6;
    }

    .builder-section .handle {
        color: #94a3b8;
        font-size: 1rem;
        cursor: grab;
    }

    .builder-section .section-remove {
        border-radius: 999px;
        padding: 2px 10px;
        font-size: 0.8rem;
    }

    .builder-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .preview-stage {
        background: #0b1120;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 18px;
        min-height: 70vh;
        position: relative;
    }

    .preview-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .preview-frame {
        background: #020617;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 20px;
        min-height: 70vh;
        transition: width 0.25s ease;
        margin: 0 auto;
    }

    .preview-frame.phone {
        width: 390px;
    }

    .preview-frame.desktop {
        width: 100%;
    }

    .preview-section {
        padding: 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .preview-section:last-child {
        border-bottom: none;
    }

    .preview-banner {
        background: #000;
        color: #fff;
        padding: 10px 16px;
        border-radius: 999px;
        display: inline-block;
        font-weight: 600;
    }

    .preview-price {
        color: #22c55e;
        font-size: 1.4rem;
        font-weight: 700;
    }

    .preview-discount {
        display: inline-block;
        margin-right: 8px;
        background: #22c55e;
        color: #fff;
        padding: 4px 10px;
        border-radius: 999px;
        animation: pulse 1.4s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }

    .inline-edit {
        outline: none;
        border-bottom: 1px dashed transparent;
        transition: border-color 0.2s ease;
    }

    .inline-edit:focus {
        border-color: rgba(148, 163, 184, 0.8);
    }

    .export-box {
        background: rgba(15, 23, 42, 0.7);
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 16px;
        margin-top: 16px;
    }

    .export-box textarea {
        background: #0b1120;
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #e2e8f0;
        min-height: 220px;
    }
</style>

<div class="builder-shell">
    <div class="builder-panel">
        <h6>إعدادات المحتوى</h6>
        <div class="mb-3">
            <label class="form-label">نص الشريط المتحرك</label>
            <input type="text" class="form-control" id="builderTicker" value="تخفيضات رمضان لفترة محدودة ✨ الكمية محدودة - اطلب الآن">
        </div>
        <div class="mb-3">
            <label class="form-label">اسم المنتج</label>
            <input type="text" class="form-control" id="builderTitle" value="سماعات بلوتوث">
        </div>
        <div class="mb-3">
            <label class="form-label">السعر</label>
            <input type="text" class="form-control" id="builderPrice" value="4900 دج">
        </div>
        <div class="mb-3">
            <label class="form-label">وصف مختصر</label>
            <textarea class="form-control" id="builderSubtitle" rows="3">تخفيض خاص بمناسبة رمضان مع شحن سريع لكل الولايات.</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">روابط الصور (سطر لكل صورة)</label>
            <textarea class="form-control" id="builderImages" rows="4">https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80
https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">وصف تفصيلي</label>
            <textarea class="form-control" id="builderDescription" rows="4">بطارية قوية - جودة صوت عالية - تصميم مريح - ضمان سنة كاملة.</textarea>
        </div>
        <div class="builder-actions">
            <button class="btn btn-accent" type="button" id="btnPreview">معاينة</button>
            <button class="btn btn-outline-light" type="button" id="btnReset">إعادة ضبط</button>
            <button class="btn btn-outline-info" type="button" id="btnCopy">نسخ القالب</button>
            <button class="btn btn-outline-success" type="button" id="btnDownload">حفظ كملف</button>
        </div>

        <hr class="border-secondary my-4">

        <h6>الأقسام (سحب لترتيبها)</h6>
        <div class="sections-list" id="sectionsList">
            <div class="builder-section" data-section="ticker">
                <div class="section-info">
                    <span class="handle" draggable="true">⇅</span>
                    <span>الشريط المتحرك</span>
                </div>
                <button class="btn btn-sm btn-outline-danger section-remove" type="button" data-action="remove">حذف</button>
            </div>
            <div class="builder-section" data-section="hero">
                <div class="section-info">
                    <span class="handle" draggable="true">⇅</span>
                    <span>العنوان + السعر</span>
                </div>
                <button class="btn btn-sm btn-outline-danger section-remove" type="button" data-action="remove">حذف</button>
            </div>
            <div class="builder-section" data-section="gallery">
                <div class="section-info">
                    <span class="handle" draggable="true">⇅</span>
                    <span>صور المنتج</span>
                </div>
                <button class="btn btn-sm btn-outline-danger section-remove" type="button" data-action="remove">حذف</button>
            </div>
            <div class="builder-section" data-section="description">
                <div class="section-info">
                    <span class="handle" draggable="true">⇅</span>
                    <span>الوصف + صور</span>
                </div>
                <button class="btn btn-sm btn-outline-danger section-remove" type="button" data-action="remove">حذف</button>
            </div>
            <div class="builder-section" data-section="order">
                <div class="section-info">
                    <span class="handle" draggable="true">⇅</span>
                    <span>نموذج الطلب</span>
                </div>
                <button class="btn btn-sm btn-outline-danger section-remove" type="button" data-action="remove">حذف</button>
            </div>
            <div class="builder-section" data-section="footer">
                <div class="section-info">
                    <span class="handle" draggable="true">⇅</span>
                    <span>Footer</span>
                </div>
                <button class="btn btn-sm btn-outline-danger section-remove" type="button" data-action="remove">حذف</button>
            </div>
        </div>
        <div class="mt-3">
            <label class="form-label">إضافة قسم جديد</label>
            <div class="d-flex gap-2">
                <input type="text" class="form-control" id="newSectionName" placeholder="مثال: شهادات العملاء">
                <button class="btn btn-outline-info" type="button" id="btnAddSection">إضافة</button>
            </div>
        </div>
    </div>

    <div class="preview-stage">
        <div class="preview-toolbar">
            <div class="btn-group">
                <button class="btn btn-outline-light" type="button" data-device="desktop">كمبيوتر</button>
                <button class="btn btn-outline-light" type="button" data-device="phone">هاتف</button>
            </div>
            <div class="text-secondary">معاينة مباشرة مثل Shopify Sections</div>
        </div>
        <div class="preview-frame desktop" id="livePreview"></div>

        <div class="export-box">
            <h6 class="mb-2">توليد قالب PHP</h6>
            <textarea class="form-control" id="templateOutput" readonly></textarea>
        </div>
    </div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">معاينة قبل الحفظ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalPreview" class="preview-frame desktop"></div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', () => {
        const sectionsList = document.getElementById('sectionsList');
        const livePreview = document.getElementById('livePreview');
        const modalPreview = document.getElementById('modalPreview');
        const templateOutput = document.getElementById('templateOutput');
        const modalElement = document.getElementById('previewModal');
        const previewModal = window.bootstrap && modalElement ? new bootstrap.Modal(modalElement) : null;

        const inputs = {
            ticker: document.getElementById('builderTicker'),
            title: document.getElementById('builderTitle'),
            price: document.getElementById('builderPrice'),
            subtitle: document.getElementById('builderSubtitle'),
            images: document.getElementById('builderImages'),
            description: document.getElementById('builderDescription'),
        };

        const sectionTemplates = (data, editableAttr, bindAttr) => ({
            ticker: `<div class="preview-section"><div class="preview-banner inline-edit" ${editableAttr} ${bindAttr('ticker')}>${data.ticker}</div></div>`,
            hero: `<div class="preview-section"><div class="preview-discount">تخفيض</div><span class="preview-price inline-edit" ${editableAttr} ${bindAttr('price')}>${data.price}</span><h2 class="mt-3 inline-edit" ${editableAttr} ${bindAttr('title')}>${data.title}</h2><p class="text-secondary inline-edit" ${editableAttr} ${bindAttr('subtitle')}>${data.subtitle}</p></div>`,
            gallery: `<div class="preview-section"><div class="row g-2">${data.images.map((src) => `<div class="col-md-4"><img src="${src}" class="img-fluid rounded" alt=""></div>`).join('')}</div></div>`,
            description: `<div class="preview-section"><h4>وصف المنتج</h4><p class="text-secondary inline-edit" ${editableAttr} ${bindAttr('description')}>${data.description}</p></div>`,
            order: `<div class="preview-section"><h4>نموذج الطلب</h4><div class="text-secondary">سيظهر نموذج الطلب النهائي هنا داخل الموقع.</div></div>`,
            footer: `<div class="preview-section text-secondary">Footer مرتب مع بيانات المتجر.</div>`,
        });

        const buildData = () => ({
            ticker: inputs.ticker.value,
            title: inputs.title.value,
            price: inputs.price.value,
            subtitle: inputs.subtitle.value,
            images: inputs.images.value.split('\n').filter(Boolean),
            description: inputs.description.value,
        });

        const renderPreview = (target) => {
            const data = buildData();
            const sections = Array.from(sectionsList.querySelectorAll('.builder-section')).map((el) => el.dataset.section);
            const editableAttr = target === livePreview ? 'contenteditable="true"' : '';
            const bindAttr = target === livePreview ? (key) => `data-bind="${key}"` : () => '';
            const templates = sectionTemplates(data, editableAttr, bindAttr);
            target.innerHTML = sections.map((key) => templates[key] || `<div class="preview-section">${key}</div>`).join('');
        };

        const generateTemplate = () => {
            const data = buildData();
            const sections = Array.from(sectionsList.querySelectorAll('.builder-section')).map((el) => el.dataset.section);
            const htmlSections = sectionTemplates(data, '', () => '');
            return `<!-- Generated by Template Builder -->\n<div class="landing-template">\n${sections.map((key) => htmlSections[key] || `<div>${key}</div>`).join('\n')}\n</div>`;
        };

        const updateOutput = () => {
            templateOutput.value = generateTemplate();
        };

        const enableDrag = () => {
            let dragged = null;
            sectionsList.addEventListener('dragstart', (event) => {
                const handle = event.target.closest('.handle');
                if (!handle) return;
                const section = handle.closest('.builder-section');
                if (!section) return;
                dragged = section;
                section.classList.add('dragging');
                event.dataTransfer.setData('text/plain', '');
                event.dataTransfer.effectAllowed = 'move';
            });
            sectionsList.addEventListener('dragend', (event) => {
                const section = event.target.closest('.builder-section');
                if (!section) return;
                section.classList.remove('dragging');
                renderPreview(livePreview);
                updateOutput();
            });
            sectionsList.addEventListener('dragover', (event) => {
                event.preventDefault();
                if (!dragged) return;
                const afterElement = [...sectionsList.querySelectorAll('.builder-section:not(.dragging)')]
                    .find((el) => event.clientY <= el.offsetTop + el.offsetHeight / 2);
                if (afterElement) {
                    sectionsList.insertBefore(dragged, afterElement);
                } else {
                    sectionsList.appendChild(dragged);
                }
            });
        };

        document.querySelectorAll('[data-device]').forEach((btn) => {
            btn.addEventListener('click', () => {
                livePreview.classList.toggle('phone', btn.dataset.device === 'phone');
                livePreview.classList.toggle('desktop', btn.dataset.device === 'desktop');
            });
        });

        document.getElementById('btnPreview').addEventListener('click', () => {
            renderPreview(modalPreview);
            if (previewModal) {
                previewModal.show();
            } else {
                modalPreview.scrollIntoView({ behavior: 'smooth' });
            }
        });

        document.getElementById('btnReset').addEventListener('click', () => {
            inputs.ticker.value = 'تخفيضات رمضان لفترة محدودة ✨ الكمية محدودة - اطلب الآن';
            inputs.title.value = 'سماعات بلوتوث';
            inputs.price.value = '4900 دج';
            inputs.subtitle.value = 'تخفيض خاص بمناسبة رمضان مع شحن سريع لكل الولايات.';
            inputs.images.value = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80\nhttps://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80';
            inputs.description.value = 'بطارية قوية - جودة صوت عالية - تصميم مريح - ضمان سنة كاملة.';
            renderPreview(livePreview);
            updateOutput();
        });

        document.getElementById('btnCopy').addEventListener('click', async () => {
            await navigator.clipboard.writeText(templateOutput.value);
        });

        document.getElementById('btnDownload').addEventListener('click', () => {
            const blob = new Blob([templateOutput.value], { type: 'text/plain' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'landing-template.html';
            link.click();
            URL.revokeObjectURL(link.href);
        });

        document.getElementById('btnAddSection').addEventListener('click', () => {
            const name = document.getElementById('newSectionName').value.trim();
            if (!name) return;
            const div = document.createElement('div');
            div.className = 'builder-section';
            div.dataset.section = name;
            div.innerHTML = `<div class="section-info"><span class="handle" draggable="true">⇅</span><span>${name}</span></div><button class="btn btn-sm btn-outline-danger section-remove" type="button" data-action="remove">حذف</button>`;
            sectionsList.appendChild(div);
            document.getElementById('newSectionName').value = '';
            renderPreview(livePreview);
            updateOutput();
        });

        sectionsList.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-action="remove"]');
            if (!removeButton) return;
            const section = removeButton.closest('.builder-section');
            if (!section) return;
            section.remove();
            renderPreview(livePreview);
            updateOutput();
        });

        Object.values(inputs).forEach((input) => {
            input.addEventListener('input', () => {
                renderPreview(livePreview);
                updateOutput();
            });
        });

        livePreview.addEventListener('input', (event) => {
            const editable = event.target.closest('[data-bind]');
            if (!editable) return;
            const key = editable.dataset.bind;
            if (!key || !inputs[key]) return;
            inputs[key].value = editable.innerText.trim();
            updateOutput();
        });

        enableDrag();
        renderPreview(livePreview);
        updateOutput();
    });
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
