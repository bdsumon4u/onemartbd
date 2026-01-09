@foreach ($child_categories as $child_category)
    <li>
        <a href="{{ route('single.category', $child_category->id) }}">{{ $child_category->category_name }}</a>
        @if (count($child_category->children) > 0)
            <span class="caret-right"></span>
        @endif
        @if (count($child_category->children) > 0)
            <div class="child_category">
                <ul>
                    @include('frontEnd.inc.child_category', [
                        'child_categories' => $child_category->children,
                    ])
                </ul>
            </div>
        @endif
    </li>
@endforeach
