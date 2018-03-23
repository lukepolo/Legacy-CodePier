#!/bin/bash
# $CERTBOT_VALIDATION
# $CERTBOT_TOKEN

curl -X POST https://auth.acme-dns.io/update \
    -H "X-Api-Key: e324dfae-48f4-425e-9795-f48380ca9674" \
    -H "X-Api-Key: 46bYuPj0tqIi6bOpZFCZxPABhShQJTLDfrIFQlga" \
    -d '{subdomain": "c91f16ff-a31e-4795-a36f-33714ce661c3", "txt": "$CERTBOT_VALIDATION"}'
    | python -m json.tool

    //        curl -s -X POST https://auth.acme-dns.io/register
    //        {
    //            "allowfrom": [],
    //    "fulldomain": "c91f16ff-a31e-4795-a36f-33714ce661c3.auth.acme-dns.io",
    //    "password": "46bYuPj0tqIi6bOpZFCZxPABhShQJTLDfrIFQlga",
    //    "subdomain": "c91f16ff-a31e-4795-a36f-33714ce661c3",
    //    "username": "e324dfae-48f4-425e-9795-f48380ca9674"
    //}