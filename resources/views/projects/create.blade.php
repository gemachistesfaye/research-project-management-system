@extends('layouts.app')

@section('content')
<style>.upload-zone{transition:all .2s ease;}.upload-zone:hover{border-color:#212529!important;background:#f8f9fa;}</style>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center justify-content-center mb-3">
            <div class="d-flex align-items-center">
                <span class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width:30px;height:30px;font-size:0.85rem;" id="step1Icon">1</span>
                <span class="fw-bold text-success ms-2 small" id="step1Label">Project Info</span>
            </div>
            <div class="mx-2" style="width:40px;height:2px;background:#dee2e6;" id="stepLine"></div>
            <div class="d-flex align-items-center">
                <span class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width:30px;height:30px;font-size:0.85rem;" id="step2Icon">2</span>
                <span class="fw-bold text-muted ms-2 small" id="step2Label">Review & Submit</span>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1"><i class="bi bi-journal-plus me-2 text-success"></i>New Research Proposal</h5>
                <p class="text-muted small mb-3">Proposals >= 500,000 ETB route to RCSC & VP approval.</p>

                <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" id="createForm" novalidate>
                    @csrf

                    <div id="step1">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Research Project Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror" required placeholder="Full academic title" value="{{ old('title') }}" id="inputTitle">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Thematic Priority Area <span class="text-danger">*</span></label>
                            <select name="thematic_id" class="form-select form-select-sm @error('thematic_id') is-invalid @enderror" required id="inputThematic">
                                <option value="">-- Select --</option>
                                @foreach($thematicAreas as $t)
                                    <option value="{{ $t->id }}" {{ old('thematic_id') == $t->id ? 'selected' : '' }}>{{ $t->title }} ({{ $t->category }})</option>
                                @endforeach
                            </select>
                            @error('thematic_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Requested Budget (ETB) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="requested_budget" class="form-control form-control-sm @error('requested_budget') is-invalid @enderror" required placeholder="e.g. 450000" value="{{ old('requested_budget') }}" id="budgetInput">
                            @error('requested_budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div id="budgetAlert" class="alert alert-warning small py-1 mb-0 mt-1 d-none"><i class="bi bi-exclamation-triangle me-1"></i>RCSC & VP approval required (>= 500k ETB)</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Technical Abstract <span class="text-danger">*</span></label>
                            <textarea name="abstract_text" rows="4" maxlength="5000" class="form-control form-control-sm @error('abstract_text') is-invalid @enderror" required placeholder="Executive summary, objectives, methodology, expected outcomes..." id="abstractInput">{{ old('abstract_text') }}</textarea>
                            <small class="text-muted"><span id="charCount">0</span>/5000</small>
                            @error('abstract_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Proposal Document (PDF) <span class="text-danger">*</span></label>
                            <div class="upload-zone border border-2 border-dashed rounded-3 p-3 text-center" id="uploadZone" style="cursor:pointer;border-color:#dee2e6;">
                                <i class="bi bi-cloud-arrow-up fs-3 text-muted d-block mb-1"></i>
                                <p class="mb-0 small fw-bold text-dark">Drag & drop or click to browse</p>
                                <input type="file" name="proposal_document" accept=".pdf,.doc,.docx" class="d-none" id="fileInput">
                                <div id="fileName" class="text-success fw-bold small d-none mt-1"><i class="bi bi-check-circle me-1"></i><span></span></div>
                            </div>
                            <small class="text-muted">PDF, DOC, or DOCX, max 10MB</small>
                            <div id="fileError" class="alert alert-danger small py-1 mb-0 mt-1 d-none"><i class="bi bi-exclamation-triangle me-1"></i>Please select a proposal document (PDF, DOC, or DOCX).</div>
                            @error('proposal_document')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
                            <button type="button" class="btn btn-sm btn-dark fw-bold" onclick="showStep2()">Next <i class="bi bi-arrow-right ms-1"></i></button>
                        </div>
                    </div>

                    <div id="step2" class="d-none">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-eye me-2"></i>Review Your Proposal</h6>
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <div class="row">
                                <div class="col-md-6 mb-2"><small class="text-muted d-block">Title</small><span class="fw-bold small" id="previewTitle">-</span></div>
                                <div class="col-md-6 mb-2"><small class="text-muted d-block">Thematic Area</small><span class="fw-bold small" id="previewThematic">-</span></div>
                                <div class="col-md-6 mb-2"><small class="text-muted d-block">Budget</small><span class="fw-bold small" id="previewBudget">-</span></div>
                                <div class="col-md-6 mb-2"><small class="text-muted d-block">Document</small><span class="fw-bold small text-success" id="previewFile">-</span></div>
                                <div class="col-12 mb-2"><small class="text-muted d-block">Abstract</small><span class="small" id="previewAbstract" style="white-space:pre-wrap;">-</span></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showStep1()"><i class="bi bi-arrow-left me-1"></i>Back</button>
                            <div class="d-flex gap-2">
                                <button type="submit" formaction="{{ route('projects.store-draft') }}" class="btn btn-sm btn-outline-dark fw-bold confirm-btn" data-confirm-title="Save as Draft" data-confirm-message="Save this proposal as a draft? You can edit and submit it later." data-confirm-icon="bi-save" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Save Draft" data-confirm-btn-class="btn-dark"><i class="bi bi-save me-1"></i>Save Draft</button>
                                <button type="submit" class="btn btn-sm btn-dark fw-bold confirm-btn" data-confirm-title="Submit Proposal" data-confirm-message="This proposal will be sent to the Department Head for initial screening. You won't be able to edit it after submission." data-confirm-icon="bi-send" data-confirm-color="text-success" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-success"><i class="bi bi-send me-1"></i>Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showStep2() {
    var title = document.getElementById('inputTitle');
    var thematic = document.getElementById('inputThematic');
    var budget = document.getElementById('budgetInput');
    var abstract = document.getElementById('abstractInput');
    var file = document.getElementById('fileInput');

    if (!title.value.trim()) { title.focus(); title.reportValidity(); return; }
    if (!thematic.value) { thematic.focus(); thematic.reportValidity(); return; }
    if (!budget.value || parseFloat(budget.value) <= 0) { budget.focus(); budget.reportValidity(); return; }
    if (!abstract.value.trim()) { abstract.focus(); abstract.reportValidity(); return; }
    if (!file.files.length) { document.getElementById('fileError').classList.remove('d-none'); document.getElementById('uploadZone').style.borderColor='#dc3545'; setTimeout(function(){document.getElementById('uploadZone').style.borderColor='#dee2e6';},2000); return; }

    document.getElementById('step1').classList.add('d-none');
    document.getElementById('step2').classList.remove('d-none');
    document.getElementById('step1Icon').classList.replace('bg-success','bg-secondary');
    document.getElementById('step1Label').classList.replace('text-success','text-muted');
    document.getElementById('step2Icon').classList.replace('bg-secondary','bg-success');
    document.getElementById('step2Label').classList.replace('text-muted','text-success');
    document.getElementById('stepLine').style.background = '#198754';
    document.getElementById('previewTitle').textContent = document.getElementById('inputTitle').value || '-';
    var sel = document.getElementById('inputThematic');
    document.getElementById('previewThematic').textContent = sel.options[sel.selectedIndex]?.text || '-';
    document.getElementById('previewBudget').textContent = (parseFloat(document.getElementById('budgetInput').value)||0).toLocaleString() + ' ETB';
    document.getElementById('previewAbstract').textContent = document.getElementById('abstractInput').value || '-';
    var fi = document.getElementById('fileInput');
    document.getElementById('previewFile').textContent = fi.files.length ? fi.files[0].name : 'No file';
}
function showStep1() {
    document.getElementById('step2').classList.add('d-none');
    document.getElementById('step1').classList.remove('d-none');
    document.getElementById('step1Icon').classList.replace('bg-secondary','bg-success');
    document.getElementById('step1Label').classList.replace('text-muted','text-success');
    document.getElementById('step2Icon').classList.replace('bg-success','bg-secondary');
    document.getElementById('step2Label').classList.replace('text-success','text-muted');
    document.getElementById('stepLine').style.background = '#dee2e6';
}

var ai = document.getElementById('abstractInput');
var cc = document.getElementById('charCount');
if(ai&&cc){cc.textContent=ai.value.length;ai.addEventListener('input',function(){cc.textContent=this.value.length;});}
var bi = document.getElementById('budgetInput');
var ba = document.getElementById('budgetAlert');
if(bi&&ba){bi.addEventListener('input',function(){ba.classList.toggle('d-none',parseFloat(this.value)<500000);});}
var uz = document.getElementById('uploadZone');
var fi = document.getElementById('fileInput');
var fn = document.getElementById('fileName');
if(uz&&fi){
    uz.addEventListener('click',function(){fi.click();});
    uz.addEventListener('dragover',function(e){e.preventDefault();this.style.borderColor='#212529';});
    uz.addEventListener('dragleave',function(e){e.preventDefault();this.style.borderColor='#dee2e6';});
    uz.addEventListener('drop',function(e){e.preventDefault();this.style.borderColor='#dee2e6';if(e.dataTransfer.files.length){fi.files=e.dataTransfer.files;showFN(e.dataTransfer.files[0]);}});
    fi.addEventListener('change',function(){if(this.files.length)showFN(this.files[0]);});
    function showFN(f){var ext=f.name.split('.').pop().toLowerCase();if(['pdf','doc','docx'].indexOf(ext)!==-1){fn.classList.remove('d-none');fn.querySelector('span').textContent=f.name;}else{alert('PDF, DOC, or DOCX only');fi.value='';}}
}
</script>
@endsection
