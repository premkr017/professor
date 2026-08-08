@extends('layouts.app')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
    @php
        $totalExpense = $categories->where('type', 'expense')->count();
        $totalIncome = $categories->where('type', 'income')->count();
        $totalActive = $categories->where('status', 'active')->count();
    @endphp
    <div class="grid grid-3" style="margin-bottom:24px">
        <div class="stat-card">
            <div class="stat-icon red">🛒</div>
            <div>
                <div class="stat-label">Expense Categories</div>
                <div class="stat-value">{{ $totalExpense }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">💰</div>
            <div>
                <div class="stat-label">Income Categories</div>
                <div class="stat-value">{{ $totalIncome }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">✅</div>
            <div>
                <div class="stat-label">Active</div>
                <div class="stat-value">{{ $totalActive }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Your Categories</div>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">+ Add Category</a>
        </div>

        @if($tree->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tree as $category)
                            @include('admin.categories.partials.row', ['category' => $category, 'depth' => 0])
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="icon">🏷️</div>
                <p>You don't have any categories yet. Create your first category to organize your finances.</p>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">Add Category</a>
            </div>
        @endif
    </div>
@endsection
