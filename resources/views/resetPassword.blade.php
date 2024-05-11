{{-- @if ($errors->any())

<ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
    
@endif --}}

<form  method="POST">
    @csrf
    <input type="hidden" name="id" value="{{$user->id}}">

    <input type="password" name="password" placeholder="New Password">
    @error('password')
    <p class="">{{$message}}</p>
    @enderror
    <br><br>
    <input type="password" name="password_confirmation" placeholder="Confirm Password">
    @error('password_confirmation')
    <p class="">{{$message}}</p>
    @enderror
    <br><br>
    <input type="submit" value="Submit">
</form>