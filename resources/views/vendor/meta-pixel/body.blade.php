@if($metaPixel->isEnabled())
@unless(empty($eventLayer))
<!-- Meta Pixel Events -->
<script>
@foreach($eventLayer as $eventName => $metaPixel)
@if(empty($metaPixel['event_id']) && empty($metaPixel['data']))
    fbq('track', '{{ $eventName }}');
@elseif(empty($metaPixel['event_id']))
    fbq('track', '{{ $eventName }}', @js($metaPixel['data']));
@else
    fbq('track', '{{ $eventName }}', @js($metaPixel['data']), {eventID: '{{ $metaPixel['event_id'] }}'});
@endif
@endforeach
</script>
<!-- End Meta Pixel Events -->
@endunless
@unless(empty($customEventLayer))
<!-- Meta Pixel Custom Events -->
<script>
@foreach($customEventLayer as $customEventName => $metaPixel)
@if(empty($metaPixel['event_id']) && empty($metaPixel['data']))
   fbq('trackCustom', '{{ $customEventName }}');
@elseif(empty($metaPixel['event_id']))
    fbq('trackCustom', '{{ $customEventName }}', @js($metaPixel['data']));
@else
   fbq('trackCustom', '{{ $customEventName }}', @js($metaPixel['data']), {eventID: '{{ $metaPixel['event_id'] }}'});
@endif
@endforeach
</script>
<!-- End Meta Custom Pixel Events -->
@endunless
@endif
