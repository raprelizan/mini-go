<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/merchant-registrations">طلبات التجار</a>'
    . '<a class="nav-link" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link active" href="/admin/customizer">مخصص الصفحات</a>'
    . '<a class="nav-link" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'مخصص الصفحات';
$subtitle = 'إدارة الأقسام والكتل الخاصة بكل صفحة مع معاينة مباشرة.';
ob_start();
?>
<style>
    .customizer-shell {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 24px;
    }
    .customizer-panel {
        background: rgba(17, 24, 39, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 20px;
        max-height: calc(100vh - 190px);
        overflow: auto;
    }
    .section-item {
        background: rgba(255, 255, 255, 0.06);
        border: 1px dashed rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        padding: 12px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: grab;
    }
    .section-item.active {
        border-color: #38bdf8;
        box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.3);
    }
    .section-item .handle {
        color: #94a3b8;
    }
    .settings-grid {
        display: grid;
        gap: 12px;
    }
    .preview-frame {
        width: 100%;
        min-height: 75vh;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: #0b1120;
    }
    .blocks-list .block-card {
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        background: rgba(15, 23, 42, 0.6);
    }
</style>

<?php if (!$page) : ?>
    <div class="card app-card">
        <div class="card-body">
            <h5 class="mb-3">اختر صفحة لبدء التخصيص</h5>
            <div class="list-group">
                <?php foreach ($pages as $item) : ?>
                    <a class="list-group-item list-group-item-action bg-transparent text-light" href="/admin/customizer?page_id=<?= (int) $item['id'] ?>">
                        <?= htmlspecialchars($item['title']) ?> - <?= htmlspecialchars($item['merchant_name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="customizer-shell">
        <div class="customizer-panel">
            <h6>الصفحة المحددة</h6>
            <p class="text-secondary mb-4"><?= htmlspecialchars($page['title']) ?> - <?= htmlspecialchars($page['merchant_name']) ?></p>

            <form method="post" action="/admin/customizer/save" id="customizerForm" class="vstack gap-3">
                <?= csrf_field() ?>
                <input type="hidden" name="page_id" value="<?= (int) $page['id'] ?>">
                <input type="hidden" name="customization_json" id="customizationJson">
                <input type="hidden" name="action" id="customizerAction" value="draft">

                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-outline-info" type="button" id="btnSaveDraft">حفظ المسودة</button>
                    <button class="btn btn-accent" type="button" id="btnPublish">نشر التخصيص</button>
                    <button class="btn btn-outline-light" type="button" id="btnPreviewRefresh">تحديث المعاينة</button>
                </div>
            </form>

            <hr class="border-secondary my-4">

            <h6>الأقسام</h6>
            <div class="d-flex gap-2 mb-3">
                <select class="form-select" id="sectionType">
                    <option value="hero">Hero</option>
                    <option value="banner">Banner</option>
                    <option value="product_grid">Product Grid</option>
                </select>
                <button class="btn btn-outline-light" type="button" id="btnAddSection">إضافة</button>
            </div>
            <div class="vstack gap-2" id="sectionsList"></div>

            <hr class="border-secondary my-4">

            <h6>إعدادات القسم</h6>
            <div id="sectionSettings" class="settings-grid text-secondary">اختر قسمًا لعرض الإعدادات.</div>
        </div>

        <div>
            <iframe class="preview-frame" id="previewFrame" src="/admin/customizer/preview?page_id=<?= (int) $page['id'] ?>"></iframe>
        </div>
    </div>

    <script>
        const sectionCatalog = {
            hero: {
                label: 'Hero',
                settings: [
                    { key: 'heading', label: 'العنوان' },
                    { key: 'subheading', label: 'الوصف' },
                    { key: 'button_text', label: 'نص الزر' },
                    { key: 'button_url', label: 'رابط الزر' },
                    { key: 'image_url', label: 'رابط الصورة' },
                    { key: 'background_color', label: 'لون الخلفية' },
                    { key: 'text_color', label: 'لون النص' },
                    { key: 'padding', label: 'Padding' },
                ],
                blocks: false,
            },
            banner: {
                label: 'Banner',
                settings: [
                    { key: 'text', label: 'النص' },
                    { key: 'background_color', label: 'لون الخلفية' },
                    { key: 'text_color', label: 'لون النص' },
                ],
                blocks: false,
            },
            product_grid: {
                label: 'Product Grid',
                settings: [
                    { key: 'heading', label: 'العنوان' },
                    { key: 'columns', label: 'عدد الأعمدة' },
                ],
                blocks: true,
            },
        };

        const initialData = <?= json_encode($customizationData, JSON_UNESCAPED_UNICODE) ?>;
        let customization = initialData && Array.isArray(initialData.sections) ? initialData : { sections: [] };
        let activeIndex = 0;

        const sectionsList = document.getElementById('sectionsList');
        const settingsPanel = document.getElementById('sectionSettings');
        const customizationJson = document.getElementById('customizationJson');
        const previewFrame = document.getElementById('previewFrame');

        const updateHidden = () => {
            customizationJson.value = JSON.stringify(customization);
        };

        const renderSections = () => {
            sectionsList.innerHTML = '';
            customization.sections.forEach((section, index) => {
                const item = document.createElement('div');
                item.className = `section-item ${index === activeIndex ? 'active' : ''}`;
                item.draggable = true;
                item.dataset.index = index;
                item.innerHTML = `<div><span class="handle">⇅</span> ${sectionCatalog[section.type]?.label || section.type}</div><button class="btn btn-sm btn-outline-danger" data-remove="${index}">حذف</button>`;
                sectionsList.appendChild(item);
            });
        };

        const renderSettings = () => {
            const section = customization.sections[activeIndex];
            if (!section) {
                settingsPanel.textContent = 'اختر قسمًا لعرض الإعدادات.';
                return;
            }
            const schema = sectionCatalog[section.type];
            if (!schema) {
                settingsPanel.textContent = 'القسم غير مدعوم.';
                return;
            }
            const settings = section.settings || {};
            settingsPanel.innerHTML = '';
            schema.settings.forEach((field) => {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = `<label class="form-label">${field.label}</label><input class="form-control" data-setting="${field.key}" value="${settings[field.key] || ''}">`;
                settingsPanel.appendChild(wrapper);
            });

            if (schema.blocks) {
                const blocksWrapper = document.createElement('div');
                blocksWrapper.className = 'blocks-list';
                blocksWrapper.innerHTML = '<label class="form-label mt-2">الكتل</label>';
                const blocks = Array.isArray(section.blocks) ? section.blocks : [];
                blocks.forEach((block, blockIndex) => {
                    const card = document.createElement('div');
                    card.className = 'block-card';
                    card.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>عنصر ${blockIndex + 1}</strong>
                            <button class="btn btn-sm btn-outline-danger" data-block-remove="${blockIndex}">حذف</button>
                        </div>
                        <div class="settings-grid">
                            <input class="form-control" data-block-setting="title" data-block-index="${blockIndex}" placeholder="اسم المنتج" value="${block.settings?.title || ''}">
                            <input class="form-control" data-block-setting="price" data-block-index="${blockIndex}" placeholder="السعر" value="${block.settings?.price || ''}">
                            <input class="form-control" data-block-setting="image_url" data-block-index="${blockIndex}" placeholder="رابط الصورة" value="${block.settings?.image_url || ''}">
                        </div>`;
                    blocksWrapper.appendChild(card);
                });
                const addBlock = document.createElement('button');
                addBlock.type = 'button';
                addBlock.className = 'btn btn-outline-light btn-sm mt-2';
                addBlock.textContent = 'إضافة عنصر';
                addBlock.addEventListener('click', () => {
                    if (!Array.isArray(section.blocks)) {
                        section.blocks = [];
                    }
                    section.blocks.push({ type: 'product', settings: { title: '', price: '', image_url: '' } });
                    renderSettings();
                    updateHidden();
                });
                blocksWrapper.appendChild(addBlock);
                settingsPanel.appendChild(blocksWrapper);
            }
        };

        const selectSection = (index) => {
            activeIndex = index;
            renderSections();
            renderSettings();
        };

        document.getElementById('btnAddSection').addEventListener('click', () => {
            const type = document.getElementById('sectionType').value;
            customization.sections.push({ type, settings: {}, blocks: [] });
            activeIndex = customization.sections.length - 1;
            renderSections();
            renderSettings();
            updateHidden();
        });

        sectionsList.addEventListener('click', (event) => {
            const removeIndex = event.target.dataset.remove;
            if (removeIndex !== undefined) {
                customization.sections.splice(Number(removeIndex), 1);
                activeIndex = Math.max(0, activeIndex - 1);
                renderSections();
                renderSettings();
                updateHidden();
                return;
            }
            const sectionItem = event.target.closest('.section-item');
            if (!sectionItem) return;
            selectSection(Number(sectionItem.dataset.index));
        });

        sectionsList.addEventListener('dragover', (event) => {
            event.preventDefault();
            const dragging = document.querySelector('.section-item.dragging');
            const target = event.target.closest('.section-item');
            if (!target || target === dragging) return;
            const draggingIndex = Number(dragging?.dataset.index);
            const targetIndex = Number(target.dataset.index);
            if (Number.isNaN(draggingIndex) || Number.isNaN(targetIndex)) return;
            const [moved] = customization.sections.splice(draggingIndex, 1);
            customization.sections.splice(targetIndex, 0, moved);
            activeIndex = targetIndex;
            renderSections();
            renderSettings();
            updateHidden();
        });

        sectionsList.addEventListener('dragend', () => {
            renderSections();
            updateHidden();
        });

        sectionsList.addEventListener('dragstart', (event) => {
            const item = event.target.closest('.section-item');
            if (item) {
                item.classList.add('dragging');
                event.dataTransfer.effectAllowed = 'move';
            }
        });

        sectionsList.addEventListener('dragend', (event) => {
            const item = event.target.closest('.section-item');
            if (item) {
                item.classList.remove('dragging');
            }
        });

        settingsPanel.addEventListener('input', (event) => {
            const section = customization.sections[activeIndex];
            if (!section) return;
            if (event.target.dataset.setting) {
                section.settings = section.settings || {};
                section.settings[event.target.dataset.setting] = event.target.value;
            }
            if (event.target.dataset.blockSetting) {
                const blockIndex = Number(event.target.dataset.blockIndex);
                const blocks = section.blocks || [];
                const block = blocks[blockIndex];
                if (block) {
                    block.settings = block.settings || {};
                    block.settings[event.target.dataset.blockSetting] = event.target.value;
                }
            }
            updateHidden();
        });

        settingsPanel.addEventListener('click', (event) => {
            const removeIndex = event.target.dataset.blockRemove;
            if (removeIndex === undefined) return;
            const section = customization.sections[activeIndex];
            if (!section || !Array.isArray(section.blocks)) return;
            section.blocks.splice(Number(removeIndex), 1);
            renderSettings();
            updateHidden();
        });

        document.getElementById('btnSaveDraft').addEventListener('click', () => {
            document.getElementById('customizerAction').value = 'draft';
            updateHidden();
            document.getElementById('customizerForm').submit();
        });

        document.getElementById('btnPublish').addEventListener('click', () => {
            document.getElementById('customizerAction').value = 'publish';
            updateHidden();
            document.getElementById('customizerForm').submit();
        });

        document.getElementById('btnPreviewRefresh').addEventListener('click', () => {
            updateHidden();
            const formData = new FormData(document.getElementById('customizerForm'));
            fetch('/admin/customizer/save', { method: 'POST', body: formData })
                .then(() => {
                    previewFrame.src = `/admin/customizer/preview?page_id=<?= (int) $page['id'] ?>&t=${Date.now()}`;
                });
        });

        renderSections();
        renderSettings();
        updateHidden();
    </script>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
