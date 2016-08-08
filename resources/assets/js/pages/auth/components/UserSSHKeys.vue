<template>
    <form>
        {!! Form::open(['action' => ['Auth\UserController@postAddSshKey', 1]]) !!}
        <div class="form-group">
            {!! Form::label('name') !!}
            <label>Name</label>
            <input name="name" type="text" class="form-control">
        </div>
        <div class="form-group">
            <label>Public Key0</label>
            <textarea name="ssh_key" class="form-control"></textarea>
        </div>
        <input type="submit" value="Install SSH Key">
    </form>

    <table class="table">
        <thead>
        <tr>
            <th>Key Name</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach(\Auth::user()->sshKeys as $sshKey)
        <tr>
            <td>{{ $sshKey->name }}</td>
            <td><a href="{{ action('Auth\UserController@getRemoveSshKey', $sshKey->id) }}" class="fa fa-remove"></a></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</template>