@extends('layouts.app')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')

@section('content')
    <div class="card" style="max-width:700px">
        <div class="card-header">
            <div class="card-title">Edit Category</div>
        </div>

        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label for="name">Category Name *</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $category->name) }}" placeholder="e.g. Grocery, Salary, Petrol" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="type">Type *</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="">-- Select Type --</option>
                        @foreach(\App\Http\Controllers\CategoryController::TYPES as $value => $label)
                            <option value="{{ $value }}" {{ (old('type', $category->type) === $value) ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        @foreach(\App\Http\Controllers\CategoryController::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ (old('status', $category->status) === $value) ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="parent_id">Parent Category</label>
                <select name="parent_id" id="parent_id" class="form-control">
                    <option value="">-- None (Top-level category) --</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ (string)old('parent_id', $category->parent_id) === (string)$parent->id ? 'selected' : '' }}>
                            {{ $parent->icon ? $parent->icon . ' ' : '' }}{{ $parent->name }}
                        </option>
                        @foreach($parent->childrenRecursive as $child)
                            <option value="{{ $child->id }}" {{ (string)old('parent_id', $category->parent_id) === (string)$child->id ? 'selected' : '' }}>&nbsp;&nbsp;└─ {{ $child->name }}</option>
                        @endforeach
                    @endforeach
                </select>
                <div style="font-size:12px;color:#94a3b8;margin-top:6px">Leave empty to make this a top-level category.</div>
                @error('parent_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="icon">Icon (emoji)</label>
                    <input type="text" name="icon" id="icon" class="form-control" value="{{ old('icon', $category->icon) }}" placeholder="🏷️">
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="color">Color</label>
                    <input type="color" name="color" id="color" class="form-control" value="{{ old('color', $category->color) }}">
                    @error('color')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
