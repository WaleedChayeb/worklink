@if($type === 'generic')
<script type="application/ld+json">
    {
    "@context": "http://schema.org",
    "@type": "Organization",
    "name": "{{getSetting('site.name')}}",
    "url": "{{getSetting('site.app_url')}}",
    "address": ""{{(GenericHelper::getAvailableSocialNetworks() ? ',' : '')}}
        @if(GenericHelper::getAvailableSocialNetworks())
            "sameAs": {!! json_encode(GenericHelper::getAvailableSocialNetworks()) !!}
        @endif
    }
</script>
@endif
