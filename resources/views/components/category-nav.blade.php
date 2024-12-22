<ul class="mb-2 navbar-nav me-auto mb-lg-0">
    <li class="nav-item dropdown kategori-dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            KATEGORI
        </a>
        <div class="dropdown-menu">
            @foreach($categories as $chunk)
            <ul>
                @foreach($chunk as $category)
                <li>
                    <a class="dropdown-item" href="#">
                        {{ $category->title }}
                    </a>
                </li>
                @endforeach
            </ul>
            @endforeach
        </div>
    </li>
</ul>
