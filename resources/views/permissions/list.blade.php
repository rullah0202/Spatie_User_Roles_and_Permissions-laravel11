<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Permissions') }}
            </h2>
            @can('create permissions')
                <a href="{{ route('permissions.create') }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-3">Create</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message></x-message>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="60">#</th>
                        <th class="px-6 py-3 text-left" >Name</th>
                        <th class="px-6 py-3 text-left" width="180">Created</th>
                        <th class="px-6 py-3 text-center" width="180">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if($permissions -> IsNotEmpty())
                        @foreach($permissions as $permission)
                            <tr class="border-b">
                                <td class="px-6 py-3 text-left">{{ $permission->id }}</td>
                                <td class="px-6 py-3 text-left">{{ $permission->name }}</td>
                                <td class="px-6 py-3 text-left">
                                    {{ \Carbon\Carbon::parse($permission->created_at)->format('d M, Y')  }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                @can('edit permissions')
                                    <a href="{{ route('permissions.edit',$permission->id) }}" class="hover:bg-slate-600 bg-slate-700 text-sm rounded-md text-white px-3 py-3">Edit</a>
                                @endcan
                                @can('delete permissions')
                                    <a href="javascript:void(0)" onclick="deletePermission('{{$permission->id}}')" class="hover:bg-red-500 bg-red-600 text-sm rounded-md text-white px-3 py-3">Delete</a>
                                @endcan
                                </td>
                            </tr>  
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="my-3">
                {{ $permissions->links()}}
            </div>
        </div>
    </div>
    <x-slot name="script">
        <script type="text/javascript">
            function deletePermission($id){
                if(confirm('Are you sure to delete?')){
                    $.ajax({
                        url: '{{ route("permissions.destroy") }}',
                        type: 'delete',
                        data: {id:$id},
                        dataType: 'json',
                        headers : {
                            'x-csrf-token' : '{{ csrf_token() }}'
                        },
                        success: function(data){
                            if(data.status == true)
                            {
                                iziToast.success({
                                    title: '',
                                    position: 'topRight',
                                    message: data.success_message,
                                });
                            }
                            window.location.href = '{{ route("permissions.list") }}'
                        }
                    })
                }
            }
        </script>
    </x-slot>
</x-app-layout>
