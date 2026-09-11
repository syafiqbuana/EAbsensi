<div
    x-data="clock()"
    x-init="start()"
>
    <div class="flex items-center flex-row gap-3">
        <div>
            <p class="text-sm font-medium">
                <span x-text="day"></span> ,
                <span x-text="date"></span>
            </p>
        </div>

        <div class="text-md font-bold tabular-nums" x-text="time"></div>
        
    </div>
</div>

@script
<script>
    Alpine.data('clock', () => ({
        day: '',
        date: '',
        time: '',
        interval: null,

        start() {
            this.update();

            this.interval = setInterval(() => {
                this.update();
            }, 1000);
        },

        update() {
            const now = new Date();

            this.day = now.toLocaleDateString('id-ID', {
                weekday: 'long'
            });

            this.date = now.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long'
            });

            this.time = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            });
        },

        destroy() {
            clearInterval(this.interval);
        }
    }));
</script>
@endscript