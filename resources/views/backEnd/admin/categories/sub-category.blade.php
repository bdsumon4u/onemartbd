@foreach($children as $child)
    &nbsp;
    <svg style="margin-top: -5px;" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-corner-down-right" width="15" height="15" viewBox="0 0 24 24"
         stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M6 6v6a3 3 0 0 0 3 3h10l-4 -4m0 8l4 -4"></path>
    </svg> <span>{{$child->category_name??""}}</span>

    &nbsp;<a href="javascript:void(0)" style="margin-right: 2px" class="add_sub_cat_btn" data-id="{{$child->id}}" data-name="{{$child->category_name}}">
        <i class="fa fa-plus-square"></i>
    </a>
    <a href="javascript:void(0)" class="edit_cat_btn" data-toggle="modal" data-target="#edit_cat"
       data-id="{{$child->id}}" data-name="{{$child->category_name}}" data-status="{{$child->status}}"
       data-image="{{ $child->image ? asset($child->image) : asset('frontEnd/images/no_image.png') }}" data-position="{{$child->position}}">
        <i class="fa fa-edit"></i>
    </a>
    <a href="{{route('admin.category.delete',$child->id)}}"
       onclick="return confirm('Are you sure to delete this?')"><i
            class="fa fa-trash"></i></a>
    <br>
    @if(count($child->children)>0)
        <div style="padding-left: 10px">
            @include('backEnd.admin.categories.sub-category',['children'=> $child->children])
        </div>
    @endif
@endforeach
