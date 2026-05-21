<div class="flex flex-wrap gap-2">
    @if(isset($show))
        <a href="{{ $show }}" class="px-3 py-1 rounded-full bg-blue-600 text-white text-xs hover:bg-blue-700">Bekijken</a>
    @endif

    @if(isset($edit))
        <a href="{{ $edit }}" class="px-3 py-1 rounded-full bg-slate-600 text-white text-xs hover:bg-slate-700">Bewerken</a>
    @endif

    @if(isset($destroy))
        <form action="{{ $destroy }}" method="POST" onsubmit="return confirm({{ json_encode($deleteConfirm ?? 'Weet je zeker dat je dit wilt verwijderen?') }});" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-3 py-1 rounded-full bg-red-600 text-white text-xs hover:bg-red-700">Verwijderen</button>
        </form>
    @endif
</div>
