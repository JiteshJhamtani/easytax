{{-- Shared form builder partial used by both create and edit views --}}

{{-- Hidden input that holds the JSON form schema --}}
<input type="hidden" name="form_schema" id="formSchemaInput">

<div class="card modern-card border-0 shadow-sm mt-4">
    <div class="card-header bg-white pt-4 pb-3 border-bottom-0 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-dark mb-0">
            <i class="fas fa-wpforms text-primary mr-2"></i> Application Form Builder
        </h3>
        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold" id="addSectionBtn">
            <i class="fas fa-plus mr-1"></i> Add Section
        </button>
    </div>

    <div class="card-body pt-2" id="formBuilderContainer">
        {{-- Sections will be rendered here by JS --}}
    </div>
</div>

{{-- Documents Section --}}
<div class="card modern-card border-0 shadow-sm mt-4">
    <div class="card-header bg-white pt-4 pb-3 border-bottom-0 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-dark mb-0">
            <i class="fas fa-paperclip text-primary mr-2"></i> Required Documents
        </h3>
        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold" id="addDocumentBtn">
            <i class="fas fa-plus mr-1"></i> Add Document
        </button>
    </div>

    <div class="card-body pt-2" id="documentsContainer">
        {{-- Document rows will be rendered here by JS --}}
    </div>
</div>

@push('js')
<script>
    const FIELD_TYPES = ['text', 'email', 'number', 'date', 'password', 'textarea', 'select'];

    let formSchema = {
        sections: {},
        documents: []
    };

    // Load existing schema if editing
    @if(isset($formConfig) && $formConfig)
        formSchema = @json($formConfig);
        if (!formSchema.sections) formSchema.sections = {};
        if (!formSchema.documents) formSchema.documents = [];
    @endif

    let sectionCounter = Object.keys(formSchema.sections).length;
    let documentCounter = formSchema.documents ? formSchema.documents.length : 0;

    // ─── Render All ───
    function renderAll() {
        renderSections();
        renderDocuments();
        updateHiddenInput();
    }

    // ─── Sections ───
    function renderSections() {
        const container = document.getElementById('formBuilderContainer');
        container.innerHTML = '';

        const sectionKeys = Object.keys(formSchema.sections);

        if (sectionKeys.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0"><i class="fas fa-info-circle mr-1"></i> No sections added yet. Click "Add Section" to start building the form.</p>';
            return;
        }

        sectionKeys.forEach((key, index) => {
            const section = formSchema.sections[key];
            container.innerHTML += buildSectionHtml(key, section, index);
        });
    }

    function buildSectionHtml(key, section, index) {
        let fieldsHtml = '';

        if (section.fields && section.fields.length > 0) {
            section.fields.forEach((field, fIndex) => {
                fieldsHtml += buildFieldRow(key, field, fIndex);
            });
        }

        return `
            <div class="section-block border rounded p-3 mb-3" data-section-key="${key}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center flex-grow-1">
                        <i class="fas fa-grip-vertical text-muted mr-2" style="cursor: grab;"></i>
                        <input type="text" class="form-control form-control-sm custom-input font-weight-bold section-label-input"
                               value="${escapeHtml(section.label)}"
                               placeholder="Section Label"
                               data-section-key="${key}"
                               style="max-width: 350px;">
                        <input type="text" class="form-control form-control-sm custom-input ml-2 section-key-input text-muted"
                               value="${escapeHtml(key)}"
                               placeholder="section_key"
                               data-section-key="${key}"
                               style="max-width: 200px; font-family: monospace; font-size: 0.85rem;">
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success add-field-btn" data-section-key="${key}">
                            <i class="fas fa-plus mr-1"></i> Field
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-section-btn" data-section-key="${key}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="fields-container" data-section-key="${key}">
                    ${fieldsHtml || '<p class="text-muted small mb-0 ml-4">No fields in this section.</p>'}
                </div>
            </div>
        `;
    }

    function buildFieldRow(sectionKey, field, fIndex) {
        let typeOptions = FIELD_TYPES.map(t =>
            `<option value="${t}" ${field.type === t ? 'selected' : ''}>${t}</option>`
        ).join('');

        let optionsDisplay = field.type === 'select' ? '' : 'display: none;';
        let optionsValue = field.options ? Object.entries(field.options).map(([k, v]) => `${k}:${v}`).join(', ') : '';

        return `
            <div class="field-row d-flex align-items-start mb-2 p-2 bg-light rounded" data-section="${sectionKey}" data-index="${fIndex}">
                <div class="row flex-grow-1 mx-0">
                    <div class="col-md-3 px-1">
                        <input type="text" class="form-control form-control-sm field-name"
                               value="${escapeHtml(field.name)}" placeholder="field_name"
                               style="font-family: monospace; font-size: 0.85rem;">
                    </div>
                    <div class="col-md-3 px-1">
                        <input type="text" class="form-control form-control-sm field-label"
                               value="${escapeHtml(field.label)}" placeholder="Field Label">
                    </div>
                    <div class="col-md-2 px-1">
                        <select class="form-control form-control-sm field-type">${typeOptions}</select>
                    </div>
                    <div class="col-md-2 px-1">
                        <input type="text" class="form-control form-control-sm field-validation"
                               value="${escapeHtml(field.validation || '')}" placeholder="Validation rules">
                    </div>
                    <div class="col-md-1 px-1 text-center pt-1">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input field-required" id="req_${sectionKey}_${fIndex}"
                                   ${field.required ? 'checked' : ''}>
                            <label class="custom-control-label" for="req_${sectionKey}_${fIndex}">Req</label>
                        </div>
                    </div>
                    <div class="col-md-1 px-1 text-right">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-field-btn"
                                data-section="${sectionKey}" data-index="${fIndex}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="col-12 px-1 mt-1 field-options-row" style="${optionsDisplay}">
                        <input type="text" class="form-control form-control-sm field-options"
                               value="${escapeHtml(optionsValue)}" placeholder="Options: key1:Label 1, key2:Label 2">
                    </div>
                </div>
            </div>
        `;
    }

    // ─── Documents ───
    function renderDocuments() {
        const container = document.getElementById('documentsContainer');
        container.innerHTML = '';

        if (!formSchema.documents || formSchema.documents.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0"><i class="fas fa-info-circle mr-1"></i> No documents configured. Click "Add Document" to add requirements.</p>';
            return;
        }

        formSchema.documents.forEach((doc, index) => {
            container.innerHTML += buildDocumentRow(doc, index);
        });
    }

    function buildDocumentRow(doc, index) {
        let mimesValue = (doc.mimes || []).join(', ');

        return `
            <div class="doc-row d-flex align-items-center mb-2 p-2 bg-light rounded" data-index="${index}">
                <div class="row flex-grow-1 mx-0">
                    <div class="col-md-3 px-1">
                        <input type="text" class="form-control form-control-sm doc-name"
                               value="${escapeHtml(doc.name)}" placeholder="document_key"
                               style="font-family: monospace; font-size: 0.85rem;">
                    </div>
                    <div class="col-md-4 px-1">
                        <input type="text" class="form-control form-control-sm doc-label"
                               value="${escapeHtml(doc.label)}" placeholder="Document Label">
                    </div>
                    <div class="col-md-2 px-1">
                        <input type="text" class="form-control form-control-sm doc-mimes"
                               value="${escapeHtml(mimesValue)}" placeholder="pdf, jpg, png">
                    </div>
                    <div class="col-md-2 px-1 pt-1">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input doc-required" id="docReq_${index}"
                                   ${doc.required ? 'checked' : ''}>
                            <label class="custom-control-label" for="docReq_${index}">Required</label>
                        </div>
                    </div>
                    <div class="col-md-1 px-1 text-right">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-doc-btn" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // ─── Event Handlers ───
    document.getElementById('addSectionBtn').addEventListener('click', function() {
        sectionCounter++;
        let key = 'section_' + sectionCounter;
        formSchema.sections[key] = { label: 'New Section', fields: [] };
        renderAll();
    });

    document.getElementById('addDocumentBtn').addEventListener('click', function() {
        if (!formSchema.documents) formSchema.documents = [];
        formSchema.documents.push({ name: '', label: '', required: false, mimes: ['pdf', 'jpg', 'png'] });
        renderAll();
    });

    // Delegate events
    document.addEventListener('click', function(e) {
        // Add field
        if (e.target.closest('.add-field-btn')) {
            let sectionKey = e.target.closest('.add-field-btn').dataset.sectionKey;
            collectCurrentState();
            formSchema.sections[sectionKey].fields.push({
                name: '', label: '', type: 'text', required: false, validation: '', options: {}
            });
            renderAll();
        }

        // Remove field
        if (e.target.closest('.remove-field-btn')) {
            let btn = e.target.closest('.remove-field-btn');
            let sectionKey = btn.dataset.section;
            let index = parseInt(btn.dataset.index);
            collectCurrentState();
            formSchema.sections[sectionKey].fields.splice(index, 1);
            renderAll();
        }

        // Remove section
        if (e.target.closest('.remove-section-btn')) {
            let sectionKey = e.target.closest('.remove-section-btn').dataset.sectionKey;
            collectCurrentState();
            delete formSchema.sections[sectionKey];
            renderAll();
        }

        // Remove document
        if (e.target.closest('.remove-doc-btn')) {
            let index = parseInt(e.target.closest('.remove-doc-btn').dataset.index);
            collectCurrentState();
            formSchema.documents.splice(index, 1);
            renderAll();
        }
    });

    // Show/hide options row when type changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('field-type')) {
            let row = e.target.closest('.field-row');
            let optionsRow = row.querySelector('.field-options-row');
            optionsRow.style.display = e.target.value === 'select' ? '' : 'none';
        }
    });

    // ─── Collect State ───
    function collectCurrentState() {
        // Collect sections
        document.querySelectorAll('.section-block').forEach(block => {
            let oldKey = block.dataset.sectionKey;
            let newKey = block.querySelector('.section-key-input').value.trim() || oldKey;
            let label = block.querySelector('.section-label-input').value.trim();

            let fields = [];
            block.querySelectorAll('.field-row').forEach(row => {
                let field = {
                    name: row.querySelector('.field-name').value.trim(),
                    label: row.querySelector('.field-label').value.trim(),
                    type: row.querySelector('.field-type').value,
                    required: row.querySelector('.field-required').checked,
                    validation: row.querySelector('.field-validation').value.trim(),
                };

                if (field.type === 'select') {
                    field.options = parseOptions(row.querySelector('.field-options').value);
                }

                fields.push(field);
            });

            // If key changed, remove old and add new
            if (newKey !== oldKey) {
                delete formSchema.sections[oldKey];
            }
            formSchema.sections[newKey] = { label: label, fields: fields };
        });

        // Collect documents
        formSchema.documents = [];
        document.querySelectorAll('.doc-row').forEach(row => {
            formSchema.documents.push({
                name: row.querySelector('.doc-name').value.trim(),
                label: row.querySelector('.doc-label').value.trim(),
                required: row.querySelector('.doc-required').checked,
                mimes: row.querySelector('.doc-mimes').value.split(',').map(s => s.trim()).filter(s => s),
            });
        });

        updateHiddenInput();
    }

    function parseOptions(str) {
        let options = {};

        if (!str) return options;

        str.split(',').forEach(pair => {
            let parts = pair.split(':');
            if (parts.length >= 2) {
                let key = parts[0].trim();
                let val = parts.slice(1).join(':').trim();
                if (key) options[key] = val;
            }
        });

        return options;
    }

    function updateHiddenInput() {
        // Only include label in schema (not the internal 'label' key config uses)
        let schema = {
            label: '', // Will be set from service name
            sections: formSchema.sections,
            documents: formSchema.documents || []
        };

        document.getElementById('formSchemaInput').value = JSON.stringify(schema);
    }

    function escapeHtml(str) {
        if (!str) return '';
        let div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    // Collect state before form submission
    document.addEventListener('submit', function(e) {
        if (e.target.tagName === 'FORM') {
            collectCurrentState();
        }
    });

    // Initial render
    renderAll();
</script>
@endpush

@push('css')
<style>
    .section-block {
        border-color: #e2e8f0 !important;
        background: #fefefe;
    }

    .section-block:hover {
        border-color: #cbd5e1 !important;
    }

    .field-row {
        transition: background 0.15s;
    }

    .field-row:hover {
        background-color: #f1f5f9 !important;
    }

    .doc-row:hover {
        background-color: #f1f5f9 !important;
    }
</style>
@endpush
