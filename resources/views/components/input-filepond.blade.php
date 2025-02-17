@props([
    'preview' => true,
    'name',
])
@push('css')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet"/>
    @if($preview)
        <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet"/>
        <link
            href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css"
            rel="stylesheet"
        />
    @endif

@endpush

<input type="file" name="{{ $name }}" id="filepond"/>

@push('js')
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

    @if($preview)
        <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
        <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    @endif


    <script>
        @if($preview)
        FilePond.registerPlugin(FilePondPluginImagePreview);
        @endif
        const inputElement = document.getElementById('filepond');

        // Create a FilePond instance
        const pond = FilePond.create(inputElement);
        pond.setOptions({
            server: {
                process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                    const formData = new FormData();
                    formData.append('{{ $name }}', file);
                    formData.append('name', '{{ $name }}');

                    const request = new XMLHttpRequest();
                    request.open('POST', '{{ route('tmp.upload') }}');
                    request.setRequestHeader('x-csrf-token', '{{ csrf_token() }}');


                    request.upload.onprogress = (e) => {
                        progress(e.lengthComputable, e.loaded, e.total);
                    };

                    request.onload = function () {
                        if (request.status >= 200 && request.status < 300) {
                            load(request.responseText);
                        } else {
                            error('oh no');
                        }
                    };

                    request.send(formData);

                    return {
                        abort: () => {
                            request.abort();
                            abort();
                        },
                    };
                },

                revert: '{{ route('tmp.delete', $name) }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },

            },
        });


    </script>
@endpush
