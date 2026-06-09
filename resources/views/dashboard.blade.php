<x-app-layout>

    {{-- ── NOTIFICATION BELL (add anywhere visible in the nav area) ── --}}
    <div style="position:fixed; top:14px; right:20px; z-index:1000;">
        <button id="notifBtn" class="btn btn-light btn-sm position-relative" onclick="toggleNotifPanel()">
            🔔
            <span id="notifBadge"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                style="display:none; font-size:10px;">
                0
            </span>
        </button>

        <div id="notifPanel" class="card shadow" style="display:none; position:absolute; right:0; top:40px;
                    width:300px; max-height:380px; overflow-y:auto; z-index:999;">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <strong>Notifications</strong>
                <button class="btn btn-sm btn-outline-secondary" onclick="markAllRead()">Mark all read</button>
            </div>
            <ul id="notifList" class="list-group list-group-flush mb-0"></ul>
        </div>
    </div>
    {{-- ──────────────────────────────────────────────────────────── --}}

    <div class="container mt-4">
        <div class="row">
            @if (count($users)>0)
            <div class="col-md-3">
                <ul class="list-group">
                    @foreach ($users as $user )
                    <li class="list-group-item cursor-pointer user-list" data-id="{{ $user->id }}">
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="user-pic">

                        <span class="ms-2">{{ $user->name }}</span>
                        <b><sup id="{{ $user->id }}-status" class="offlineStatus">Offline</sup></b>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-9">
                <h3 class="startHead">
                    click start chat!
                </h3>
                <div class="chatSection">
                    <div id="chatContainer">
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