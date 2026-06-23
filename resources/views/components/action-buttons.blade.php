<div class="flex flex-wrap gap-2">
    @if(isset($show))
        <a href="{{ $show }}" class="px-3 py-1 rounded-full bg-blue-600 text-white text-xs hover:bg-blue-700">{{ __('View') }}</a>
    @endif

    @if(isset($edit))
        <a href="{{ $edit }}" class="px-3 py-1 rounded-full bg-slate-600 text-white text-xs hover:bg-slate-700">{{ __('Edit') }}</a>
    @endif

    @if(isset($destroy))
        <form action="{{ $destroy }}" method="POST" onsubmit="return confirm({{ json_encode($deleteConfirm ?? __('Are you sure you want to delete this item?')) }});" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-3 py-1 rounded-full bg-red-600 text-white text-xs hover:bg-red-700">{{ __('Delete') }}</button>
        </form>
    @endif
</div>
