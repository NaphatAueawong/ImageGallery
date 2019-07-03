@extends('layouts.app')
@section('content')
    <div>
        <h3>Disk Usage Overview</h3>
        <p>Total size : {{$user->images()->sum('size')}}</p>
        <p>No of files : {{$user->images()->count()}}</p>
        <hr>

        <h3>Disk Usage Competition</h3>
        <p>JPEG File  :No of files {{$jpeg->count()}} :size {{$jpeg->sum('size')}}</p>
        <p>PNG File  :No of files {{$png->count()}} :size {{$png->sum('size')}}</p>
        <p>JPG File  :No of files {{$jpg->count()}} :size {{$jpg->sum('size')}}</p>
        <hr>
    </div>

@endsection