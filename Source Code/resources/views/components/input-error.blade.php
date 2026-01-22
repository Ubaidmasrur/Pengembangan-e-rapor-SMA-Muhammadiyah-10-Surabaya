@props(['message' => null])

@if ($message)
    <span class="text-red-600 text-sm mt-1 flex items-center" role="alert">
        <i class="fas fa-circle-info"></i>
        {{ $message }}
    </span>
@endif
