@extends('layouts.app')

@section('content')
<div class="container">
   <chats :user = "{{auth()->user()}}" />
</div>
@endsection
