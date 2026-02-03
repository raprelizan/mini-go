const customizerApp = document.getElementById('themeCustomizerApp');

if (customizerApp) {
    const registry = JSON.parse(customizerApp.dataset.registry || '{}');
    const initialData = JSON.parse(customizerApp.dataset.customization || '{}');
    const pageId = customizerApp.dataset.pageId;
    const sectionCatalog = registry || {};
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
        Object.keys(schema.settings || {}).forEach((key) => {
            const schemaEntry = schema.settings[key];
            const labelText = typeof schemaEntry === 'object' && schemaEntry.label ? schemaEntry.label : key;
            const wrapper = document.createElement('div');
            wrapper.innerHTML = `<label class="form-label">${labelText}</label><input class="form-control" data-setting="${key}" value="${settings[key] || ''}">`;
            settingsPanel.appendChild(wrapper);
        });

        if (schema.blocks && Object.keys(schema.blocks).length > 0) {
            const blocksWrapper = document.createElement('div');
            blocksWrapper.className = 'blocks-list';
            blocksWrapper.innerHTML = '<label class="form-label mt-2">الكتل</label>';
            const blocks = Array.isArray(section.blocks) ? section.blocks : [];
            const blockType = Object.keys(schema.blocks)[0];
            const blockSchema = schema.blocks[blockType] || {};
            blocks.forEach((block, blockIndex) => {
                const card = document.createElement('div');
                card.className = 'block-card';
                const fields = Object.keys(blockSchema).map((key) => {
                    const entry = blockSchema[key];
                    const labelText = typeof entry === 'object' && entry.label ? entry.label : key;
                    return `<input class="form-control" data-block-setting="${key}" data-block-index="${blockIndex}" placeholder="${labelText}" value="${block.settings?.[key] || ''}">`;
                }).join('');
                card.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>عنصر ${blockIndex + 1}</strong>
                        <button class="btn btn-sm btn-outline-danger" data-block-remove="${blockIndex}">حذف</button>
                    </div>
                    <div class="settings-grid">
                        ${fields}
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
                const settings = {};
                Object.keys(blockSchema).forEach((key) => {
                    settings[key] = '';
                });
                section.blocks.push({ type: blockType, settings });
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
        updateHidden();
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
                previewFrame.src = `/admin/customizer/preview?page_id=${pageId}&t=${Date.now()}`;
            });
    });

    renderSections();
    renderSettings();
    updateHidden();
}
