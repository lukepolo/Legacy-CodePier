<section>
    <div class="events--item-status events--item-status-neutral"></div>
    <a><span class="@if($subEvents) icon-play @endif"></span></a>
    {{ $title }}
    <div class="events--item-details @if(isset($hide)) hide @endif">
        {{ $slot }}
    </div>
</section>