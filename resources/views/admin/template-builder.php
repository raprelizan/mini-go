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
$title = 'منشئ القالب المباشر';
$subtitle = 'تحرير الأقسام وترتيبها مع معاينة مباشرة قبل الحفظ.';
ob_start();
?>
<style>
    .builder-panel {
        background: rgba(17, 24, 39, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 20px;
    }

    .builder-section {
        background: rgba(255, 255, 255, 0.04);
        border: 1px dashed rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        padding: 12px 16px;
        margin-bottom: 10px;
        cursor: grab;
    }

    .builder-section.dragging {
        opacity: 0.6;
    }

    .preview-panel {
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 20px;
        background: #0b1120;
        min-height: 500px;
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
</style>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="builder-panel">
            <h5 class="mb-3">الإعدادات الأساسية</h5>
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
            <div class="d-flex gap-2">
                <button class="btn btn-accent" type="button" id="btnPreview">معاينة</button>
                <button class="btn btn-outline-light" type="button" id="btnReset">إعادة ضبط</button>
            </div>
        </div>
        <div class="builder-panel mt-4">
            <h5 class="mb-3">ترتيب الأقسام</h5>
            <div id="sectionsList">
                <div class="builder-section" draggable="true" data-section="ticker">الشريط المتحرك</div>
                <div class="builder-section" draggable="true" data-section="hero">العنوان + السعر + وصف</div>
                <div class="builder-section" draggable="true" data-section="gallery">صور المنتج</div>
                <div class="builder-section" draggable="true" data-section="description">الوصف + الصور</div>
                <div class="builder-section" draggable="true" data-section="order">نموذج الطلب</div>
                <div class="builder-section" draggable="true" data-section="footer">Footer</div>
            </div>
            <div class="mt-3">
                <label class="form-label">إضافة قسم جديد</label>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control" id="newSectionName" placeholder="مثال: شهادات العملاء">
                    <button class="btn btn-outline-info" type="button" id="btnAddSection">إضافة</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="preview-panel" id="livePreview"></div>
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
                <div id="modalPreview" class="preview-panel"></div>
            </div>
        </div>
    </div>
</div>

<script>
    const sectionsList = document.getElementById('sectionsList');
    const livePreview = document.getElementById('livePreview');
    const modalPreview = document.getElementById('modalPreview');
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));

    const inputs = {
        ticker: document.getElementById('builderTicker'),
        title: document.getElementById('builderTitle'),
        price: document.getElementById('builderPrice'),
        subtitle: document.getElementById('builderSubtitle'),
        images: document.getElementById('builderImages'),
        description: document.getElementById('builderDescription'),
    };

    const renderPreview = (target) => {
        const images = inputs.images.value.split('\\n').filter(Boolean);
        const sections = Array.from(sectionsList.querySelectorAll('.builder-section')).map((el) => el.dataset.section);
        const sectionHtml = {
            ticker: `<div class="preview-section"><div class="preview-banner">${inputs.ticker.value}</div></div>`,
            hero: `<div class="preview-section"><div class="preview-discount">تخفيض</div><span class="preview-price">${inputs.price.value}</span><h2 class="mt-3">${inputs.title.value}</h2><p class="text-secondary">${inputs.subtitle.value}</p></div>`,
            gallery: `<div class="preview-section"><div class="row g-2">${images.map((src) => `<div class="col-md-4"><img src="${src}" class="img-fluid rounded" alt=""></div>`).join('')}</div></div>`,
            description: `<div class="preview-section"><h4>وصف المنتج</h4><p class="text-secondary">${inputs.description.value}</p></div>`,
            order: `<div class="preview-section"><h4>نموذج الطلب</h4><div class="text-secondary">سيظهر نموذج الطلب النهائي هنا داخل الموقع.</div></div>`,
            footer: `<div class="preview-section text-secondary">Footer مرتب مع بيانات المتجر.</div>`,
        };
        target.innerHTML = sections.map((key) => sectionHtml[key] || `<div class="preview-section">${key}</div>`).join('');
    };

    const enableDrag = () => {
        let dragged = null;
        sectionsList.addEventListener('dragstart', (event) => {
            dragged = event.target;
            event.target.classList.add('dragging');
        });
        sectionsList.addEventListener('dragend', (event) => {
            event.target.classList.remove('dragging');
            renderPreview(livePreview);
        });
        sectionsList.addEventListener('dragover', (event) => {
            event.preventDefault();
            const afterElement = [...sectionsList.querySelectorAll('.builder-section:not(.dragging)')]
                .find((el) => event.clientY <= el.offsetTop + el.offsetHeight / 2);
            if (afterElement) {
                sectionsList.insertBefore(dragged, afterElement);
            } else {
                sectionsList.appendChild(dragged);
            }
        });
    };

    document.getElementById('btnPreview').addEventListener('click', () => {
        renderPreview(modalPreview);
        previewModal.show();
    });

    document.getElementById('btnReset').addEventListener('click', () => {
        inputs.ticker.value = 'تخفيضات رمضان لفترة محدودة ✨ الكمية محدودة - اطلب الآن';
        inputs.title.value = 'سماعات بلوتوث';
        inputs.price.value = '4900 دج';
        inputs.subtitle.value = 'تخفيض خاص بمناسبة رمضان مع شحن سريع لكل الولايات.';
        inputs.images.value = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80\\nhttps://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80';\n        inputs.description.value = 'بطارية قوية - جودة صوت عالية - تصميم مريح - ضمان سنة كاملة.';\n        renderPreview(livePreview);\n    });\n\n    document.getElementById('btnAddSection').addEventListener('click', () => {\n        const name = document.getElementById('newSectionName').value.trim();\n        if (!name) return;\n        const div = document.createElement('div');\n        div.className = 'builder-section';\n        div.draggable = true;\n        div.dataset.section = name;\n        div.textContent = name;\n        sectionsList.appendChild(div);\n        document.getElementById('newSectionName').value = '';\n        renderPreview(livePreview);\n    });\n\n    Object.values(inputs).forEach((input) => {\n        input.addEventListener('input', () => renderPreview(livePreview));\n    });\n\n    enableDrag();\n    renderPreview(livePreview);\n</script>\n<?php\n$content = ob_get_clean();\nrequire __DIR__ . '/../layouts/app.php';\nPHP","workdir":"/workspace/mini-go"}    } )"""}  # ၘ
