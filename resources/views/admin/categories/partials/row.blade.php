@php
    $hasChildren = $category->childrenRecursive->isNotEmpty();
    $isParent = $depth === 0;
@endphp
<tr>
    <td>
        <div style="display:flex;align-items:center;gap:10px">
            <span style="width:{{ $depth * 24 }}px;flex-shrink:0"></span>
            @if($hasChildren)
                <button type="button" class="tree-toggle" data-target="#cat-{{ $category->id }}"
                    style="background:none;border:none;cursor:pointer;font-size:13px;color:#64748b">▾</button>
            @else
                <span style="width:14px;display:inline-block"></span>
            @endif
            <span class="stat-icon" style="width:36px;height:36px;font-size:16px;background:{{ $category->color }}22;color:{{ $category->color }}">
                {{ $category->icon ?? '🏷️' }}
            </span>
            <div>
                <div style="font-weight:600;color:#0f172a">
                    {{ $category->name }}
                    @if($category->is_system)
                        <span class="badge badge-gray" style="margin-left:6px">System</span>
                    @endif
                </div>
                @if($category->parent)
                    <div style="font-size:12px;color:#94a3b8">{{ $category->parent->name }}</div>
                @endif
            </div>
        </div>
    </td>
    <td>
        <span class="badge {{ $category->type === 'income' ? 'badge-green' : 'badge-red' }}">
            {{ ucfirst($category->type) }}
        </span>
    </td>
    <td>
        <span class="badge {{ $category->status === 'active' ? 'badge-green' : 'badge-gray' }}">
            {{ ucfirst($category->status) }}
        </span>
    </td>
    <td>
        <div class="td-actions">
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-secondary btn-sm">Edit</a>
            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                onsubmit="return confirm('Delete this {{ $category->type }} category?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </div>
    </td>
</tr>

@if($hasChildren)
    <tbody class="tree-children" id="cat-{{ $category->id }}">
        @foreach($category->childrenRecursive as $child)
            @include('admin.categories.partials.row', ['category' => $child, 'depth' => $depth + 1])
        @endforeach
    </tbody>
@endif

@once
@push('scripts')
<script>
    document.querySelectorAll('.tree-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = document.querySelector(btn.dataset.target);
            if (target) {
                var hidden = target.style.display === 'none';
                target.style.display = hidden ? '' : 'none';
                btn.textContent = hidden ? '▾' : '▸';
            }
        });
    });
</script>
@endpush
@endonce
