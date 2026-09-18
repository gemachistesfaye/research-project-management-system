@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Research Proposal</h5>
                <p class="text-muted small mb-3">Update your draft or withdrawn proposal.</p>

                <form method="POST" action="{{ route('projects.update', $project->project_id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Research Project Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror" required placeholder="Full academic title" value="{{ old('title', $project->title) }}">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Thematic Priority Area <span class="text-danger">*</span></label>
                        <select name="thematic_id" class="form-select form-select-sm @error('thematic_id') is-invalid @enderror" required>
                            <option value="">-- Select --</option>
                            @foreach($thematicAreas as $t)
                                <option value="{{ $t->id }}" {{ old('thematic_id', $project->thematic_id) == $t->id ? 'selected' : '' }}>{{ $t->title }} ({{ $t->category }})</option>
                            @endforeach
                        </select>
                        @error('thematic_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Requested Budget (ETB) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="requested_budget" class="form-control form-control-sm @error('requested_budget') is-invalid @enderror" required placeholder="e.g. 450000" value="{{ old('requested_budget', $project->requested_budget) }}" id="budgetInput">
                        @error('requested_budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="budgetAlert" class="alert alert-warning small py-1 mb-0 mt-1 d-none"><i class="bi bi-exclamation-triangle me-1"></i>RCSC & VP approval required (>= 500k ETB)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Technical Abstract <span class="text-danger">*</span></label>
                        <textarea name="abstract_text" rows="4" maxlength="5000" class="form-control form-control-sm @error('abstract_text') is-invalid @enderror" required placeholder="Executive summary, objectives, methodology..." id="abstractInput">{{ old('abstract_text', $project->abstract_text) }}</textarea>
                        <small class="text-muted"><span id="charCount">0</span>/5000</small>
                        @error('abstract_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        @if($project->proposal_document_url)
                            <div class="alert alert-info small py-1 mb-2"><i class="bi bi-check-circle me-1"></i>Current document uploaded.</div>
                        @endif
                        <label class="form-label fw-bold small">Proposal Document (PDF) <span class="text-muted fw-normal">— Leave empty to keep current</span></label>
                        <div class="upload-zone border border-2 border-dashed rounded-3 p-3 text-center" id="uploadZone" style="cursor:pointer;border-color:#dee2e6;">
                            <i class="bi bi-cloud-arrow-up fs-3 text-muted d-block mb-1"></i>
                            <p class="mb-0 small fw-bold text-dark">Drag & drop or click to browse</p>
                            <input type="file" name="proposal_document" accept=".pdf,.doc,.docx" class="d-none" id="fileInput">
                            <div id="fileName" class="text-success fw-bold small d-none mt-1"><i class="bi bi-check-circle me-1"></i><span></span></div>
                        </div>
                        <small class="text-muted">PDF, DOC, or DOCX, max 10MB</small>
                        @error('proposal_document')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.show', $project->project_id) }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-sm fw-bold" style="background:#e67700;color:#fff;border:none;" onmouseover="this.style.background='#cc6600'" onmouseout="this.style.background='#e67700'"><i class="bi bi-check-lg me-1"></i>Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ai = document.getElementById('abstractInput');
    var cc = document.getElementById('charCount');
    if(ai&&cc){cc.textContent=ai.value.length;ai.addEventListener('input',function(){cc.textContent=this.value.length;});}
    var bi = document.getElementById('budgetInput');
    var ba = document.getElementById('budgetAlert');
    if(bi&&ba){var ck=function(){ba.classList.toggle('d-none',parseFloat(bi.value)<500000);};bi.addEventListener('input',ck);ck();}
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
});
</script>
@endpush
<style>.upload-zone{transition:all .2s ease;}.upload-zone:hover{border-color:#212529!important;background:#f8f9fa;}</style>
@endsection
