{{-- Notification popup partial --}}
@if (session('error'))
<div id="notifPopup" class="notif-popup bg-red-500">
    <p>{{ session('error') }}</p>
</div>
@endif

@if (session('success'))
<div id="notifPopup" class="notif-popup bg-green-500">
    <p>{{ session('success') }}</p>
</div>
@endif

@if ($errors->any())
<div id="notifPopup" class="notif-popup bg-red-500">
    <p>Ada error dalam form:</p>
    <ul class="mt-2 ml-4">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<style>
    .notif-popup {
        position: fixed;
        top: 20px;
        right: 20px;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 1000;
        opacity: 1;
        transition: opacity 0.3s ease;
        max-width: 400px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .notif-popup ul {
        list-style-type: disc;
    }
    
    .notif-popup li {
        margin-bottom: 4px;
    }
</style>

<script>
    // Auto-hide notification popup
    setTimeout(function() {
        const popup = document.getElementById('notifPopup');
        if (popup) {
            popup.style.opacity = '0';
            setTimeout(() => popup.remove(), 300);
        }
    }, 5000);
</script>
