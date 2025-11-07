@php
    $breadcrumbs = [];
    $currentRoute = request()->route()->getName();
    
    // Define breadcrumb mappings
    $breadcrumbMap = [
        'admin.dashboard' => [
            ['name' => 'Dashboard', 'url' => null]
        ],
        'admin.users.index' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Users', 'url' => null]
        ],
        'admin.users.show' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Users', 'url' => route('admin.users.index')],
            ['name' => 'User Details', 'url' => null]
        ],
        'admin.users.edit' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Users', 'url' => route('admin.users.index')],
            ['name' => 'Edit User', 'url' => null]
        ],
    ];
    
    $breadcrumbs = $breadcrumbMap[$currentRoute] ?? [['name' => 'Dashboard', 'url' => null]];
@endphp

<div class="breadcrumb-container">
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                @foreach($breadcrumbs as $index => $breadcrumb)
                    @if($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $breadcrumb['name'] }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}">
                                {{ $breadcrumb['name'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
        
        <!-- Page Actions -->
        <div class="page-actions">
            @yield('page-actions')
        </div>
    </div>
</div>

