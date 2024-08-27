@extends('admin.layouts.app')
@section('title', 'Listagem dos Usuários')

@section('content')
    <h1>Usuários</h1>

    <a href="{{route('users.create')}}">Novo</a>

    <x-alert/>

    <table>
        <theadr>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Ações</th>
            </tr>            
        </theadr>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>
                        <a href="{{route('users.edit', $user->id)}}">Edit</a>
                        <a href="{{route('users.show', $user->id)}}">Detalhes</a>
                    </td>
                </tr>                    
            @empty
                <tr>
                    <td colspan="100">Nenhum usuário no banco</td>
                </tr>                    
            @endforelse
        </tbody>
    </table>

    {{$users->links()}}
@endsection