{{-- resources/views/tools/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">{{ __('tools.title') }}</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('tools.filename_validator.title') }}</h5>
            </div>
            <div class="card-body">
                <form id="filenameValidatorForm">
                    <div class="mb-3">
                        <label for="filename" class="form-label">{{ __('tools.filename_validator.input_label') }}</label>
                        <input type="text" class="form-control" id="filename" name="filename" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('tools.filename_validator.validate') }}</button>
                </form>
                <div id="validationResult" class="mt-3 d-none">
                    <h6>{{ __('tools.filename_validator.result') }}:</h6>
                    <pre class="json-result p-3 rounded"></pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filenameValidatorForm');
    const result = document.getElementById('validationResult');
    const resultPre = result.querySelector('pre');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        try {
            const response = await fetch('{{ route('tools.validate-filename') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    filename: document.getElementById('filename').value
                })
            });

            const data = await response.json();
            resultPre.textContent = JSON.stringify(data, null, 2);
            result.classList.remove('d-none');

        } catch (error) {
            console.error('Error:', error);
        }
    });
});
</script>
@endpush
