@if(auth()->check() && auth()->user()->hasRole('karyawan'))
<script>
(function() {
    const PING_INTERVAL_MS = 60000;
    const PING_URL = @json(route('location.ping'));
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());

    if (!navigator.geolocation) {
        console.warn('Geolocation tidak didukung browser ini.');
        return;
    }

    function sendLocation(position) {
        fetch(PING_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
            }),
        }).catch(function(err) {
            console.warn('Gagal mengirim lokasi:', err);
        });
    }

    function requestAndSend() {
        navigator.geolocation.getCurrentPosition(
            sendLocation,
            function(err) {
                console.warn('Gagal mendapatkan lokasi:', err.message);
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 30000 }
        );
    }

    requestAndSend();
    setInterval(requestAndSend, PING_INTERVAL_MS);
})();
</script>
@endif
