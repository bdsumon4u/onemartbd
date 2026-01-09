{{-- @foreach ($child_categories as $child_category)
    <br>
    &nbsp;<svg style="margin-top: -5px;" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-corner-down-right" width="15" height="15" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M6 6v6a3 3 0 0 0 3 3h10l-4 -4m0 8l4 -4"></path>
    </svg>
    <a style="font-size: 13px" href="{{route('single.category',$child_category->id)}}">{{$child_category->category_name}}</a>
    @if (count($child_category->children) > 0)
        @include('frontEnd.inc.child_category_m',[ 'child_categories' => $child_category->children])
    @endif
@endforeach --}}

@foreach ($child_categories as $child_category)
    <li>
        <svg style="margin-top: -5px;" xmlns="http://www.w3.org/2000/svg"
            class="icon icon-tabler icon-tabler-corner-down-right" width="15" height="15" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M6 6v6a3 3 0 0 0 3 3h10l-4 -4m0 8l4 -4"></path>
        </svg>
        <a style="font-size: 13px"
            href="{{ route('single.category', $child_category->id) }}">{{ $child_category->category_name }}</a>
    </li>
    @if (count($child_category->children) > 0)
        <div class="sub-category-m" >
            <ul>
                @include('frontEnd.inc.child_category_m', [
                    'child_categories' => $child_category->children,
                ])
            </ul>
        </div>
    @endif
@endforeach
