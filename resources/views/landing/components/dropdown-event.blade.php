<section>
    <div class="events--item-status"></div>
    <a><span class="@if($subEvents) icon-play @endif"></span></a>
    {{ $title }}
    <div class="events--item-details">
        {{ $slot }}
    </div>
</section>