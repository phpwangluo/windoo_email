<div class="form-group {!! !$errors->has($label) ?: 'has-error' !!}">

    <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div id="{{$id}}" style="width: 100%; height: 100%;">
            <textarea name="body" id="editor" rows="3" placeholder="请填入至少三个字符的内容" required="required" class="form-control">{{ old($column, $value) }}</textarea>
        </div>
    </div>
</div>
