<x-app-layout>

    <div class="container mt-4">
        <div class="row">
            @if (count($users)>0)
            <div class="col-md-3">
                <ul class="list-group">
                    @foreach ($users as $user )
                    <li class="list-group-item cursor-pointer user-list">
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="user-pic">

                        <span class="ms-2">{{ $user->name }}</span>
                        <b><sup id="{{ $user->id }}-status" class="offlineStatus">Offline</sup></b>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-9">
                <h3 class="startHead text-white">
                    click start chat!
                </h3>
                <div class="chatSection">
                    <div id="chatContainer">
                        <div class="currentUser">
                            <h5>Hello</h5>
                        </div>
                        <div class="distantUser">
                            <h5>Hello back</h5>
                        </div>

                    </div>
                    <form action="" id="chatForm">
                        <input class="border" type="text" name="message" placeholder="Enter message.." id="message"
                            required>
                        <input type="submit" value="Send Message" class="btn btn-primary">
                    </form>
                </div>
            </div>
            @else
            <div class="col-md-12">
                <h4>Users not found</h4>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>